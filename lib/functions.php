<?php
//TODO 1: require db.php
require(__DIR__ . "/db.php");
//require safer_echo.php
require(__DIR__ . "/safer_echo.php");
//TODO 2: filter helpers
if(isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])){
    $email = se($_POST, "email", false);
    $password = se($_POST, "password", false);
    $confirm = se($_POST, "confirm", false);
}
//TODO 3: User helpers

//TODO 4: Flash Message Helpers
?>