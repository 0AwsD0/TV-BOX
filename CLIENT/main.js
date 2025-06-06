const path = require('path');
const { app, BrowserWindow, ipcMain } = require('electron');
const userDataPath = app.getPath('userData');

const fs = require('fs-extra');
const { execSync } = require('child_process');
const moment = require('moment');
const mysql = require('mysql');
let con;
let dbCon;
let local_deviceName;
let local_loopTime = 3000000; //default - 3000000 -> 10 min
let local_dbRetry = 120000; //default-> 2 minutes
let local_timestamp;
let db_timestamp;

let baseUrl ='http://localhost/TVBOX/media/';
let baseConfigUrl = 'http://localhost/TVBOX/cfg/';

let downloadErrors = 0;

//Creating cfg files etc. directly in app folder (so it's not packed into app.asar in build versions)
// SECTION 'Anti ASAR'
/*Fixed in some part by package.json But leaved here to be extra safe
     "extraFiles": [
      "bg-logo.json",
      "config.json",
      "app_config.json",
      "db_config.json"
    ]
*/


// -> sendNotification('ERROR','Could not load remote_config.json!','Remote configuration file  (remote_config.json) can be incomplete or corrupted. Please delete it for default configuration or enter custom data after default file creation. // Sent by: getRemoteConfig() // Error msg: '+err.message);
function sendNotification(type,title,content){
        let insertQuery = "INSERT INTO `notifications` (`type`, `title`, `content`) VALUES ('"+type+"', '["+type+"] ["+local_deviceName+"] "+title+" {"+getTimestamp()+"}', '"+content+"');";
        try{
            con.query(insertQuery, function (err) {
                if(err)
                {
                    console.log('sendNotification()::Query (INSERT):'+err);
                }
            });
        }
        catch{
            console.warn('sendNotification()::ERROR-try-catch');
        }
}

function createFile(file, data){ //data is optional if defaults not needed
    fs.access(file, (err) => {
        if (err) {
            console.log("createFile:: "+file+" does not exist");
            fs.writeFileSync(file, data);
          } else {
            console.log("createFile:: "+file+" exists");
          }
      })
}

createFile(path.join(userDataPath, "db_config.json"),'{"address": "localhost","name": "tv_box","user": "root","password": ""}')
createFile(path.join(userDataPath, "remote_config.json"),'{"baseUrl": "http://localhost/TVBOX/media/","baseConfigUrl": "http://localhost/TVBOX/cfg/"}')
createFile(path.join(userDataPath, "config.json"), '[{"type": "image","name": "1.jpg", "timer": 5},{"type": "image","name": "2.jpg", "timer": 5},{"type": "video","name": "1.mp4", "timer": 10}]');
createFile(path.join(userDataPath, "app_config.json"), '{"deviceName":"testDev","loopTime": 30000,"configTimestamp":"1970-01-01 00:00:00"}, "dbRetry":10000');

//Write default db config if file does not exist - should invoke on first run of the app
//wrote function above to do it
// function defaultDb(){
//     if(fs.existsSync(path.join(userDataPath, "db_config.json")) == false){
//         data = '{"address": "localhost","user": "root","password": ""}';
//         fs.writeFileSync(path.join(userDataPath, "db_config.json"),data)
//     }
// }
// defaultDb();

//Must be above LOGs Section
function assureFolderExistence(file,folder){
        //Example strings below
        //assureFile = './log/test.txt';
        // assureFolder = './log';
        fs.ensureFile(file, err => {
            console.log('createLogFolder()::Errors: '+err); // => null if no errors
        });
        if (!fs.existsSync(folder)){
            fs.mkdirSync(folder);
        }
}

assureFolderExistence(path.join(userDataPath, "log/test.txt"),path.join(userDataPath, "log"));
assureFolderExistence(path.join(userDataPath, "temp/test.txt"),path.join(userDataPath, "temp"));
assureFolderExistence(path.join(userDataPath, "media/test.txt"),path.join(userDataPath, "media"));
// SECTION END 'Anti ASAR'

