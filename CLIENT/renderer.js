const path = require('path');
const fs = require('fs');
const { ipcRenderer } = require('electron');

//Declare globals
let execDir, execPath;
let json_iterator, parsed_json, iterator = 0;

//DOM elements
const mediaContainer = document.querySelector('#mediaContainer');
const videoElement = document.querySelector('video');
const imageElement = document.querySelector('#displayImage');
mediaContainer.style.opacity = 1;

let userDataPath;

//Init the app
init();

async function init() {
  userDataPath = await ipcRenderer.invoke('get-user-dir');
  execPath = await ipcRenderer.invoke('get-root-dir');
  execDir = path.dirname(userDataPath);
  console.log('Executable path:', userDataPath);
  console.log('Executable directory:', userDataPath);
  //Executable path: X:\TVBOX_CLIENT\tv-box_electron
  //Executable directory: X:\TVBOX_CLIENT

  watchJsonChanges();
  displayNext(); // Start display loop
}

function waitForFileStability(filePath, retries = 5, delayMs = 200) {
  return new Promise((resolve, reject) => {
    let lastSize = -1;
    let attempts = 0;

    const check = () => {
      if (!fs.existsSync(filePath)) return resolve("File does not exist");

      const stats = fs.statSync(filePath);
      if (stats.size === lastSize) return resolve();
      lastSize = stats.size;

      if (++attempts >= retries) return reject(new Error("File not stable after retries"));
      setTimeout(check, delayMs);
    };

    check();
  });
}

function json() {
  return new Promise((resolve, reject) => {
    const jsonPath = path.join(userDataPath, 'config.json');

    waitForFileStability(jsonPath, 5, 200)
      .then(() => {
        fs.readFile(jsonPath, 'utf-8', (err, data) => {
          if (err) return reject(err);
          try {
            parsed_json = JSON.parse(data);
            json_iterator = parsed_json.length;
            resolve("JSON loaded");
          } catch (e) {
            reject(e);
          }
        });
      })
      .catch(err => reject(err));
  });
}

async function displayNext() {
  await json();

  if (!parsed_json[iterator]) {
    console.log("Invalid entry at iterator", iterator);
    iterator = 0;
  }

  const currentItem = parsed_json[iterator]; //I fell like it's let not const XD but it works, don't touch!
  console.log("Current item type:", currentItem.type);

  if (currentItem.type === "image") {
    await showImage(currentItem);
  } else if (currentItem.type === "video") {
    await showVideo(currentItem);
  } else {
    console.warn("Unknown type in config.json:", currentItem.type);
  }

  iterator++;
  checkIterator();

  setTimeout(displayNext, currentItem.timer * 1000);
}

function checkIterator() {
  if (iterator >= json_iterator) {
    iterator = 0;
  }
}

function showImage(item) {
  return new Promise((resolve) => {
    const mediaSrc = path.join(userDataPath, 'media', item.name);
    imageElement.setAttribute('src', mediaSrc);
    imageElement.style.opacity = 1;
    videoElement.style.opacity = 0;
    console.log('Image loaded:', mediaSrc);
    resolve();
  });
}

function showVideo(item) {
  return new Promise((resolve) => {
    const mediaSrc = path.join(userDataPath, 'media', item.name);
    videoElement.setAttribute('src', mediaSrc);
    videoElement.load();
    videoElement.play();
    imageElement.style.opacity = 0;
    videoElement.style.opacity = 1;
    console.log('Video playing:', mediaSrc);
    resolve();
  });
}

function watchJsonChanges() {
  const jsonDir = userDataPath;
  let reloadTimer = null;

  fs.watch(jsonDir, (eventType, filename) => {
    if (filename === 'config.json' && (eventType === 'change' || eventType === 'rename')) {
      clearTimeout(reloadTimer);
      reloadTimer = setTimeout(() => {
        console.log(`config.json ${eventType} detected – reloading…`);
        json()
          .then(() => console.log('Reloaded config:', parsed_json))
          .catch(err => {
            console.error('Failed to reload config.json:', err);
            setTimeout(() => {
              json().catch(e => console.error('Second reload failed:', e));
            }, 1000);
          });
      }, 500);
    }
  });
}