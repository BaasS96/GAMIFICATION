window.onload = () => {
    document.getElementById("submit").addEventListener('click', () => {
        checkGamePin();
    });
};

enum Stage {
    GROUP,
    GROUPCODE,
    GROUPNAME,
    GROUPMEMBERS
}

var currentstage = Stage.GROUP;
var gamecode, groupcode, groupname;

function checkGamePin() {
    document.getElementById('submit').innerHTML = "<div class='dots'><div class='dot dot1'></div><div class='dot dot2'></div><div class='dot dot3'></div></div>";
    let pin = <HTMLInputElement>document.getElementById("pin").value;
    fetch('auth/checkpin.php?pin=' + pin)
    .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
    .then(res => {
        if (res.success) {
            //Next step;
            gamecode = pin;
            document.title = "GROUPCODE";
            setTimeout(nextStage, 100);
        } else {
            document.getElementById('submit').innerHTML = "GO!";
            document.getElementById('error').style.display = "inline-block";
        }
    });
}

function checkGroupCode() {
    document.getElementById('submit').innerHTML = "<div class='dots'><div class='dot dot1'></div><div class='dot dot2'></div><div class='dot dot3'></div></div>";
    let pin = <HTMLInputElement>document.getElementById("pin").value;
    fetch('auth/checkcode.php?game=' + gamecode + '&code=' + pin)
    .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
    .then(res => {
        if (res.success) {
            groupcode = pin;
            document.title = "GROUPMEMBERS";
            setTimeout(nextStage, 100);
        } else {
            document.getElementById('submit').innerHTML = "GO!";
            document.getElementById('error').style.display = "inline-block";
        }
    })
}

function nextStepSubmitGroupData() {
    let pin = <HTMLInputElement>document.getElementById("pin").value;
    groupname = pin;
    setTimeout(nextStage, 100);
}

function submitGroupData() {
    document.getElementById('submit').innerHTML = "<div class='dots'><div class='dot dot1'></div><div class='dot dot2'></div><div class='dot dot3'></div></div>";
    let pin = <HTMLInputElement>document.getElementById("pin");
    let members = <string>pin.value;
    fetch('auth/submitgroupdata.php?game=' + gamecode + '&group=' + groupcode +'&name=' + groupname + '&members=' + members)
    .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
    .then(res => {
        if (res.success) {
            //Redirect
            setTimeout(function() {location.href = "index.php"}, 1000);
        } else {
            alert("An error occurred! Please try again or contact an administrator...");
        }
    });
}

function nextStage() {
    let newdiv = <HTMLElement>getNewDiv();
    let olddivs = <HTMLElement>document.getElementById("lastholder");
    let child = <HTMLElement>olddivs.children[0];
    child.style.transition = "margin-left 1s";
    let w = olddivs.offsetWidth.toString();
    let h = olddivs.offsetHeight.toString();
    olddivs.style.width = w + "px";    
    olddivs.style.height = h + "px";  
    olddivs.style.padding = "0px";
    //olddivs.children[0].style.position = "absolute";
    newdiv.style.marginTop = (parseInt(h) + 100).toString() + "px";
    child.style.marginLeft = (parseInt(w) + 200).toString() + "px";
    setTimeout(function(parent, newdiv) {
        parent.removeChild(this);
        parent.appendChild(newdiv);
        newdiv.style.transition = "margin-top 1s";
        setTimeout(function() {
            this.style.marginTop = "0px";
        }.bind(newdiv), 50);
        document.getElementById("submit").addEventListener('click', () => {
            if (currentstage == Stage.GROUPCODE) {
                checkGroupCode();
            } else if (currentstage == Stage.GROUPNAME) {
                nextStepSubmitGroupData();
            } else if (currentstage == Stage.GROUPMEMBERS) {
                submitGroupData();
            }
        });
    }.bind(child, olddivs, newdiv), 800);
}   

function getNewDiv() : Node {
    let template;
    if (currentstage == Stage.GROUP) {
        currentstage = Stage.GROUPCODE; 
        template = <HTMLTemplateElement>document.getElementById("groupcode");
    } else if (currentstage == Stage.GROUPCODE) {
        currentstage = Stage.GROUPNAME;
        template = <HTMLTemplateElement>document.getElementById("groupname");
    } else if (currentstage == Stage.GROUPNAME) {
        currentstage = Stage.GROUPMEMBERS;
        template = <HTMLTemplateElement>document.getElementById("groupmembers");
    } else {
        return null;
    }
    let fragment = template.content.cloneNode(true);
    let div = document.createElement("div");
    for (var i = 0; i < fragment.children.length; i++) {
        let a = div.appendChild(fragment.children[i]);
    }
    return div.firstElementChild;
}