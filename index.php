<?php
require 'api/auth.php';

session_start();

if(!isset($_SESSION["user"])){
    header("Location: views/login.php");
    exit();
}

require 'api/db.php';

// IF Ternario
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

$sql = "SELECT id, nome, descricao, preco, imagem FROM produto";
if ($search !== '') {
    $sql .= " WHERE nome LIKE '%$search%' OR descricao LIKE '%$search%'";
}
$result = $con->query($sql);

$produtos = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Loja</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <?php if(isAdmin()){ ?>
                    <li class="nav-item">
                        <a class="nav-link" href="views/areaadmin.php">Área de administração</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="views/logout.php">Logout</a>
                </li>
                   <li class="nav-item">
                    <a class="nav-link" href="views/cart.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16" style="margin-right: 4px;">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zm3.14 4l1.25 6.5h7.22l1.25-6.5H3.14zM5.5 16a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>


<div class="container">

    <form class="row mb-4" method="get" action="">
        <div class="col-md-10">
            <input type="text" class="form-control" name="search" placeholder="Pesquisar produtos..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </form>

    <div class="row g-4">
    <?php foreach ($produtos as $produto): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
                <?php
                // Display product image directly from DB (assuming 'imagem' is BLOB or base64 string)
                if (!empty($produto['imagem'])) {
                    // If it's binary data (BLOB), encode as base64
                    $imgData = base64_encode($produto['imagem']);
                    $src = 'data:image/jpeg;base64,' . $imgData;
                } else {
                    // Placeholder image if none exists
                    $src = 'https://via.placeholder.com/300x180?text=Sem+Imagem';
                }
                ?>
                <img src="<?php echo $src; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>" style="height: 180px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                    <div class="mt-auto">
                        <strong class="text-success">€<?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong>
                        <form method="post" action="api/add_to_cart.php" class="mt-3 d-flex align-items-center gap-2">
                            <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                            <input type="number" name="quantidade" value="1" min="1" class="form-control form-control-sm" style="width: 70px;">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Adicionar ao carrinho</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>