function switchGame() {
    gamepin = "";
    pinvalid = false;
    document.getElementById("menu").style.display = "none";
    document.getElementById("header_txt").innerHTML = "Game ";
    let input = document.getElementById("gameinput");
    input.style.display = "block";
    input.value = "";
    let feedback = document.getElementById("gameinput_feedback");
    feedback.style.display = "inline-block";
    feedback.innerHTML = "This game doesn't exist!";
    feedback.classList.remove("green");
}
function pushHistoryState() {
    let historyObj = { id: generateID() };
    let s = new XMLSerializer();
    let content = [];
    content.push(s.serializeToString(document.getElementById("titlebar").cloneNode(true)));
    content.push(s.serializeToString(document.getElementById("content").cloneNode(true)));
    sessionStorage.setItem(historyObj.id, JSON.stringify(content));
    history.pushState(historyObj, new Date().getTime().toString());
}
function generateID() {
    let id = "";
    do {
        for (var i = 0; i < 5; i++) {
            let num = Math.floor(Math.random() * 10);
            id += num.toString();
        }
    } while (sessionStorage.getItem(id));
    return id;
}
var View;
(function (View) {
    View[View["GAME"] = 0] = "GAME";
    View[View["GROUP"] = 1] = "GROUP";
    View[View["QGROUP"] = 2] = "QGROUP";
    View[View["TERMINAL"] = 3] = "TERMINAL";
})(View || (View = {}));
function changeView(viewType, viewID) {
    console.log(viewType, viewID);
}
