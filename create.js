window.onload = function() {
    var radios = document.querySelectorAll('input[type=radio]');
    for (var i = 0; i < radios.length; i++) {
        radios[i].addEventListener('click', changeDisplay);
    }
    var submits = document.querySelectorAll('input[type=submit]');
    for (var i = 0; i < submits.length; i++) {
        submits[i].addEventListener('click', submitData);
    }

    document.getElementById("game_num_available").addEventListener('change', function(ev) {
        document.getElementById("game_num").disabled = ev.target.checked;
    });
};

var currentselection;

function changeDisplay(ev) {
    let t = ev.target;
    let id = t.value;
    if (currentselection) {
        document.getElementById(currentselection).style.display = "none";
    }
    currentselection = id;
    document.getElementById(id).style.display = "block";
}

function submitData(ev) {
    let status = document.getElementById("status")
    status.innerHTML = "Submitting..."
    status.style.color = "black";
    let t = ev.target;
    let parent = t.parentNode;
    let inputs = parent.children;
    let o = {creationtype: currentselection};
    for (var i = 0; i < inputs.length; i++) {
        let node = inputs[i];
        if (node.nodeName === "INPUT" || node.nodeName === "TEXTAREA" || node.nodeName === "SELECT") {
            o[node.name] = node.type === "checkbox" ? node.checked : encodeURIComponent(encodeURI(node.value));
        }
    }
    if (o.creationtype !== "game") {
        let gamepinholder = document.getElementById("game_num");
        let disabled = gamepinholder.disabled;
        let gamepin = gamepinholder.value;
        if (!disabled) {
            if (gamepin !== "") {
                o['game'] = gamepin;
                uploadData(o);
            } else {
                alert('No game pin is set!');
            }
        } else {
            alert('No game pin is set!');
        }
    } else {
        uploadData(o);
    }
    console.log(o);
}

function uploadData(data) {
    fetch('create.php', {
        method: "POST",
        body: JSON.stringify({data: data})
    })
    .then(res => {
        if (res.ok) {
            try {
                return res.json();
            } catch(e) {
                let status = document.getElementById("status")
                status.innerHTML = "An error occurred while trying to process the response from the server";
                status.style.color = "red";
            }
        }
    })
    .then(res => {
        let status = document.getElementById("status");
        if (res.type === "error") {
            status.style.color = "red";
        } else {
            status.style.color = "green";
        }
        status.innerHTML = res.message;
    });
}