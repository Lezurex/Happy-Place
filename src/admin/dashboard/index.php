<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: /admin");
    exit();
}

?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Happy Place - Management Console</title>

    <link rel="stylesheet" href="/css/dashboard.css">

</head>
<body>

<header>
    <div>
        <p>Happy Place - Management Console</p>
    </div>
</header>

<main>

</main>
<script src="/js/dashboard.js"></script>
</body>
</html>
