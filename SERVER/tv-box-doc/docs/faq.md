# FAQ

## FAQ
#### Can you assign all devices to one channel?

Yes.

#### Can you assign device to more than one channel?

No.

#### Can you use same name for more than one device?

Yes, but it's not recommended. You won't know if one of the devices went offline, since other opens will still ping to the DB and the www panel will show only one device w name that's used -> one row that. i s updated by many devices.

#### Can you change interval of device 'ping' timer?

Yes, the configuration let's you change appLoop timer and DB reconnection timer. If you increase Loop Time - remember to edit web panel too. It displays different status color depending on device last dbPing() -> timestamp in database. I recommend to make status "yellow" after 2 pings didn't come through. If you ping database every 3 minutes - maybe set status "RED" after some bigger amount of time than 4, 6 pings. The app won't ping database while downloading files, so when network is slow or there are many files to download devices may show up as yellow or even red on the www panel.

#### Where can I deploy the app?

In case off backend (management panel) - most basics hostings that have option to create at least one database are enough. Form the clients - since it's Electron.js app, it can be deployed ANYWHERE. Just compile it to target operating system or not - you can install node js and just npm run it!


#### Where can I get in contact with the developer?

Here: [Github](https://github.com/0AwsD0) | [LinkedIn](https://www.linkedin.com/in/wojciech-dudek-8a6561240/)

