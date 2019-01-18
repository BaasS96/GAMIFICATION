function switchGame() {
    gamepin = "";
    pinvalid = false;
    document.getElementById("menu").style.display = "none";
    document.getElementById("header_txt").innerHTML = "Game ";
    let input = document.getElementById("gameinput")
    input.style.display = "block"; 
    input.value = "";
    let feedback = document.getElementById("gameinput_feedback");
    feedback.style.display = "inline-block";
    feedback.innerHTML = "This game doesn't exist!";
    feedback.classList.remove("green");
}

function pushHistoryState() {
    let historyObj = {id: generateID()};
    let s = new XMLSerializer();
    let content = [];
    content.push(s.serializeToString(document.getElementById("titlebar").cloneNode(true)));
    content.push(s.serializeToString(document.getElementById("content").cloneNode(true)));
    sessionStorage.setItem(historyObj.id, JSON.stringify(content));
    history.pushState(historyObj, new Date().getTime().toString());
}

function generateID() {
    let id : string = "";
    do {
        for (var i = 0; i < 5; i++) {
            let num = Math.floor(Math.random() * 10);
            id += num.toString();
        }
    } while (sessionStorage.getItem(id));
    return id;
}

enum View {
    GAME,
    GROUP,
    QGROUP,
    TERMINAL
}

function changeView(viewType : View, viewID : string) {
    console.log(viewType, viewID);
}