<?php

// Verifica se o utilizador está autenticado

require 'auth.php';

session_start();

if(!isset($_SESSION["user"])){
    header("Location: ../views/login.php");
    exit();
}

// Recebe o post com o produto_id e quantidade

require 'db.php';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['produto_id']) && isset($_POST['quantidade'])) {
    // Verificar se o produto já está no carrinho e se sim, atualizar a quantidade 
    $produto_id = intval($_POST['produto_id']);
    $quantidade = intval($_POST['quantidade']);
    $sql = $con->prepare("SELECT quantidade FROM Carrinho WHERE produtoId = ? AND userId = ?");
    $sql->bind_param("ii", $produto_id, $_SESSION['user']['id']);
    $sql->execute();
    $result = $sql->get_result();
    if ($result->num_rows > 0) {
        // Produto já existe no carrinho, atualizar a quantidade
        $row = $result->fetch_assoc();
        $nova_quantidade = $row['quantidade'] + $quantidade;
        $update_sql = $con->prepare("UPDATE Carrinho SET quantidade = ? WHERE produtoId = ? AND userId = ?");
        $update_sql->bind_param("iii", $nova_quantidade, $produto_id, $_SESSION['user']['id']);
        $update_sql->execute();
    } else {
        // Produto não existe no carrinho, adicionar novo item
        $insert_sql = $con->prepare("INSERT INTO Carrinho (produtoId, userId, quantidade) VALUES (?, ?, ?)");
        $insert_sql->bind_param("iii", $produto_id, $_SESSION['user']['id'], $quantidade);
        $insert_sql->execute();
    }
    // Se não, adicionar o produto ao carrinho
    header("Location: ../index.php");
}



?>