//SECTION LOGs
let currentDate = getDateString();
let logFile = createLogStream(currentDate);

// Format: "2025-05-12" for the next day next file creation
function getDateString() {
    return new Date().toISOString().split('T')[0];
}

//'Standard' Timestamp for the logs
function getTimestamp() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

// Create a new write stream for the given date
function createLogStream(date) {
    const filePath = path.join(userDataPath, 'log', `${date}.log`);
    return fs.createWriteStream(filePath, { flags: 'a' });
}

// Rotate log if the date has changed
function rotateLogIfNeeded() {
    const newDate = getDateString();
    if (newDate !== currentDate) {
        logFile.end(); // close old file
        currentDate = newDate;
        logFile = createLogStream(currentDate);
    }
}

// Override console methods
['log', 'info', 'warn', 'error'].forEach(method => {
    const original = console[method].bind(console);

    console[method] = (...args) => {
        rotateLogIfNeeded(); // Check if log file needs to rotate

        const timestamp = getTimestamp(); // e.g. 2025-05-12 15:33:22
        const message = `[${timestamp}] [${method.toUpperCase()}] ${args.join(' ')}`;

        original(message);           // Print timestamped log to terminal
        logFile.write(message + '\n'); // Save to file
    };
});
//END SECTION LOGS

let dbAddr = '';
let dbName = '';
let dbUser = '';
let dbPwd = '';

function getDbConfig(){
return new Promise((resolve, reject) => {
    try{
        let dbCfg = JSON.parse(fs.readFileSync(path.join(userDataPath,'db_config.json'), 'utf8'));
        if (!dbCfg.address || !dbCfg.user || !dbCfg.name) { //password can be empty for instance if it runs locally you get by default user 'root' pwd ''
            console.debug("DB Config contents: ", dbCfg);
            throw new Error("Incomplete DB config.");
            reject("/DB CONFIG ERROR!!!");
        }
        dbAddr = dbCfg.address;
        dbName = dbCfg.name;
        dbUser = dbCfg.user;
        dbPwd = dbCfg.password;
        console.log("getDbConfig()::Addr: "+dbAddr);
        console.log("getDbConfig()::Name: "+dbName);
        console.log("getDbConfig()::User: "+dbUser);
        console.log("getDbConfig()::Pwd: "+dbPwd);

        resolve("Got the DB config."); //"If an error happens inside try, you never call resolve, and the Promise might hang. Instead, move resolve() into the try block."
    }catch(err){
        reject(err+" ///DB CONFIG ERROR!!!");
    }
});
}

function getRemoteConfig(){
    return new Promise((resolve, reject) => {
        try{
            let remoteCfg = JSON.parse(fs.readFileSync(path.join(userDataPath,'remote_config.json'), 'utf8'));
            if (!remoteCfg.baseUrl || !remoteCfg.baseConfigUrl) {
                throw new Error("Incomplete Remote config.");
            }
            baseUrl = remoteCfg.baseUrl;
            baseConfigUrl = remoteCfg.baseConfigUrl;
            console.log("getRemoteConfig()::baseUrl: "+baseUrl);
            console.log("getRemoteConfig()::baseConfigUrl: "+baseConfigUrl);
            console.log("getDbConfig()::Pwd: "+dbPwd);
            resolve("Got the DB config."); //"If an error happens inside try, you never call resolve, and the Promise might hang. Instead, move resolve() into the try block."
        }catch(err){
            sendNotification('ERROR','Could not load remote_config.json!','Remote configuration file  (remote_config.json) can be incomplete or corrupted. Please delete it for default configuration or enter custom data after default file creation. // Sent by: getRemoteConfig() // Error msg: '+err.message);
            reject(err+" ///REMOTE SERVER CONFIG ERROR!!!");
        }
    });
}

