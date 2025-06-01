<?php

require 'api/auth.php';

session_start();
if(!isset($_SESSION["user"])){
    header("Location: views/login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Criar Produto</title>
</head>
<body>

    <?php if(isAdmin()){
    ?>


        <?php

// Função para obter todos os produtos
        function getProdutos($conn) {
            $sql = "SELECT id, nome, descricao, preco FROM Produto";
            return $conn->query($sql);
        }

// Upload e inserção
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
            $nome = $_POST['nome'];
            $descricao = $_POST['descricao'];
            $preco = $_POST['preco'];
            $imagem = null;

            if (!empty($_FILES['imagem']['tmp_name'])) {
                $imagem = addslashes(file_get_contents($_FILES['imagem']['tmp_name']));
            }

            if ($_POST['acao'] === 'criar') {
                $sql = "INSERT INTO Produto (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("ssds", $nome, $descricao, $preco, $imagem);
                $stmt->execute();
            } elseif ($_POST['acao'] === 'editar') {
                $id = $_POST['id'];
                if ($imagem) {
                    $sql = "UPDATE Produto SET nome=?, descricao=?, preco=?, imagem=? WHERE id=?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("ssdsi", $nome, $descricao, $preco, $imagem, $id);
                } else {
                    $sql = "UPDATE Produto SET nome=?, descricao=?, preco=? WHERE id=?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("ssdi", $nome, $descricao, $preco, $id);
                }
                $stmt->execute();
            } elseif ($_POST['acao'] === 'excluir') {
                $id = $_POST['id'];
                $con->query("DELETE FROM Produto WHERE id=$id");
            }
        }

        $produtos = getProdutos($con);
        ?>

        <h1>Administração de Produtos</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalCriar">Novo Produto</button>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $produtos->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nome'] ?></td>
                    <td><?= $row['descricao'] ?></td>
                    <td><?= $row['preco'] ?></td>
                    <td>
                        <button
                                class="btn btn-sm btn-warning btn-editar"
                                data-id="<?= $row['id'] ?>"
                                data-nome="<?= htmlspecialchars($row['nome'], ENT_QUOTES) ?>"
                                data-descricao="<?= htmlspecialchars($row['descricao'], ENT_QUOTES) ?>"
                                data-preco="<?= $row['preco'] ?>"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditar"
                        >Editar</button>

                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="acao" value="excluir">
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Modal Criar -->
        <div class="modal fade" id="modalCriar" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" enctype="multipart/form-data" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="acao" value="criar">
                        <input type="text" name="nome" class="form-control mb-2" placeholder="Nome" required>
                        <textarea name="descricao" class="form-control mb-2" placeholder="Descrição"></textarea>
                        <input type="number" step="0.01" name="preco" class="form-control mb-2" placeholder="Preço" required>
                        <input type="file" name="imagem" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Criar</button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Modal Editar (reutilizável) -->
        <div class="modal fade" id="modalEditar" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" enctype="multipart/form-data" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editar-id">
                        <input type="hidden" name="acao" value="editar">
                        <input type="text" name="nome" id="editar-nome" class="form-control mb-2" required>
                        <textarea name="descricao" id="editar-descricao" class="form-control mb-2"></textarea>
                        <input type="number" step="0.01" name="preco" id="editar-preco" class="form-control mb-2" required>
                        <input type="file" name="imagem" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const buttonsEditar = document.querySelectorAll('.btn-editar');
                const inputId = document.getElementById('editar-id');
                const inputNome = document.getElementById('editar-nome');
                const inputDescricao = document.getElementById('editar-descricao');
                const inputPreco = document.getElementById('editar-preco');

                buttonsEditar.forEach(button => {
                    button.addEventListener('click', () => {
                        inputId.value = button.dataset.id;
                        inputNome.value = button.dataset.nome;
                        inputDescricao.value = button.dataset.descricao;
                        inputPreco.value = button.dataset.preco;
                    });
                });
            });
        </script>







    <?php } ?>
    <a href="views/logout.php">Logout</a>
</body>
</html>