///<reference path="../../../js/loadgame.d.ts"/>

import {GameData, GroupData} from '../../../js/loadgame';
import {Game} from './game.js'

export var gamepin = "";

export function setGamePin(newpin) {
    gamepin = newpin;
}   
 
var data : GameData;
var dataprov : Game;

var groupsarray : Array<GroupData> = Array();

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
            dataprov = new Game(data);
            document.getElementById("content").innerHTML = dataprov.render();
            fetch("../../game/getgroups.php?game=" + gamepin)
            .then(res => {
                if (res.ok) return res.json();
            })
            .then(res => {
                let keys = Object.keys(res);
                for (var i = 0; i < keys.length; i++) {
                    let data : GroupData = JSON.parse(res[keys[i]]);
                    groupsarray[keys[i]] = data;
                }
                populateDropDowns();
                pushHistoryState();
            });
        }
    });
}

function populateDropDowns() {
    let terminals = document.getElementById("menu_terminals");
    let qgroups = document.getElementById("menu_qgroups");
    let groups = document.getElementById("menu_groups");
    //@ts-ignore
    for (var terminal of dataprov.getTerminals) {
        let a = document.createElement("a");
        a.innerHTML = terminal.id;
        let b = terminals.appendChild(a);
        b.addEventListener("click", changeView.bind(this, View.TERMINAL, terminal.id));
    }
    for (var qgroup of dataprov.getQGroups) {
        let a = document.createElement("a");
        a.innerHTML = qgroup.substr(qgroup.lastIndexOf('/') + 1);
        let b = qgroups.appendChild(a);
        b.addEventListener("click", changeView.bind(this, View.QGROUP, a.innerHTML));
    }
    for (var group in groupsarray) {
        let a = document.createElement("a");
        a.innerHTML = group;
        let b = groups.appendChild(a);
        b.addEventListener("click", changeView.bind(this, View.GROUP, group));
    }

    document.getElementById("menu_game").addEventListener("click", changeView.bind(this, View.GAME, null));
}