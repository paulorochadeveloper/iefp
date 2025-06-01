<?php
require '../api/auth.php';

session_start();

if(!isset($_SESSION["user"])){
    header("Location: views/login.php");
    exit();
}

require '../api/db.php';


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["produtoId"])){

    $userId = $_SESSION["user"]["id"];
    $produtoId = $con->real_escape_string($_POST["produtoId"]);
    $sql = $con->prepare("DELETE FROM Carrinho WHERE userId = ? AND produtoId = ?");
    $sql->bind_param("ii", $userId, $produtoId);
    if($sql->execute()){
        header("Location: ../views/cart.php");
        exit();
    } else {
        echo "Erro ao remover produto do carrinho.";
    }
}




?>