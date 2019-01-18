import { loadGame, gamepin, setGamePin } from './load.js';
var pinvalid = false;
window.onload = function () {
    document.getElementById("gameinput_feedback").addEventListener('click', () => {
        if (pinvalid) {
            loadGame();
        }
    });
    document.getElementById("gameinput").addEventListener('input', function (ev) {
        let target = ev.target;
        let newchar = '';
        if (ev.data) {
            newchar = ev.data;
        }
        else {
            let val = target.value;
            newchar = val.slice(-1);
        }
        if (isNaN(newchar)) {
            target.value = gamepin;
            ev.stopPropagation();
            ev.preventDefault();
            pinvalid = false;
            return false;
        }
        if (target.value.length > 4) {
            target.value = gamepin;
            ev.stopPropagation();
            ev.preventDefault();
            pinvalid = false;
            return false;
        }
        setGamePin(target.value);
        fetch("../../auth/checkpin.php?pin=" + ev.target.value)
            .then((res) => {
            if (res.ok) {
                return res.json();
            }
        })
            .then(json => {
            if (json.success) {
                let feedback = document.getElementById('gameinput_feedback');
                feedback.innerHTML = "Click here to load this game!";
                feedback.classList.add("green");
                pinvalid = true;
            }
            else {
                pinvalid = false;
                document.getElementById('gameinput_feedback').classList.remove("green");
            }
        });
    });
};
