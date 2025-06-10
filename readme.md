# TV-BOX
Display images and videos in full screen window, from local directory. Configure server to manage your displays remotely.

## In the package
CLIENT - Electron.JS based application for usage on whatever system you choose

SERVER - PHP -> Requires any www server and database (built on Apache and MariaDB)

Full documentation for system included in SERVER -> /tv-box-doc dir

# Deploy

## How to deploy TV-BOX system
### First you need to deploy the 'admin panel' - www interface to manage devices.
- This requires www server and MySQL/MariaDB database
- Create database named 'tv_box' **(or choose any other name, app pulls DB from db_config.json)** and import '.sql' file located in root www repository folder
- Now you can delete default 'admin' user or replace password hash /-> if you deploy to the live server
- Copy paste files into your www server
- Edit pdo.php and enter valid credentials for your database
- Log in into default admin account (login: admin | pwd: admin) or if you deleted it -> INSERT new one you can generate SQL Query by going to `acc_gen.php`

- remember to set the PHP MAX POST and UPLOAD size UP - to for example 500MB if you wanna use upload on the www interface not the FTP only <- Because you can do it too.

### Deploy client side
- You can deploy the client in 2 different ways
- Directly install node.js and run app as node project
- Compile / get compiled version from repo and install the application
- Enter valid credentials into `db_config.json` and `remote_config.json` located in root directory.
- Edit and configure app in `app_config.json`
- If you installed program from the executable file, you may have to run it once before setup. Program will generate all required config files if they are not present in the program directory
- If you are testing/developing and you don't have database password set you can leave 'password' field empty->`""`

### Windows
- If you want to run program every time system starts:

        RUN -> (Windows+R)
        Type -> shell:startup
        Insert shortcut to the application in the opened folder

The instal directory for the app is:
                C:\Users\{User}\AppData\Local\Programs\tv-box_electron
The config/working directory for the app is:
                C:\Users\{User}\AppData\Roaming\TV-BOX-CLIENT

### Linux / Raspberry Pi
- If you want to run program every time system starts (installed version)
- This works in Raspberry PI and /should work on most Linux systems

Create and edit file:

        sudo nano /etc/xdg/autostart/tv-box.desktop

Inside the file:

        [Desktop Entry]
        Name=tv-box_electron
        Exec=tv-box_electron

SAVE file and Reboot to test it | Created file may need 'X' (Execute) permissions

The install directory for the app is:
                /opt/TV-BOX-CLIENT
The config directory for the app is:
                ~/.config/TV-BOX_CLIENT

- If you want to auto start the node version -> make .sh (shell) file with proper `npm start` command and execute it on system start

- Should work on most Linux systems
Navigate into $HOME/.config/autostart and create '.desktop' file, within the file:

        [Desktop Entry]
        Type=Application
        Exec="</path/to/script>"
        Hidden=false
        NoDisplay=false
        X-GNOME-Autostart-enabled=true
        Name=Startup Script

#### Node install

[https://azukidigital.com/blog/2019/electron-application-on-raspberry-pi/](https://azukidigital.com/blog/2019/electron-application-on-raspberry-pi/)

Download node:
[https://nodejs.org/en/download/](https://nodejs.org/en/download/)

Unpack Node, go into its folder -> cd node

        sudo cp -R bin/* /usr/local/bin/
        sudo cp -R include/* /usr/local/include/
        sudo cp -R lib/* /usr/local/lib/
        sudo cp -R share/* /usr/local/share/

        npm install --save-dev electron

The install directory for the app is:
                /opt/TV-BOX-CLIENT
The config/working directory for the app is:
                ~/.config/TV-BOX-CLIENT

## Rest of the documentation can be accessed in www interface
