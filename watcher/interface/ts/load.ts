import {GameData} from '../../../js/loadgame';
import {Game} from './game'

export var gamepin;

export function setGamePin(newpin) {
    gamepin = newpin;
}   
 
var data : GameData;

export function loadGame() {
    document.getElementById("gameinput").style.display = "none";
    document.getElementById("gameinput_feedback").style.display = "none";
    document.getElementById("menu").style.display = "flex";
    document.getElementById("header_txt").innerText += " " + gamepin;
    fetchData();
}

function fetchData() {
    fetch("../../game/gamedata.php?game=" + gamepin)
    .then(res => {
        if (res.ok) return res.json();
    })
    .then(res => {
        if (res.success) {
            data = JSON.parse(res.data);
            console.log(data);
            let game = new Game(data);
            document.getElementById("content").innerHTML = game.render();
            populateDropDowns();
        }
    });
}

function populateDropDowns() {
    let terminals = document.getElementById("menu_terminals");
    let qgroups = document.getElementById("menu_qgroups");
    //@ts-ignore
    for (var terminal of data.terminals) {
        let a = document.createElement("a");
        a.innerHTML = terminal.id;
        terminals.appendChild(a);
    }
    for (var qgroup of data.qgroups) {
        let a = document.createElement("a");
        a.innerHTML = qgroup.substr(qgroup.lastIndexOf('/') + 1);
        qgroups.appendChild(a);
    }
}