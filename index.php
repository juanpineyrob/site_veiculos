<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Obtener categorías para el menú
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar búsquedas
$veiculos = [];
$busca_modelo = '';
$busca_ano = '';
$categoria_filtro = '';

if (isset($_GET['busca_modelo']) && !empty($_GET['busca_modelo'])) {
    $busca_modelo = $_GET['busca_modelo'];
    $sql = "SELECT v.*, c.nome as categoria_nome FROM veiculos v 
            JOIN categorias c ON v.id_categoria = c.id 
            WHERE v.modelo LIKE :modelo";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['modelo' => '%' . $busca_modelo . '%']);
    $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_GET['busca_ano']) && !empty($_GET['busca_ano'])) {
    $busca_ano = $_GET['busca_ano'];
    $sql = "SELECT v.*, c.nome as categoria_nome FROM veiculos v 
            JOIN categorias c ON v.id_categoria = c.id 
            WHERE v.ano LIKE :ano";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['ano' => '%' . $busca_ano . '%']);
    $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $categoria_filtro = $_GET['categoria'];
    $sql = "SELECT v.*, c.nome as categoria_nome FROM veiculos v 
            JOIN categorias c ON v.id_categoria = c.id 
            WHERE v.id_categoria = :categoria";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['categoria' => $categoria_filtro]);
    $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Mostrar todos los vehículos
    $sql = "SELECT v.*, c.nome as categoria_nome FROM veiculos v 
            JOIN categorias c ON v.id_categoria = c.id 
            ORDER BY v.id DESC";
    $stmt = $pdo->query($sql);
    $veiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-car"></i> Sistema de Veículos
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Categorias
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($categorias as $categoria): ?>
                                <li><a class="dropdown-item" href="?categoria=<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Gerenciar
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="admin/veiculos.php">Veículos</a></li>
                                <li><a class="dropdown-item" href="admin/categorias.php">Categorias</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item">
                            <span class="nav-link">Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Sair</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-white mb-4">Encontre o Veículo Perfeito</h1>
                    <p class="lead text-white mb-4">Explore nossa ampla seleção de veículos de qualidade</p>
                </div>
                <div class="col-lg-6">
                    <div class="search-box">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="busca_modelo" placeholder="Buscar por modelo..." value="<?= htmlspecialchars($busca_modelo) ?>">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="busca_ano" placeholder="Buscar por ano..." value="<?= htmlspecialchars($busca_ano) ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div class="container my-5">
        <?php if (!empty($busca_modelo) || !empty($busca_ano) || !empty($categoria_filtro)): ?>
            <div class="row mb-4">
                <div class="col">
                    <h2>Resultados da Busca</h2>
                    <?php if (!empty($busca_modelo)): ?>
                        <p>Buscando por modelo: <strong><?= htmlspecialchars($busca_modelo) ?></strong></p>
                    <?php endif; ?>
                    <?php if (!empty($busca_ano)): ?>
                        <p>Buscando por ano: <strong><?= htmlspecialchars($busca_ano) ?></strong></p>
                    <?php endif; ?>
                    <?php if (!empty($categoria_filtro)): ?>
                        <p>Categoria: <strong><?= htmlspecialchars($categorias[array_search($categoria_filtro, array_column($categorias, 'id'))]['nome']) ?></strong></p>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-outline-primary">Limpar Filtros</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Grid de Veículos -->
        <div class="row">
            <?php if (empty($veiculos)): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Nenhum veículo encontrado.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($veiculos as $veiculo): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 veiculo-card">
                            <div class="card-img-top-container">
                                <?php if (!empty($veiculo['imagem'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($veiculo['imagem']) ?>" class="card-img-top" alt="<?= htmlspecialchars($veiculo['modelo']) ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-car"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="categoria-badge">
                                    <?= htmlspecialchars($veiculo['categoria_nome']) ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($veiculo['marca']) ?> <?= htmlspecialchars($veiculo['modelo']) ?></h5>
                                <p class="card-text">
                                    <strong>Placa:</strong> <?= htmlspecialchars($veiculo['placa']) ?><br>
                                    <strong>Cor:</strong> <?= htmlspecialchars($veiculo['cor']) ?><br>
                                    <strong>Ano:</strong> <?= htmlspecialchars($veiculo['ano']) ?>
                                </p>
                                <a href="veiculo.php?id=<?= $veiculo['id'] ?>" class="btn btn-primary">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Sistema de Veículos</h5>
                    <p>Encontre o veículo perfeito para suas necessidades.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Sistema de Veículos. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 