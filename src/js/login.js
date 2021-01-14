let user = document.getElementById("login-user");
let password = document.getElementById("login-password");
let btn = document.getElementById("login-btn");
let loginError = document.getElementById("login-error");

btn.addEventListener("click", doRequest);
user.addEventListener("keyup", enterEvent);
password.addEventListener("keyup", enterEvent);

function enterEvent(event) {
    if (event.keyCode === 13) {
        doRequest();
    }
}

function doRequest() {
    if (user.value !== "" && password.value !== "") {
        let request = new XMLHttpRequest();
        request.open("POST", document.location.protocol + "//" + document.location.hostname + ":" + document.location.port + "/php/login/login.php");
        let formData = new FormData();
        formData.append("username", user.value);
        formData.append("password", password.value);
        request.addEventListener("load", function (event) {
            if (request.status >= 200 && request.status < 300) {
                if (request.responseText === "proceed") {
                    window.location.href = document.location.protocol + "//" + document.location.hostname + ":" + document.location.port +  "/admin/dashboard"
                } else {
                    loginError.innerHTML = "Falscher Benutzername/Falsches Passwort!";
                }
            } else {
                loginError.innerHTML = "Falscher Benutzername/Falsches Passwort!";
            }
        });
        request.send(formData);
    }
}

setTimeout(function () {
    document.getElementById("logout-notice").style.opacity = "0";
}, 5000);