// START INIT
async function initialize(){

    let dbCfg = await getDbConfig();
    console.log("initialize()::getDbConfig(): "+dbCfg);

    let remoteCfg = await getRemoteConfig();
    console.log("initialize()::getRemoteConfig(): "+remoteCfg);

    let localConfig = await getApplicationConfig();
    console.log("initialize()::getApplicationConfig(): "+localConfig);

    let folders = await folderCheck();
    console.log("initialize()::folderCheck(): "+folders);
    //let fileWrite = await writeFileTest();
    //console.log(fileWrite);

    let connection = await checkInternetConnection();
    console.log("initialize()::checkInternetConnection(): "+connection);

    if(connection == "Connected"){
        handleDatabaseConnection();
        console.log("initialize()::dbCon:offline: "+db_offline);
        if(dbCon == "Connected to database!"){
            configDownload = await getConfig();
            //removed update local cfg timestamp since download is invoked below anyway and migrates control to main app loop
            deviceAlivePing = await dbPing();
            console.log("deviceAlivePing()::"+deviceAlivePing);
            console.log("initialize():: "+configDownload);
        }
        //This is outside of IF above - coz DB may be dead, but files can be downloaded if server is up and it makes program download files on startup
        let download = await fileDownload(path.join(userDataPath,'config.json'), path.join(userDataPath,'media/'));
        console.log('INIT::fileDownload():promise: '+download);
    }

    //fs - read to check if exists -> files according to config json if no try download that one again and check FS>? if not throw error + db info record add if not
    //OR DON'T CHECK IT FILE CAN BER SAME BUT HAVE 2 BYTES DIFFERENCE

    //FOR dbPings AND UPDATES -> CONFIG, FILES
    appLoop();
}
// END INIT

function getApplicationConfig(){
    return new Promise((resolve,reject) =>{
        try{
            fs.readFile(path.join(userDataPath,'./app_config.json'), 'utf8', (err, data) => {
                if (err) {
                    console.error("getApplicationConfig()::fs: "+err);
                }
                console.log("getApplicationConfig()::fs: "+data);
                local_json = data;
                let local_parsed_json = JSON.parse(local_json);
                local_deviceName = local_parsed_json.deviceName;
                local_loopTime = local_parsed_json.loopTime;
                local_dbRetry = local_parsed_json.dbRetry;
                    if (typeof local_loopTime !== 'number' || Number.isNaN(local_loopTime) || local_loopTime < 10000) {
                        local_loopTime = 15000;
                    }
                    if (typeof local_dbRetry !== 'number' || Number.isNaN(local_dbRetry) || local_dbRetry < 10000) {
                        local_dbRetry = 120000;
                    }
                local_timestamp = local_parsed_json.configTimestamp;
                console.log("Device name from JSON: "+local_parsed_json.deviceName);
                console.log("App Loop Timer from JSON: "+local_parsed_json.loopTime);
                console.log("DB-Retry Timer from JSON: "+local_parsed_json.dbRetry);
                console.log("Timestamp from JSON: "+local_parsed_json.configTimestamp);
                resolve("APP Config - device name: "+local_deviceName+" | config timestamp: "+local_timestamp);
            });
        }
        catch(err){
            sendNotification('ERROR','Could not load app_config.json!','Application configuration file (app_config.json) can be incomplete or corrupted. Please delete it for default configuration or enter custom data after default file creation. // Sent by: getApplicationConfig() // Error msg: '+err.message);
            resolve("getApplicationConfig()::"+err.message);
        }
    });

}

