function enterCode1() {
    var input = document.getElementById("codeInput1").value;
    var code = "1234";
    if (input == code) {
        document.getElementById("feedback1").innerHTML = "YES";
    } else {
        document.getElementById("feedback1").innerHTML = "NO";
    }
}