function toQuestionGroup(qestionGroupID) {
    var URL = "questiongroup.php?qg=" + qestionGroupID;
    window.open(URL, "_self");
}

function makeHexCode() {
    code = Math.random().toString(16).slice(3, 7);
    code = code.toUpperCase();
    return code;
}