// f() to update status of the device in www interface
function dbPing(){
    return new Promise((resolve,reject) =>{

        let current_timestamp = moment().format("YYYY-MM-DD HH:mm:ss");

        console.log('dbPing()::Current timestamp: '+current_timestamp);

        let pingQueryCheck = 'SELECT * FROM devices WHERE name = "'+local_deviceName+'";';
        let result;
        let fields;

        con.query(pingQueryCheck, function (err, result, fields) {
            if (err)
            {
                resolve('dbPing()::'+err);
            }
            console.log('dbPing()::First Query:'+result);

            let insertQuery = "INSERT INTO devices(id, name, last_seen, channel) VALUES(NULL, '"+local_deviceName+"', '"+current_timestamp+"', NULL)";

        try{//this try catch is for DB conn fail
            if(result.length === 0){ //0 rows than query insert
                con.query(insertQuery, function (err, result, fields) {
                    if (err)
                    {
                        console.log('dbPing()::Second Query (INSERT):'+err);
                        resolve('dbPing()::'+err);
                    }
                    console.log('dbPing()::Second Query (INSERT):'+result);
                });
            }
            else{//row exist query update
                let updateQuery = "UPDATE devices SET last_seen = '"+current_timestamp+"' WHERE name='"+local_deviceName+"';";
                con.query(updateQuery, function (err, result, fields) {
                    if (err)
                    {
                        console.log('dbPing()::Second Query (UPDATE):'+err);
                        resolve('dbPing()::'+err);
                    }
                    console.log('dbPing()::Second Query (UPDATE):'+result);
                });
            }
        }catch(err){
            console.log("dbPing()::result.length ERROR");
        }

        });
        resolve(result);
    });
}


let select_q;

//cfg - what to display and how long
function getConfig() {
    return new Promise((resolve, reject) => {
        const configDateQuery = `SELECT channels.configuration_date FROM channels WHERE channels.id = (SELECT devices.channel FROM devices WHERE devices.name = "${local_deviceName}");`;

        con.query(configDateQuery, function (err, result) {
            if (err) {
                console.log('dbConfig()::Query (SELECT):' + err);
                return resolve('dbConfig()::' + err);
            }

            select_q = result[0]?.configuration_date;
            if (!select_q) return resolve("dbConfig()::No configuration_date found");

            console.log("dbConfig()::TIMESTAMP INSIDES: " + select_q + ' | ' + local_timestamp);

            if (select_q > local_timestamp) {
                console.log("dbConfig()::Config on the server is newer.");

                const cfg_query1 = `SELECT channels.name FROM channels WHERE channels.id = (SELECT devices.channel FROM devices WHERE devices.name = "${local_deviceName}");`;

                con.query(cfg_query1, function (err, result) {
                    if (err) {
                        console.log('getConfig()::CFG1:', err);
                        return resolve('getConfig()::' + err);
                    }

                    const channel_name = result[0]?.name;
                    if (!channel_name) return resolve('getConfig()::No channel_name found.');

                    console.log('getConfig()::CFG1: channel_name:', channel_name);

                    const cfg_query2 = `SELECT channels_config.file_name, channels_config.duration FROM channels_config WHERE channels_config.channel_name = "${channel_name}";`;

                    con.query(cfg_query2, function (err, result) {
                        if (err) {
                            console.log('getConfig()::CFG2:', err);
                            return resolve('getConfig()::' + err);
                        }

                        // Transform data
                        function getTypeFromExtension(filename) {
                            const ext = filename.split('.').pop().toLowerCase();
                            if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(ext)) return 'image';
                            if (['mp4', 'avi', 'mov', 'webm'].includes(ext)) return 'video';
                            return 'unknown';
                        }

                        const transformed = result.map(item => ({
                            type: getTypeFromExtension(item.file_name),
                            name: item.file_name,
                            timer: item.duration
                        }));

                        const jsonData = JSON.stringify(transformed, null, 2);
                        const configPath = path.join(userDataPath, 'temp/config.json');

                        fs.writeFile(configPath, jsonData, 'utf8', (err) => {
                            if (err) {
                                console.error('getConfig()::Error writing config.json:', err);
                                return resolve('getConfig()::File write error');
                            }

                            console.log('getConfig()::Successfully wrote config.json to:', configPath);
                            old_timestamp = local_timestamp;
                            db_timestamp = select_q;
                            return resolve('Config on the server is newer.');
                        });
                    });
                });
            } else {
                old_timestamp = local_timestamp;
                db_timestamp = local_timestamp;
                return resolve('Config on the server is not newer.');
            }
        });
    });
}

