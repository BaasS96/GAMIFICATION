window.onload = () => {
    document.getElementById("submit").addEventListener('click', () => {
        checkGamePin();
    });
};
var Stage;
(function (Stage) {
    Stage[Stage["GROUP"] = 0] = "GROUP";
    Stage[Stage["GROUPCODE"] = 1] = "GROUPCODE";
    Stage[Stage["GROUPNAME"] = 2] = "GROUPNAME";
    Stage[Stage["GROUPMEMBERS"] = 3] = "GROUPMEMBERS";
})(Stage || (Stage = {}));
var currentstage = Stage.GROUP;
var gamecode, groupcode, groupname;
function checkGamePin() {
    document.getElementById('submit').innerHTML = "<div class='dots'><div class='dot dot1'></div><div class='dot dot2'></div><div class='dot dot3'></div></div>";
    let pin = document.getElementById("pin").value;
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
        }
        else {
            document.getElementById('submit').innerHTML = "GO!";
            document.getElementById('error').style.display = "inline-block";
        }
    });
}
function checkGroupCode() {
    document.getElementById('submit').innerHTML = "<div class='dots'><div class='dot dot1'></div><div class='dot dot2'></div><div class='dot dot3'></div></div>";
    let pin = document.getElementById("pin").value;
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
        }
        else {
            //@ts-ignore
            if (res.alreadysetup) {
                //Continue anyway, the group is already set up
                setTimeout(function () {
                    fetch('auth/setsession.php?game=' + gamecode + '&group=' + pin)
                        .then(res => {
                        if (res.ok) {
                            return res.text();
                        }
                    })
                        .then(res => {
                        if (res === "") {
                            location.href = "index.php";
                        }
                        else {
                            document.getElementById('error').innerHTML = "Er is iets fout gegaan!";
                            document.getElementById('submit').innerHTML = "GO!";
                            document.getElementById('error').style.display = "inline-block";
                        }
                    });
                }, 1000);
            }
            else {
                if (res.error) {
                    document.getElementById('error').innerHTML = res.error;
                }
                document.getElementById('submit').innerHTML = "GO!";
                document.getElementById('error').style.display = "inline-block";
            }
        }
    });
}
function nextStepSubmitGroupData() {
    let pin = document.getElementById("pin").value;
    groupname = pin;
    setTimeout(nextStage, 100);
}
function submitGroupData() {
    document.getElementById('submit').innerHTML = "<div class='dots'><div class='dot dot1'></div><div class='dot dot2'></div><div class='dot dot3'></div></div>";
    let pin = document.getElementById("pin");
    let members = pin.value;
    fetch('auth/submitgroupdata.php?game=' + gamecode + '&group=' + groupcode + '&name=' + groupname + '&members=' + members)
        .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
        .then(res => {
        if (res.success) {
            //Redirect
            setTimeout(function () { location.href = "index.php"; }, 1000);
        }
        else {
            alert("An error occurred! Please try again or contact an administrator...");
        }
    });
}
function nextStage() {
    let newdiv = getNewDiv();
    let olddivs = document.getElementById("lastholder");
    let child = olddivs.children[0];
    child.style.transition = "margin-left 1s";
    let w = olddivs.offsetWidth.toString();
    let h = olddivs.offsetHeight.toString();
    olddivs.style.width = w + "px";
    olddivs.style.height = h + "px";
    olddivs.style.padding = "0px";
    //olddivs.children[0].style.position = "absolute";
    newdiv.style.marginTop = (parseInt(h) + 100).toString() + "px";
    child.style.marginLeft = (parseInt(w) + 200).toString() + "px";
    setTimeout(function (parent, newdiv) {
        parent.removeChild(this);
        parent.appendChild(newdiv);
        newdiv.style.transition = "margin-top 1s";
        setTimeout(function () {
            this.style.marginTop = "0px";
        }.bind(newdiv), 50);
        document.getElementById("submit").addEventListener('click', () => {
            if (currentstage == Stage.GROUPCODE) {
                checkGroupCode();
            }
            else if (currentstage == Stage.GROUPNAME) {
                nextStepSubmitGroupData();
            }
            else if (currentstage == Stage.GROUPMEMBERS) {
                submitGroupData();
            }
        });
    }.bind(child, olddivs, newdiv), 800);
}
function getNewDiv() {
    let template;
    if (currentstage == Stage.GROUP) {
        currentstage = Stage.GROUPCODE;
        template = document.getElementById("groupcode");
    }
    else if (currentstage == Stage.GROUPCODE) {
        currentstage = Stage.GROUPNAME;
        template = document.getElementById("groupname");
    }
    else if (currentstage == Stage.GROUPNAME) {
        currentstage = Stage.GROUPMEMBERS;
        template = document.getElementById("groupmembers");
    }
    else {
        return null;
    }
    let fragment = template.content.cloneNode(true);
    let div = document.createElement("div");
    for (var i = 0; i < fragment.children.length; i++) {
        let a = div.appendChild(fragment.children[i]);
    }
    return div.firstElementChild;
}
