-- Script para criar o banco de dados e tabelas do Sistema de Veículos

-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS sistema_veiculos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar o banco de dados
USE sistema_veiculos;

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Criar tabela de categorias
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE
);

-- Criar tabela de veículos
CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(10) NOT NULL UNIQUE,
    cor VARCHAR(30) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    marca VARCHAR(50) NOT NULL,
    id_categoria INT NOT NULL,
    ano VARCHAR(4) NOT NULL,
    quilometragem INT DEFAULT 0,
    preco DECIMAL(10,2) NULL,
    descricao TEXT NULL,
    combustivel VARCHAR(20) NULL,
    cambio VARCHAR(20) NULL,
    portas VARCHAR(2) NULL,
    final_placa VARCHAR(1) NULL,
    imagem VARCHAR(255) NULL,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id) ON DELETE RESTRICT
);

-- Inserir categorias padrão
INSERT INTO categorias (nome) VALUES 
('Caminhões'),
('Camionetes'),
('SUV'),
('Veículos de Passeio'),
('Motos'),
('Utilitários');

-- Inserir usuário administrador padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha) VALUES 
('Administrador', 'admin@sistema.com', '$2y$10$TqmMaQDJT5/9s4lRWhLPX.nBTViTl/D8vyqf0wxmWaUM40Grvmn06');

-- Inserir alguns veículos de exemplo
INSERT INTO veiculos (placa, cor, modelo, marca, id_categoria, ano, quilometragem, preco, descricao, combustivel, cambio, portas, final_placa) VALUES 
('ABC1234', 'Branco', 'Civic', 'Honda', 4, '2020', 45000, 85000.00, 'Veículo em excelente estado, revisado recentemente.', 'Flex', 'Automático', '4', '4'),
('DEF5678', 'Prata', 'Corolla', 'Toyota', 4, '2019', 32000, 78000.00, 'Carro conservado, único dono.', 'Flex', 'Automático', '4', '8'),
('GHI9012', 'Preto', 'HR-V', 'Honda', 3, '2021', 28000, 95000.00, 'SUV compacto, ideal para cidade.', 'Flex', 'Automático', '5', '2'),
('JKL3456', 'Azul', 'Ranger', 'Ford', 2, '2018', 65000, 120000.00, 'Picape robusta, ideal para trabalho.', 'Diesel', 'Manual', '4', '6'),
('MNO7890', 'Vermelho', 'CG 150', 'Honda', 5, '2022', 15000, 8500.00, 'Moto econômica, baixo consumo.', 'Gasolina', 'Manual', '0', '0'),
('PQR1234', 'Cinza', 'Sprinter', 'Mercedes-Benz', 1, '2017', 120000, 180000.00, 'Caminhão para carga, em ótimo estado.', 'Diesel', 'Manual', '2', '4');

-- Criar índices para melhor performance
CREATE INDEX idx_veiculos_categoria ON veiculos(id_categoria);
CREATE INDEX idx_veiculos_marca ON veiculos(marca);
CREATE INDEX idx_veiculos_modelo ON veiculos(modelo);
CREATE INDEX idx_veiculos_ano ON veiculos(ano);
CREATE INDEX idx_usuarios_email ON usuarios(email); 