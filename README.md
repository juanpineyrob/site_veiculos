# Sistema de Veículos

Um sistema completo para gerenciamento e oferta de veículos desenvolvido em PHP com interface moderna e responsiva.

## 🚗 Funcionalidades

### Para Visitantes
- **Página Inicial**: Visualização de todos os veículos disponíveis
- **Busca por Modelo**: Pesquisar veículos por modelo
- **Busca por Ano**: Pesquisar veículos por ano
- **Filtro por Categoria**: Visualizar veículos por categoria específica
- **Detalhes do Veículo**: Página completa com todas as informações do veículo
- **Veículos Relacionados**: Sugestões de veículos da mesma categoria

### Para Administradores (Usuários Logados)
- **Gestão de Veículos**: Adicionar, editar e excluir veículos
- **Gestão de Categorias**: Adicionar, editar e excluir categorias
- **Upload de Imagens**: Sistema de upload de imagens para os veículos
- **Painel Administrativo**: Interface intuitiva para gerenciamento

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3.0
- **Ícones**: Font Awesome 6.0.0
- **Conexão**: PDO (PHP Data Objects)

## 📋 Requisitos do Sistema

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL, GD (para processamento de imagens)

## 🚀 Instalação

### 1. Configuração do Banco de Dados

1. Crie um banco de dados MySQL
2. Execute o script `database.sql` para criar as tabelas e dados iniciais:

```sql
mysql -u root -p < database.sql
```

### 2. Configuração da Aplicação

1. Clone ou baixe os arquivos do projeto
2. Configure a conexão com o banco de dados no arquivo `config/database.php`:

```php
$host = 'localhost';
$dbname = 'sistema_veiculos';
$username = 'seu_usuario';
$password = 'sua_senha';
```

3. Certifique-se de que o diretório `uploads/` tenha permissões de escrita:

```bash
chmod 755 uploads/
```

### 3. Acesso ao Sistema

- **URL**: Acesse o sistema através do seu servidor web
- **Login Padrão**:
  - Email: `admin@sistema.com`
  - Senha: `admin123`

## 📁 Estrutura do Projeto

```
site_veiculos/
├── admin/
│   ├── veiculos.php          # Gestão de veículos
│   ├── categorias.php        # Gestão de categorias
│   ├── adicionar_veiculo.php # Adicionar veículo
│   └── editar_veiculo.php    # Editar veículo
├── assets/
│   └── css/
│       └── style.css         # Estilos personalizados
├── config/
│   └── database.php          # Configuração do banco
├── includes/
│   └── functions.php         # Funções auxiliares
├── uploads/                  # Diretório de imagens
├── database.sql              # Script do banco de dados
├── index.php                 # Página inicial
├── login.php                 # Página de login
├── logout.php                # Logout
├── veiculo.php               # Detalhes do veículo
└── README.md                 # Este arquivo
```

## 🗄️ Estrutura do Banco de Dados

### Tabela: usuarios
- `id` - ID único do usuário
- `nome` - Nome completo
- `email` - Email (único)
- `senha` - Senha criptografada
- `data_cadastro` - Data de cadastro

### Tabela: categorias
- `id` - ID único da categoria
- `nome` - Nome da categoria (único)

### Tabela: veiculos
- `id` - ID único do veículo
- `placa` - Placa do veículo (única)
- `cor` - Cor do veículo
- `modelo` - Modelo do veículo
- `marca` - Marca do veículo
- `id_categoria` - ID da categoria (FK)
- `ano` - Ano do veículo
- `quilometragem` - Quilometragem
- `preco` - Preço (opcional)
- `descricao` - Descrição detalhada
- `combustivel` - Tipo de combustível
- `cambio` - Tipo de câmbio
- `portas` - Número de portas
- `final_placa` - Final da placa
- `imagem` - Nome do arquivo de imagem
- `data_cadastro` - Data de cadastro

## 🎨 Características da Interface

- **Design Responsivo**: Funciona em desktop, tablet e mobile
- **Interface Moderna**: Utiliza Bootstrap 5 com design atual
- **Animações**: Efeitos suaves e transições
- **Ícones**: Font Awesome para melhor experiência visual
- **Gradientes**: Design com gradientes modernos
- **Cards**: Layout em cards para melhor organização

## 🔒 Segurança

- **Senhas Criptografadas**: Utiliza `password_hash()` para criptografia
- **Validação de Entrada**: Função `cleanInput()` para limpeza de dados
- **Prepared Statements**: Prevenção contra SQL Injection
- **Controle de Sessão**: Sistema de login/logout seguro
- **Validação de Upload**: Verificação de tipos e tamanhos de arquivo

## 📱 Funcionalidades de Busca

- **Busca por Modelo**: Pesquisa parcial no nome do modelo
- **Busca por Ano**: Pesquisa por ano específico
- **Filtro por Categoria**: Visualização por categoria
- **Resultados Dinâmicos**: Atualização em tempo real dos resultados

## 🖼️ Sistema de Imagens

- **Upload Seguro**: Validação de tipos e tamanhos
- **Redimensionamento**: Processamento automático
- **Nomes Únicos**: Evita conflitos de nomes
- **Fallback**: Ícone padrão quando não há imagem

## 🔧 Personalização

O sistema pode ser facilmente personalizado através de:

- **CSS**: Arquivo `assets/css/style.css`
- **Categorias**: Adicionar novas categorias via admin
- **Campos**: Modificar campos dos veículos no banco
- **Temas**: Alterar cores e estilos no CSS

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
- Verifique as credenciais em `config/database.php`
- Certifique-se de que o MySQL está rodando
- Verifique se o banco `sistema_veiculos` existe

### Erro de Upload de Imagem
- Verifique as permissões do diretório `uploads/`
- Certifique-se de que a extensão GD está habilitada
- Verifique o limite de upload no PHP

### Página em Branco
- Verifique os logs de erro do PHP
- Certifique-se de que todas as extensões estão habilitadas
- Verifique a sintaxe dos arquivos PHP

## 📞 Suporte

Para dúvidas ou problemas:

1. Verifique este README
2. Consulte os logs de erro
3. Verifique a documentação do PHP e MySQL

## 📄 Licença

Este projeto é de uso livre para fins educacionais e comerciais.

---

**Desenvolvido com ❤️ em PHP** 