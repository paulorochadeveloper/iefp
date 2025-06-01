<?php
    session_start();
    require '../api/auth.php';
    if( !isAdmin() ){
        header("Location: ../index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de administração</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Administração</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold">Área de administração</h1>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#insertProductModal">
                <i class="bi bi-plus-circle"></i> Inserir Novo Produto
            </button>
        </div>

        <?php
        require_once '../api/db.php';

        $stmt = $con->prepare("SELECT id, nome, preco, descricao, imagem FROM produto ORDER BY id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $produtos = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $con->close();
        ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="h5 mb-0">Produtos</h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Descrição</th>
                                <th>Imagem</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produto['id']) ?></td>
                                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                                    <td><span class="badge bg-success"><?= number_format($produto['preco'], 2, ',', '.') ?> €</span></td>
                                    <td><?= htmlspecialchars($produto['descricao']) ?></td>
                                    <td>
                                        <?php if (!empty($produto['imagem'])): ?>
                                            <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="img-thumbnail" style="width: 80px; height: auto;">
                                        <?php else: ?>
                                            <span class="text-muted">Sem imagem</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-sm me-1" title="Eliminar"
                                            onclick="if(confirm('Tem a certeza que deseja eliminar este produto?')) { fetch('../api/admin/delete_product.php?id=<?= $produto['id'] ?>').then(r => r.json()).then(result => { if(result.status === 'success'){ location.reload(); } else { alert(result.message || 'Erro ao eliminar produto.'); } }); }">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <button class="btn btn-warning btn-sm" title="Editar"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editProductModal"
                                            data-id="<?= htmlspecialchars($produto['id']) ?>"
                                            data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                                            data-preco="<?= htmlspecialchars($produto['preco']) ?>"
                                            data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProductForm" method="post" enctype="multipart/form-data" action="../api/admin/edit_product.php">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="editProductModalLabel">Editar Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="editProductName" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Preço</label>
                            <input type="number" step="0.01" class="form-control" id="editProductPrice" name="preco" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Descrição</label>
                            <textarea class="form-control" id="editProductDescription" name="descricao" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editProductImage" class="form-label">Imagem (deixe em branco para não alterar)</label>
                            <input type="file" class="form-control" id="editProductImage" name="imagem">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Insert Modal -->
    <div class="modal fade" id="insertProductModal" tabindex="-1" aria-labelledby="insertProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="../api/admin/insert_product.php" enctype="multipart/form-data">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="insertProductModalLabel">Inserir Novo Produto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="productName" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Preço</label>
                            <input type="number" step="0.01" class="form-control" id="productPrice" name="preco" required>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Descrição</label>
                            <textarea class="form-control" id="productDescription" name="descricao" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Imagem</label>
                            <input type="file" class="form-control" id="productImage" name="imagem" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Inserir Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast for feedback -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <div id="feedbackToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
    // Preencher modal de edição com dados do produto
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('editProductModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            document.getElementById('editProductId').value = button.getAttribute('data-id');
            document.getElementById('editProductName').value = button.getAttribute('data-nome');
            document.getElementById('editProductPrice').value = button.getAttribute('data-preco');
            document.getElementById('editProductDescription').value = button.getAttribute('data-descricao');
            document.getElementById('editProductImage').value = '';
        });

        // Submeter edição via AJAX
        document.getElementById('editProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                let message = result.message || 'Produto atualizado com sucesso!';
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = message;

                toastEl.classList.remove('text-bg-primary', 'text-bg-danger', 'text-bg-success');
                if (result.status === 'success') {
                    toastEl.classList.add('text-bg-success');
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastEl.classList.add('text-bg-danger');
                }

                var toast = new bootstrap.Toast(toastEl);
                toast.show();

            } catch (error) {
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = 'Erro ao atualizar produto.';
                toastEl.classList.remove('text-bg-primary', 'text-bg-success');
                toastEl.classList.add('text-bg-danger');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });

        // Submeter inserção via AJAX
        document.querySelector('#insertProductModal form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                let message = result.message || 'Produto inserido com sucesso!';
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = message;

                toastEl.classList.remove('text-bg-primary', 'text-bg-danger', 'text-bg-success');
                if (result.status === 'success') {
                    toastEl.classList.add('text-bg-success');
                    form.reset();
                    var modal = bootstrap.Modal.getInstance(document.getElementById('insertProductModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastEl.classList.add('text-bg-danger');
                }

                var toast = new bootstrap.Toast(toastEl);
                toast.show();

            } catch (error) {
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = 'Erro ao inserir produto.';
                toastEl.classList.remove('text-bg-primary', 'text-bg-success');
                toastEl.classList.add('text-bg-danger');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>