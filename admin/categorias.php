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
    
    // Verificar se há veículos usando esta categoria
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM veiculos WHERE id_categoria = ?");
    $stmt->execute([$id]);
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        $message = 'Não é possível excluir esta categoria pois existem veículos cadastrados nela.';
        $message_type = 'danger';
    } else {
        // Deletar a categoria
        $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
        if ($stmt->execute([$id])) {
            $message = 'Categoria excluída com sucesso!';
            $message_type = 'success';
        } else {
            $message = 'Erro ao excluir a categoria.';
            $message_type = 'danger';
        }
    }
}

// Processar adição/edição
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = cleanInput($_POST['nome']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if (empty($nome)) {
        $message = 'Nome da categoria é obrigatório.';
        $message_type = 'danger';
    } else {
        if ($id > 0) {
            // Editar categoria existente
            $stmt = $pdo->prepare("UPDATE categorias SET nome = ? WHERE id = ?");
            if ($stmt->execute([$nome, $id])) {
                $message = 'Categoria atualizada com sucesso!';
                $message_type = 'success';
            } else {
                $message = 'Erro ao atualizar a categoria.';
                $message_type = 'danger';
            }
        } else {
            // Adicionar nova categoria
            $stmt = $pdo->prepare("INSERT INTO categorias (nome) VALUES (?)");
            if ($stmt->execute([$nome])) {
                $message = 'Categoria adicionada com sucesso!';
                $message_type = 'success';
            } else {
                $message = 'Erro ao adicionar a categoria.';
                $message_type = 'danger';
            }
        }
    }
}

// Buscar categoria para edição
$categoria_edit = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
    $stmt->execute([$id]);
    $categoria_edit = $stmt->fetch();
}

// Buscar todas as categorias
$sql = "SELECT c.*, COUNT(v.id) as total_veiculos FROM categorias c 
        LEFT JOIN veiculos v ON c.id = v.id_categoria 
        GROUP BY c.id 
        ORDER BY c.nome";
$stmt = $pdo->query($sql);
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias - Sistema de Veículos</title>
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
                        <i class="fas fa-tags"></i> Gerenciar Categorias
                    </h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="../index.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-home"></i> Início
                    </a>
                    <a href="veiculos.php" class="btn btn-outline-light">
                        <i class="fas fa-car"></i> Veículos
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

        <div class="row">
            <div class="col-lg-4">
                <div class="admin-form">
                    <h5 class="mb-3">
                        <i class="fas fa-<?= $categoria_edit ? 'edit' : 'plus' ?>"></i>
                        <?= $categoria_edit ? 'Editar Categoria' : 'Adicionar Categoria' ?>
                    </h5>
                    
                    <form method="POST">
                        <?php if ($categoria_edit): ?>
                            <input type="hidden" name="id" value="<?= $categoria_edit['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome da Categoria *</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?= $categoria_edit ? htmlspecialchars($categoria_edit['nome']) : '' ?>" 
                                   required>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                <?= $categoria_edit ? 'Atualizar' : 'Adicionar' ?>
                            </button>
                            
                            <?php if ($categoria_edit): ?>
                                <a href="categorias.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list"></i> Lista de Categorias
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Total de Veículos</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($categorias)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="fas fa-info-circle text-muted"></i>
                                                Nenhuma categoria cadastrada.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <tr>
                                                <td><?= $categoria['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($categoria['nome']) ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info"><?= $categoria['total_veiculos'] ?> veículos</span>
                                                </td>
                                                <td>
                                                    <a href="?edit=<?= $categoria['id'] ?>" 
                                                       class="btn btn-sm btn-edit" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($categoria['total_veiculos'] == 0): ?>
                                                        <a href="?delete=<?= $categoria['id'] ?>" 
                                                           class="btn btn-sm btn-delete" 
                                                           onclick="return confirm('Tem certeza que deseja excluir esta categoria?')"
                                                           title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 