<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário está logado
requireLogin();

$message = '';
$message_type = '';

// Processar exclusão
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Buscar o veículo para deletar a imagem
    $stmt = $pdo->prepare("SELECT imagem FROM veiculos WHERE id = ?");
    $stmt->execute([$id]);
    $veiculo = $stmt->fetch();
    
    if ($veiculo && !empty($veiculo['imagem'])) {
        deleteImage($veiculo['imagem'], '../uploads/');
    }
    
    // Deletar o veículo
    $stmt = $pdo->prepare("DELETE FROM veiculos WHERE id = ?");
    if ($stmt->execute([$id])) {
        $message = 'Veículo excluído com sucesso!';
        $message_type = 'success';
    } else {
        $message = 'Erro ao excluir o veículo.';
        $message_type = 'danger';
    }
}

// Buscar todos os veículos
$sql = "SELECT v.*, c.nome as categoria_nome FROM veiculos v 
        JOIN categorias c ON v.id_categoria = c.id 
        ORDER BY v.id DESC";
$stmt = $pdo->query($sql);
$veiculos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Veículos - Sistema de Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-car"></i> Gerenciar Veículos
                    </h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="../index.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-home"></i> Início
                    </a>
                    <a href="adicionar_veiculo.php" class="btn btn-success">
                        <i class="fas fa-plus"></i> Adicionar Veículo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
                <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list"></i> Lista de Veículos
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagem</th>
                                <th>Marca/Modelo</th>
                                <th>Placa</th>
                                <th>Categoria</th>
                                <th>Ano</th>
                                <th>Preço</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($veiculos)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-info-circle text-muted"></i>
                                        Nenhum veículo cadastrado.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($veiculos as $veiculo): ?>
                                    <tr>
                                        <td><?= $veiculo['id'] ?></td>
                                        <td>
                                            <?php if (!empty($veiculo['imagem'])): ?>
                                                <img src="../uploads/<?= htmlspecialchars($veiculo['imagem']) ?>" 
                                                     class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-car text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($veiculo['marca']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($veiculo['modelo']) ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($veiculo['placa']) ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= htmlspecialchars($veiculo['categoria_nome']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($veiculo['ano']) ?></td>
                                        <td>
                                            <?php if (!empty($veiculo['preco'])): ?>
                                                <span class="text-success fw-bold"><?= formatPrice($veiculo['preco']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Não informado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="../veiculo.php?id=<?= $veiculo['id'] ?>" 
                                               class="btn btn-sm btn-view" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="editar_veiculo.php?id=<?= $veiculo['id'] ?>" 
                                               class="btn btn-sm btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?= $veiculo['id'] ?>" 
                                               class="btn btn-sm btn-delete" 
                                               onclick="return confirm('Tem certeza que deseja excluir este veículo?')"
                                               title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 