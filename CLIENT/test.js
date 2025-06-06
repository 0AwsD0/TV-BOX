const fs = require('fs-extra');
const path = require('path');
downloadErrors = 0;

function cleanAndMove(sourceDir, destinationDir){
    return new Promise((resolve,reject) =>{
        console.log("cleanAndMove()::Invoked:DirPath: SRC:"+sourceDir+" | DEST: "+destinationDir);
        console.log("cleanAndMove():downloadErrors: "+downloadErrors);
        //if no errors move files as new baseline
        if(downloadErrors == 0){
            downloadErrors = 0;
            setTimeout(function(){ //idk if  all that timeouts are necessary - just to be sure its not fucked by async exec.
                //del files fs
                fs.emptyDirSync(destinationDir);
            }, 100)
            setTimeout(function(){
                //move files fs
                fs.readdirSync(sourceDir).forEach(f => fs.move(sourceDir+`/${f}`, destinationDir+`/${f}`));
            }, 200)
            setTimeout(function(){
                //'reload' page
                mainWindow.loadFile(path.join(__dirname, 'index.html'));
            }, 500)
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

let cnm = cleanAndMove("test2", "test3");
console.log(cnm)