// //This works and is catching ->  -------- DB LISTENER -------- caught this error: Error: read ECONNRESET //but what after this? -> appLoop()::dbCon: RESOLVE::databaseCon()::Cannot enqueue Handshake after fatal error. // only app reset?
// con.on('error', function (err) {
//     console.log(' -------- DB LISTENER -------- caught this error: ' + err.toString());
//     db_offline = 1;

//     //add blinking database icon to the corner of the screen device to show that database error happened + try sending file to the server
//     //put the name od the device ein txt file and put to dir -> /errors in server -> DB CONNECTION LOST //works coz internet is up since SB is not pinged otherwise
// });

//DB redone
let db_offline;
//Auto detection of DB conn fail and auto retry on set interval
function handleDatabaseConnection() {
    con = mysql.createConnection({
        host: dbAddr,
        user: dbUser,
        password: dbPwd,
        database: dbName
    });

    con.connect((err) => {
        if (err) {
            console.error("handleDatabaseConnection(): Error connecting to DB:", err.message);
            setTimeout(handleDatabaseConnection, local_dbRetry);
        } else {
            console.log("handleDatabaseConnection(): Connected to MySQL");
            db_offline = 0;
        }
    });

    con.on('error', (err) => {
        console.error("handleDatabaseConnection(): MySQL error", err);
        if (err.code === 'PROTOCOL_CONNECTION_LOST' || err.fatal) {
            db_offline = 1;
            //if internet is up put file on the server
            handleDatabaseConnection(); // Reconnect
        } else {
            throw err;
        }
    });
}

function checkInternetConnection(){
    return new Promise((resolve,reject) =>{
        //"fool proof" (kinda) since you can have physical connection but no internet - DNS query with connected media but no internet would be hard
        require('dns').resolve('www.google.com', function(err) {
            if (err) {
                console.error('checkInternetConnection()::ERROR');
                resolve("No internet connection!");
            } else {
               resolve("Connected");
            }
          });
        });
}

function downloadFileSync(url, destination) {
    try {
        execSync(`curl -o "${destination}" "${url}" --retry-all-errors --silent --show-error --fail`);
        console.log(`downloadFileSync::File downloaded successfully: ${destination}`);

        // Add delay to allow OS to flush writes (not ideal but works)
        const waitUntil = Date.now() + 500; // wait 500ms
        while (Date.now() < waitUntil) {}
    } catch (error) {
        console.error(`downloadFileSync::Error downloading file ${destination}:`, error.message);
        downloadErrors = 1;
        if (fs.existsSync(destination)) {
            fs.unlinkSync(destination);
        }
        console.log("downloadFileSync::ERROR: ",error);
        //throw error; uncomment to show the message box - useful for testing internet connection stability
    }
}

function fileDownload(config, directory){
    return new Promise((resolve,reject) =>{

        try{
            //'config.json' or 'temp/config.json' - if download doesn't go all the way through - raise flag and don't invoke copy paste into /media/ -> instead of roll back to old config just don't overwrite it and use it
            let jsonRaw = fs.readFileSync(path.join(config), 'utf-8');
            let parsed_json = JSON.parse(jsonRaw);
            let json_iterator = parsed_json.length;
            let iterator = 0;

            console.log("fileDownload::json: " + JSON.stringify(parsed_json));

            //const baseUrl ='http://localhost/tv-box/';
            setTimeout(() =>{
                while(iterator < json_iterator){
                    console.log('fileDownload()::iterator: '+iterator)
                    console.log('fileDownload()::json_iterator: '+json_iterator)
                    //foreach or fx. while loop to iterate over all files from config json file
                    let fileName = parsed_json[iterator].name;
                    let currentFile = baseUrl+fileName;
                    const destination = directory+fileName;
                    console.log("fileDownload::Destination: "+destination);
                    downloadFileSync(currentFile, destination);
                    //loop end here after completion -> resolve
                    iterator++;
                }
                resolve("Download: Function executed.");
            }, 500);
        }
        catch(err){
            console.log("fileDownload()::JSON FAIL! - "+err);
            resolve("RESOLVE fileDownload()::JSON FAIL! - "+err);
        }
    });
}

