<?php
require "../api/auth.php";

if(isset($_GET["email"]) && isset($_GET["token"])) {
    ativarConta($_GET["email"], $_GET["token"]);
    header("Location: login.php");
    exit();
}

?>