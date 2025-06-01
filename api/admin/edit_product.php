<?php
session_start();
require '../db.php';
require '../auth.php';

header('Content-Type: application/json');

if (!isset($_POST['id']) || !isset($_POST['nome']) || !isset($_POST['descricao']) || !isset($_POST['preco'])) {
    echo json_encode(array("status" => "error", "message" => "Faltam dados obrigatórios"));
    exit();
}

if (!isAdmin()) {
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit();
}

$id = intval($_POST['id']);
$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$preco = $_POST['preco'];

if (isset($_FILES['imagem']) && $_FILES['imagem']['size'] > 0) {
    $imagem = file_get_contents($_FILES['imagem']['tmp_name']);
    $sql = $con->prepare("UPDATE produto SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?");
    $sql->bind_param("ssdsi", $nome, $descricao, $preco, $imagem, $id);
    $sql->send_long_data(3, $imagem);
} else {
    $sql = $con->prepare("UPDATE produto SET nome=?, descricao=?, preco=? WHERE id=?");
    $sql->bind_param("ssdi", $nome, $descricao, $preco, $id);
}

$sql->execute();

if ($sql->affected_rows > 0) {
    echo json_encode(array("status" => "success", "message" => "Produto atualizado com sucesso"));
} else {
    echo json_encode(array("status" => "error", "message" => "Nenhuma alteração feita ou erro ao atualizar produto"));
}

$sql->close();
$con->close();
?>