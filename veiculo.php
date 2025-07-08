<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = (int)$_GET['id'];

// Buscar o veículo
$sql = "SELECT v.*, c.nome as categoria_nome FROM veiculos v 
        JOIN categorias c ON v.id_categoria = c.id 
        WHERE v.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($veiculo['marca'] . ' ' . $veiculo['modelo']) ?> - Sistema de Veículos</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
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

    <!-- Detalhes do Veículo -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                        <li class="breadcrumb-item"><a href="index.php?categoria=<?= $veiculo['id_categoria'] ?>"><?= htmlspecialchars($veiculo['categoria_nome']) ?></a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($veiculo['marca'] . ' ' . $veiculo['modelo']) ?></li>
                    </ol>
                </nav>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($veiculo['imagem'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($veiculo['imagem']) ?>" 
                                         class="img-fluid rounded" 
                                         alt="<?= htmlspecialchars($veiculo['modelo']) ?>">
                                <?php else: ?>
                                    <div class="no-image d-flex align-items-center justify-content-center" style="height: 300px;">
                                        <i class="fas fa-car fa-5x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h1 class="h2 mb-3"><?= htmlspecialchars($veiculo['marca']) ?> <?= htmlspecialchars($veiculo['modelo']) ?></h1>
                                
                                <div class="mb-3">
                                    <span class="badge bg-primary fs-6"><?= htmlspecialchars($veiculo['categoria_nome']) ?></span>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Placa:</strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($veiculo['placa']) ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Cor:</strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($veiculo['cor']) ?></span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Ano:</strong><br>
                                        <span class="text-muted"><?= htmlspecialchars($veiculo['ano']) ?></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Quilometragem:</strong><br>
                                        <span class="text-muted"><?= number_format($veiculo['quilometragem'], 0, ',', '.') ?> km</span>
                                    </div>
                                </div>

                                <?php if (!empty($veiculo['preco'])): ?>
                                    <div class="mb-3">
                                        <strong>Preço:</strong><br>
                                        <span class="h4 text-success"><?= formatPrice($veiculo['preco']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($veiculo['descricao'])): ?>
                                    <div class="mb-3">
                                        <strong>Descrição:</strong><br>
                                        <p class="text-muted"><?= nl2br(htmlspecialchars($veiculo['descricao'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="d-grid gap-2">
                                    <a href="index.php" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left"></i> Voltar
                                    </a>
                                    <?php if (isset($_SESSION['usuario_id'])): ?>
                                        <a href="admin/editar_veiculo.php?id=<?= $veiculo['id'] ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Informações Adicionais
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Combustível:</strong></div>
                            <div class="col-6"><?= htmlspecialchars($veiculo['combustivel'] ?? 'Não informado') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Câmbio:</strong></div>
                            <div class="col-6"><?= htmlspecialchars($veiculo['cambio'] ?? 'Não informado') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Portas:</strong></div>
                            <div class="col-6"><?= htmlspecialchars($veiculo['portas'] ?? 'Não informado') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Final da Placa:</strong></div>
                            <div class="col-6"><?= htmlspecialchars($veiculo['final_placa'] ?? 'Não informado') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Data de Cadastro:</strong></div>
                            <div class="col-6"><?= formatDate($veiculo['data_cadastro']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Veículos Relacionados -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-car"></i> Veículos da Mesma Categoria
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql_relacionados = "SELECT v.*, c.nome as categoria_nome FROM veiculos v 
                                            JOIN categorias c ON v.id_categoria = c.id 
                                            WHERE v.id_categoria = :categoria AND v.id != :id 
                                            ORDER BY v.id DESC LIMIT 3";
                        $stmt_relacionados = $pdo->prepare($sql_relacionados);
                        $stmt_relacionados->execute(['categoria' => $veiculo['id_categoria'], 'id' => $veiculo['id']]);
                        $relacionados = $stmt_relacionados->fetchAll();
                        ?>

                        <?php if (empty($relacionados)): ?>
                            <p class="text-muted">Nenhum veículo relacionado encontrado.</p>
                        <?php else: ?>
                            <?php foreach ($relacionados as $rel): ?>
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <?php if (!empty($rel['imagem'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($rel['imagem']) ?>" 
                                                 class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 60px;">
                                                <i class="fas fa-car text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            <a href="veiculo.php?id=<?= $rel['id'] ?>" class="text-decoration-none">
                                                <?= htmlspecialchars($rel['marca'] . ' ' . $rel['modelo']) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($rel['ano']) ?> • <?= htmlspecialchars($rel['cor']) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
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