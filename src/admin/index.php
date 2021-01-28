<?php

session_start();

if (isset($_SESSION['username'])) {
    header("Location: /admin/dashboard/");
}

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Happy Place - Login</title>

    <link rel="stylesheet" href="/css/admin.css">
</head>
<body>
<?php

if (isset($_GET['logout'])) {
    echo '<div id="logout-notice">
    Erfolgreich ausgeloggt!
</div>';
}

?>
<div id="login-content">
    <table>
        <tr>
            <td>Benutzername: </td>
            <td><input type="text" id="login-user"></td>
        </tr>
        <tr>
            <td>Passwort: </td>
            <td><input type="password" id="login-password"></td>
        </tr>
        <tr>
            <td colspan="2" id="login-error"></td>
        </tr>
        <tr>
            <td></td>
            <td><button id="login-btn">Anmelden</button></td>
        </tr>
    </table>
</div>
<script src="/js/login.js"></script>
</body>
</html>
