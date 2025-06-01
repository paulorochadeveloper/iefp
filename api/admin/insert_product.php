<?php 
session_start();
require '../db.php';
require '../auth.php';



if(!isset($_POST['nome']) || !isset($_POST['descricao']) || !isset($_POST['preco'])) {
    echo json_encode(array("status" => "error", "message" => "Faltam dados obrigatórios"));
    exit();
}

if(!isAdmin()){
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit();
}

$imagem = file_get_contents($_FILES['imagem']['tmp_name']);

$sql = $con->prepare("INSERT INTO produto (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)");
$sql->bind_param("ssdb", $_POST['nome'], $_POST['descricao'], $_POST['preco'], $imagem);
$sql->send_long_data(3, $imagem);
$sql->execute();
if($sql->affected_rows > 0){
    echo json_encode(array("status" => "success", "message" => "Produto inserido com sucesso"));
}else{
    echo json_encode(array("status" => "error", "message" => "Erro ao inserir produto"));
}
$sql->close();
$con->close();


?>