let tablebody = document.querySelector("#table tbody");

function onLoad() {
    loadTable();
}

function loadTable() {
    let request = new XMLHttpRequest();
    request.open("get", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/loadTable.php");
    request.addEventListener("load", function (event) {
        tablebody.innerHTML = request.responseText;
    })
    request.send();
}