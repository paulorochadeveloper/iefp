<?php
session_start();
require '../db.php';
require '../auth.php';

if(!isAdmin()){
    echo json_encode(array("status" => "error", "message" => "Acesso negado"));
    exit();
}

if(!isset($_GET['id'])) {
    echo json_encode(array("status" => "error", "message" => "ID do produto não fornecido"));
    exit();
}

$id = $_GET['id'];
$sql = $con->prepare("DELETE FROM produto WHERE id = ?");
$sql->bind_param("i", $id);
$sql->execute();
if($sql->affected_rows > 0){
    echo json_encode(array("status" => "success", "message" => "Produto eliminado com sucesso"));
}else{
    echo json_encode(array("status" => "error", "message" => "Erro ao eliminar produto"));
}
$sql->close();
$con->close();

?>