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

let noticeBox = document.getElementById("notice-box");

let deleteStudentId = undefined;

let currentAutocompleteInput = undefined;

function onLoad() {
    loadTable();
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
    if (addFirstname.value === "" || addCity.value === "" || addLastname.value === "") {
        showMessage(false, "Bitte füllen Sie alle Felder aus!");
        return;
    }

    if (isNaN(addCity.value.substr(0, 4))) {
        showMessage(false, "Bitte wählen Sie einen Eintrag aus den Vorschlägen aus. Die ersten vier Zeichen müssen eine Postleitzahl bilden.");
        return;
    }

    let request = new XMLHttpRequest();
    let formData = new FormData();
    formData.append("firstname", addFirstname.value);
    formData.append("lastname", addLastname.value);
    formData.append("plz", addCity.value.substr(0, 4));
    request.open("post", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/addStudent.php");
    request.addEventListener("load", function (event) {
        loadTable();
        if (request.responseText === "200") {
            showMessage(true, "Lernender erfolgreich hinzugefügt!");
            addFirstname.value = "";
            addLastname.value = "";
            addCity.value = "";
        } else {
            showMessage(false, "Während dem Hinzufügen ist ein Fehler aufgetreten!");
        }
    });
    request.send(formData);
}

function editStudent() {
    if (editFirstname.value === "" || editCity.value === "" || editLastname.value === "") {
        showMessage(false, "Bitte füllen Sie alle Felder aus!");
        return;
    }

    if (isNaN(editCity.value.substr(0, 4))) {
        showMessage(false, "Bitte wählen Sie einen Eintrag aus den Vorschlägen aus. Die ersten vier Zeichen müssen eine Postleitzahl bilden.");
        return;
    }

    let request = new XMLHttpRequest();
    let formData = new FormData();
    formData.append("id", currentStudentId);
    formData.append("firstname", editFirstname.value);
    formData.append("lastname", editLastname.value);
    formData.append("plz", editCity.value.substr(0, 4));
    request.open("post", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/editStudent.php");
    request.addEventListener("load", function (event) {
        loadTable();
        editModal.style.display = "none";
        switch (request.responseText) {
            case "200":
                showMessage(true, "Lernender erolgreich editiert!");
                break;
            case "403":
                showMessage(false, "Ihre Sitzung ist abgelaufen. Bitte loggen Sie sich erneut ein!");
                break;
            default:
                showMessage(false, "Während dem Editieren ist ein Fehler aufgetreten!");
        }
    });
    request.send(formData);
}

function deleteStudent() {
    let request = new XMLHttpRequest();
    let formData = new FormData();
    formData.append("id", deleteStudentId);
    request.open("post", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/deleteStudent.php");
    request.addEventListener("load", function (event) {
        loadTable();
        switch (request.responseText) {
            case "200":
                showMessage(true, "Lernender erolgreich gelöscht!");
                break;
            case "403":
                showMessage(false, "Ihre Sitzung ist abgelaufen. Bitte loggen Sie sich erneut ein!");
                break;
            default:
                showMessage(false, "Während dem Löschen ist ein Fehler aufgetreten!");
        }
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
            deleteStudentId = parseInt(icon.dataset.id);
            deleteStudent();
        });
    });
}

editSave.addEventListener("click", function (event) {
   editStudent();
});

editModalX.onclick = function() {
    editModal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == editModal) {
        editModal.style.display = "none";
    }
}

function showMessage(successful, text) {
    noticeBox.style.transition = ".2s";
    if (successful) {
        noticeBox.style.backgroundColor = "#75ff8d";
        noticeBox.style.borderColor = "#00d623";
    } else {
        noticeBox.style.backgroundColor = "#ff6767";
        noticeBox.style.borderColor = "#ae0000";
    }
    noticeBox.innerHTML = text;
    noticeBox.style.opacity = "100%";
    noticeBox.style.transition = ".5s";
    setTimeout(function () {
        document.getElementById("notice-box").style.opacity = "0";
    }, 5000);
}

document.querySelectorAll(".city-autocomplete").forEach(function (obj) {
    obj.addEventListener("keyup", function (event) {
        currentAutocompleteInput = obj;
        let request = new XMLHttpRequest();
        let formData = new FormData();
        formData.append("query", obj.value);
        request.open("post", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/php/dashboard/getReccommendations.php");
        request.addEventListener("load", function (event) {
            updateCityDropdown(JSON.parse(request.responseText));
        });
        request.send(formData);
    });
    obj.addEventListener("focus", function () {
        currentAutocompleteInput = obj;
    })
});

function updateCityDropdown(data) {
    let list = currentAutocompleteInput.nextElementSibling;
    list.innerHTML = "";
    for (let item in data) {
        item = data[item];
        let optionElement = document.createElement('option');
        optionElement.setAttribute("value", item.plz + " " + item.name);
        list.appendChild(optionElement);
    }
}