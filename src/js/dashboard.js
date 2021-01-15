let tablebody = document.querySelector("#table tbody");
let addFirstname = document.getElementById("add-firstname");
let addLastname = document.getElementById("add-lastname");
let addCity = document.getElementById("add-city");
let addBtn = document.getElementById("add-btn");

function onLoad() {
    loadTable();
    addCity.addEventListener("keypress", function (event) {
       // TODO get suggestions
    });
    addBtn.addEventListener("click", addStudent);
}

function loadTable() {
    let request = new XMLHttpRequest();
    request.open("get", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/loadTable.php");
    request.addEventListener("load", function (event) {
        tablebody.innerHTML = request.responseText;
    })
    request.send();
}

function addStudent() {
    let request = new XMLHttpRequest();
    let formData = new FormData();
    formData.append("firstname", addFirstname.value);
    formData.append("lastname", addLastname.value);
    formData.append("plz", addCity.value);
    request.open("post", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/addStudent.php");
    request.addEventListener("load", function (event) {

    });
    request.send(formData);
}