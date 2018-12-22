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