function folderCheck(){
    return new Promise((resolve,reject) =>{
        //ensure file is there /> creates folder and file if not
        const file = path.join(userDataPath,'temp/test.txt');
        const file2 = path.join(userDataPath,'log/test.txt');
        const mediaFolder = path.join(userDataPath,'media');

        fs.ensureFile(file, err => {
            console.log('folderCheck()::Errors: '+err); // => null if no errors
        });

        fs.ensureFile(file2, err => {
            console.log('folderCheck()::Errors: '+err); // => null if no errors
        });

        if (!fs.existsSync(mediaFolder)){
            fs.mkdirSync(mediaFolder);
        }

        //timeout to let system create folders and prevent trying to write and download to nonexisting place
        setTimeout(() => {resolve("Ensured!")}, 1000);

    });
}


// function writeFileTest(){
//     return new Promise((resolve,reject) =>{
//         fs.appendFile('./temp/info.txt', 'This is test! \n', (err) => {
//             if (err) {
//                 console.log(err);
//             }
//             else {
//                 // Get the file contents after the append operation
//                 console.log("\nFile Contents of file after append:",
//                     fs.readFileSync("./temp/info.txt", "utf8"));
//             }
//         });
//         resolve("Writing into file: Done!");
//     });
// }

function createMainWindow(){
    const mainWindow = new BrowserWindow({
        title: "TV-BOX",
        fullscreen: true,
        webPreferences:{
            //preload: path.join(__dirname, 'preload.js'),
            nodeIntegration: true,
            contextIsolation: false //can't use this and preload in the same time / it allows renderer to use node directly
        }
    });
    //mainWindow.loadURL("https://motherfuckingwebsite.com/") For test

    mainWindow.loadFile(path.join(__dirname, 'index.html'));
}

//Async! -> called in::function getConfig()
let configDownload = '';
function localTimestampUpdate(select_q){
        try{
            //read file and make object
            let local_json = JSON.parse(fs.readFileSync(path.join(userDataPath,'app_config.json')), 'utf8');
            //edit or add property
            console.log("localTimestampUpdate::Read JSON: ", local_json);
            console.log("localTimestampUpdate::Updating 'configTimestamp' to:", select_q);
            local_json.configTimestamp = select_q;
            //write file
            fs.writeFileSync(path.join(userDataPath,'app_config.json'), JSON.stringify(local_json));
            console.log("localTimestampUpdate::Local timestamp updated");
        }catch(err){
            sendNotification('ERROR','Could not update local timestamp!','Device could not write/errored out of -> // Sent by: localTimestampUpdate() // Error msg: '+err.message);
            console.log("localTimestampUpdate()::Local timestamp NOT updated!: "+err.message);
        }

}

//readdirSync(dir).forEach(f => rmSync(`${dir}/${f}`));
function cleanAndMove(sourceDir, destinationDir){
    return new Promise((resolve,reject) =>{
        console.log("cleanAndMove()::Invoked:DirPath: SRC:"+sourceDir+" | DEST: "+destinationDir);
        console.log("cleanAndMove():downloadErrors: "+downloadErrors);
        //if no errors move files as new baseline
        if(downloadErrors == 0){
            downloadErrors = 0;
                //del files fs
                setTimeout(() =>{
                    try{
                        fs.emptyDirSync(destinationDir);
                    }
                    catch{
                        console.log("cleanAndMove():emptyDirSync - mp4 in use (deletion fail)");
                    }
                }, 100);

                //move files fs
                setTimeout(() =>{
                fs.readdirSync(sourceDir).forEach(f => fs.move(sourceDir+`/${f}`, destinationDir+`/${f}`));
                }, 200);
                //'reload' page
                setTimeout(() =>{
                    fs.unlink(path.join(userDataPath,'config.json'));
                    fs.move(path.join(userDataPath,'media/config.json'), path.join(userDataPath,'config.json')); //for config replace
            }, 500);
            resolve("Resolve:cleanAndMove:: FILES MOVED");
        }
        else{
            //old_timestamp can be useful here
            console.log("cleanAndMove:: One or more errors occurred - files not moved");
            downloadErrors = 0;//reset to 0 every time coz function setting it to 1 doesn't set it to 0 if success. Also that function always invokes first before this one - so if fails sets it to 1 if not it stays 0 so this expression just overwrites 0 to 0 again.
            resolve("Resolve:cleanAndMove:: NOT MOVED");
        }
    });
}

