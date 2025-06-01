<?php

require '../api/auth.php';

session_start();

if(!isset($_SESSION["user"])){
    header("Location: views/login.php");
    exit();
}

require '../api/db.php';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["produtoId"]) && isset($_POST["quantidade"])){
    
    $userId = $_SESSION["user"]["id"];
    $produtoId = $con->real_escape_string($_POST["produtoId"]);
    $quantidade = $con->real_escape_string($_POST["quantidade"]);

    if($quantidade <= 0){
        $sql = $con->prepare("DELETE FROM Carrinho WHERE userId = ? AND produtoId = ?");
        $sql->bind_param("ii", $userId, $produtoId);
    } else {
        $sql = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE userId = ? AND produtoId = ?");
        $sql->bind_param("iii", $quantidade, $userId, $produtoId);
    }
    if($sql->execute()){
        header("Location: ../views/cart.php");
        exit();
    } else {
        echo "Erro ao atualizar carrinho.";
    }
}


?>