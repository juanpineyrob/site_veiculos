<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar se o usuário está logado
requireLogin();

$message = '';
$message_type = '';

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: veiculos.php');
    exit();
}

$id = (int)$_GET['id'];

// Buscar o veículo
$stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = ?");
$stmt->execute([$id]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    header('Location: veiculos.php');
    exit();
}

// Buscar categorias para o select
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $placa = cleanInput($_POST['placa']);
    $cor = cleanInput($_POST['cor']);
    $modelo = cleanInput($_POST['modelo']);
    $marca = cleanInput($_POST['marca']);
    $id_categoria = (int)$_POST['id_categoria'];
    $ano = cleanInput($_POST['ano']);
    $quilometragem = (int)$_POST['quilometragem'];
    $preco = !empty($_POST['preco']) ? (float)$_POST['preco'] : null;
    $descricao = cleanInput($_POST['descricao']);
    $combustivel = cleanInput($_POST['combustivel']);
    $cambio = cleanInput($_POST['cambio']);
    $portas = cleanInput($_POST['portas']);
    $final_placa = cleanInput($_POST['final_placa']);
    
    // Validações
    $errors = [];
    
    if (empty($placa)) $errors[] = 'Placa é obrigatória.';
    if (empty($cor)) $errors[] = 'Cor é obrigatória.';
    if (empty($modelo)) $errors[] = 'Modelo é obrigatório.';
    if (empty($marca)) $errors[] = 'Marca é obrigatória.';
    if (empty($id_categoria)) $errors[] = 'Categoria é obrigatória.';
    if (empty($ano)) $errors[] = 'Ano é obrigatório.';
    
    if (empty($errors)) {
        $imagem = $veiculo['imagem']; // Manter a imagem atual
        
        // Upload da nova imagem se fornecida
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $nova_imagem = uploadImage($_FILES['imagem'], '../uploads/');
            if ($nova_imagem) {
                // Deletar a imagem antiga se existir
                if (!empty($veiculo['imagem'])) {
                    deleteImage($veiculo['imagem'], '../uploads/');
                }
                $imagem = $nova_imagem;
            } else {
                $errors[] = 'Erro no upload da nova imagem. Verifique o formato e tamanho.';
            }
        }
        
        if (empty($errors)) {
            $sql = "UPDATE veiculos SET placa = ?, cor = ?, modelo = ?, marca = ?, id_categoria = ?, 
                                        ano = ?, quilometragem = ?, preco = ?, descricao = ?, combustivel = ?, 
                                        cambio = ?, portas = ?, final_placa = ?, imagem = ? 
                    WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $params = [$placa, $cor, $modelo, $marca, $id_categoria, $ano, $quilometragem, 
                      $preco, $descricao, $combustivel, $cambio, $portas, $final_placa, $imagem, $id];
            
            if ($stmt->execute($params)) {
                $message = 'Veículo atualizado com sucesso!';
                $message_type = 'success';
                
                // Atualizar os dados do veículo na variável
                $veiculo = array_merge($veiculo, $_POST);
                $veiculo['imagem'] = $imagem;
            } else {
                $message = 'Erro ao atualizar o veículo.';
                $message_type = 'danger';
            }
        }
    }
    
    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Veículo - Sistema de Veículos</title>
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
                        <i class="fas fa-edit"></i> Editar Veículo
                    </h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="../index.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-home"></i> Início
                    </a>
                    <a href="veiculos.php" class="btn btn-outline-light">
                        <i class="fas fa-list"></i> Lista de Veículos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> alert-dismissible fade show">
                <i class="fas fa-<?= $message_type == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="admin-form">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="placa" class="form-label">Placa *</label>
                                    <input type="text" class="form-control" id="placa" name="placa" 
                                           value="<?= htmlspecialchars($veiculo['placa']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cor" class="form-label">Cor *</label>
                                    <input type="text" class="form-control" id="cor" name="cor" 
                                           value="<?= htmlspecialchars($veiculo['cor']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="marca" class="form-label">Marca *</label>
                                    <input type="text" class="form-control" id="marca" name="marca" 
                                           value="<?= htmlspecialchars($veiculo['marca']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modelo" class="form-label">Modelo *</label>
                                    <input type="text" class="form-control" id="modelo" name="modelo" 
                                           value="<?= htmlspecialchars($veiculo['modelo']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_categoria" class="form-label">Categoria *</label>
                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria['id'] ?>" 
                                                    <?= $veiculo['id_categoria'] == $categoria['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ano" class="form-label">Ano *</label>
                                    <input type="text" class="form-control" id="ano" name="ano" 
                                           value="<?= htmlspecialchars($veiculo['ano']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="quilometragem" class="form-label">Quilometragem</label>
                                    <input type="number" class="form-control" id="quilometragem" name="quilometragem" 
                                           value="<?= htmlspecialchars($veiculo['quilometragem']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco" class="form-label">Preço</label>
                                    <input type="number" step="0.01" class="form-control" id="preco" name="preco" 
                                           value="<?= htmlspecialchars($veiculo['preco']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="combustivel" class="form-label">Combustível</label>
                                    <select class="form-select" id="combustivel" name="combustivel">
                                        <option value="">Selecione</option>
                                        <option value="Gasolina" <?= $veiculo['combustivel'] == 'Gasolina' ? 'selected' : '' ?>>Gasolina</option>
                                        <option value="Etanol" <?= $veiculo['combustivel'] == 'Etanol' ? 'selected' : '' ?>>Etanol</option>
                                        <option value="Flex" <?= $veiculo['combustivel'] == 'Flex' ? 'selected' : '' ?>>Flex</option>
                                        <option value="Diesel" <?= $veiculo['combustivel'] == 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                                        <option value="Elétrico" <?= $veiculo['combustivel'] == 'Elétrico' ? 'selected' : '' ?>>Elétrico</option>
                                        <option value="Híbrido" <?= $veiculo['combustivel'] == 'Híbrido' ? 'selected' : '' ?>>Híbrido</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cambio" class="form-label">Câmbio</label>
                                    <select class="form-select" id="cambio" name="cambio">
                                        <option value="">Selecione</option>
                                        <option value="Manual" <?= $veiculo['cambio'] == 'Manual' ? 'selected' : '' ?>>Manual</option>
                                        <option value="Automático" <?= $veiculo['cambio'] == 'Automático' ? 'selected' : '' ?>>Automático</option>
                                        <option value="CVT" <?= $veiculo['cambio'] == 'CVT' ? 'selected' : '' ?>>CVT</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="portas" class="form-label">Portas</label>
                                    <select class="form-select" id="portas" name="portas">
                                        <option value="">Selecione</option>
                                        <option value="2" <?= $veiculo['portas'] == '2' ? 'selected' : '' ?>>2</option>
                                        <option value="3" <?= $veiculo['portas'] == '3' ? 'selected' : '' ?>>3</option>
                                        <option value="4" <?= $veiculo['portas'] == '4' ? 'selected' : '' ?>>4</option>
                                        <option value="5" <?= $veiculo['portas'] == '5' ? 'selected' : '' ?>>5</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="final_placa" class="form-label">Final da Placa</label>
                                    <input type="text" class="form-control" id="final_placa" name="final_placa" 
                                           value="<?= htmlspecialchars($veiculo['final_placa']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem</label>
                            <?php if (!empty($veiculo['imagem'])): ?>
                                <div class="mb-2">
                                    <img src="../uploads/<?= htmlspecialchars($veiculo['imagem']) ?>" 
                                         class="image-preview" alt="Imagem atual">
                                    <small class="text-muted d-block">Imagem atual</small>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                            <div class="form-text">Deixe em branco para manter a imagem atual. Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 5MB.</div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="4"><?= htmlspecialchars($veiculo['descricao']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="veiculos.php" class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Atualizar Veículo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 