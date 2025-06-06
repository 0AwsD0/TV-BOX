//make code that checks url and adds active box // highlight on button like in img
let menu_state = 1;
const menu = document.querySelector(".side-bar");
const content = document.querySelector("#content");

function menu_toggle(){
    if(menu_state == 1){
        menu.classList.add("side-bar-hide");
        content.classList.add("width-100");
        menu_state = 0;
    }
    else{
        menu.classList.remove("side-bar-hide");
        content.classList.remove("width-100");
        menu_state = 1;
    }
}

const preview = document.querySelector('#preview-window');
const previewImg = document.querySelector('#preview-img');

function media_tails_in(element){
    console.log(element.getAttribute("src"));
    previewImg.setAttribute("src", element.getAttribute("src"));
    preview.style.display = "block";
}

function media_tails_out(){
    preview.style.display = "none";
}