async function appLoop() {
    try {
        console.log("appLoop()::##################################################");
        let localConfig = await getApplicationConfig();
        console.log("appLoop()::localConfig: " + localConfig);

        //TEST - uncomment to make sure notification are working
        //sendNotification('ERROR','TTTTTTEEEEEEESSSSSSSTTTTTT Could not load remote_config.json!','Remote configuration file  (remote_config.json) can be incomplete or corrupted. Please delete it for default configuration or enter custom data after default file creation. // Sent by: getRemoteConfig() // Error msg: ');
        //sendNotification('INFO','TTTTTTEEEEEEESSSSSSSTTTTTT Could not load remote_config.json!','Remote configuration file  (remote_config.json) can be incomplete or corrupted. Please delete it for default configuration or enter custom data after default file creation. // Sent by: getRemoteConfig() // Error msg: ');
        //sendNotification('WARNING','TTTTTTEEEEEEESSSSSSSTTTTTT Could not load remote_config.json!','Remote configuration file  (remote_config.json) can be incomplete or corrupted. Please delete it for default configuration or enter custom data after default file creation. // Sent by: getRemoteConfig() // Error msg: ');

        configDownload = '';
        let connection = await checkInternetConnection();
        console.log("appLoop()::Internet Status: " + connection);

        if (connection === "Connected") {
            if (con && con.state === 'authenticated') {
                let deviceAlivePing = await dbPing();
                console.log("appLoop()::deviceAlivePing: " + deviceAlivePing);

                let getCfg = await getConfig();
                console.log("appLoop()::getConfig result: " + getCfg);

                if (getCfg === "Config on the server is newer.") {
                    let download = await fileDownload(path.join(userDataPath,'temp/config.json'), path.join(userDataPath,'temp/'));
                    console.log("appLoop()::Download: " + download);

                    let move = await cleanAndMove(path.join(userDataPath,"temp"), path.join(userDataPath,"media"));
                    console.log("appLoop()::cleanAndMove:" + move);

                    if(download === "Download: Function executed."){ //will not update timestamp on fail and on next loop app will try to download again
                        localTimestampUpdate(select_q);
                    }

                    // Optional: Reload browser window if needed
                }
            } else {
                console.warn("appLoop()::MySQL not connected, attempting to reconnect.");
                handleDatabaseConnection();
            }
        }

    } catch (error) {
        console.error("appLoop()::Error:", error);
    }

    //Loop again after timeout without stacking
    setTimeout(appLoop, local_loopTime); //setTimeout(appLoop, 10000); -> 10 seconds /-> 3000 000 -> 5 minutes
};

//for path of main  exe file -> to renderer
ipcMain.handle('get-root-dir', () => {
    const isPackaged = app.isPackaged;
    return isPackaged
      ? path.dirname(process.execPath)   //folder where .exe is
      : app.getAppPath();                //project folder in dev
  });

ipcMain.handle('get-user-dir', () => {
    return userDataPath;
  });


app.whenReady().then(
() =>{
    createMainWindow();
    //Starts up all things - file creation / download etc.
    initialize();
});

//using '__dirname' is bad idea use './' instead coz the dirname can return -> fileDownload()::JSON FAIL! - Error: ENOENT, temp\config.json not found in C:\Users\{User}\AppData\Local\Programs\tv-box_electron\resources\app.asar | Expected -> main program folder


//If you are on Windows -> build in WSL / docker / VM for linux ->     "dist": "electron-builder build --linux deb --arm64" // for windows just run -> "dist": "electron-builder build" it defaults to current platform