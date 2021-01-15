let tablebody = document.querySelector("#table tbody");
let addFirstname = document.getElementById("add-firstname");
let addLastname = document.getElementById("add-lastname");
let addCity = document.getElementById("add-city");
let addBtn = document.getElementById("add-btn");

let editModal = document.getElementById("edit-modal");
let editModalX = document.getElementsByClassName("close")[0];

let editFirstname = document.getElementById("edit-firstname");
let editLastname = document.getElementById("edit-lastname");
let editCity = document.getElementById("edit-city");
let editSave = document.getElementById("edit-save");
let currentStudentId = undefined;

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
        setEventListeners();
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
        loadTable();
    });
    request.send(formData);
}

function editStudent() {
    let request = new XMLHttpRequest();
    let formData = new FormData();
    formData.append("id", currentStudentId);
    formData.append("firstname", editFirstname.value);
    formData.append("lastname", editLastname.value);
    formData.append("plz", editCity.value);
    request.open("post", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/editStudent.php");
    request.addEventListener("load", function (event) {
        loadTable();
    });
    request.send(formData);
}

function setEventListeners() {
    document.querySelectorAll(".edit").forEach(function (icon) {
        icon.addEventListener("click", function (event) {
            let city = icon.parentElement.previousSibling;
            let lastname = city.previousSibling;
            let firstname = lastname.previousSibling;
            editFirstname.value = firstname.innerHTML;
            editLastname.value = lastname.innerHTML;
            editCity.value = city.innerHTML;
            currentStudentId = parseInt(icon.dataset.id);
            editModal.style.display = "block";
        });
    });
    document.querySelectorAll(".delete").forEach(function (icon) {
        icon.addEventListener("click", function (event) {
            // do delete stuff
        });
    });
}

editModalX.onclick = function() {
    editModal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == editModal) {
        editModal.style.display = "none";
    }
}