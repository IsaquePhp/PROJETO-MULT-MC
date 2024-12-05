# API Loja MC

Sistema completo de gerenciamento de loja com backend em Laravel e frontend em Vue.js, incluindo controle de produtos, vendas, estoque e usuários.

## Tecnologias Utilizadas

### Backend
- Laravel 8.x
- Laravel Sanctum para autenticação
- Spatie Laravel-Permission para controle de acesso
- MySQL para banco de dados

### Frontend
- Vue.js 3
- Vue Router para navegação
- Pinia para gerenciamento de estado
- Tailwind CSS para estilização
- Vite como bundler

## Requisitos

### Backend
- PHP >= 8.0
- Composer
- MySQL
- XAMPP ou servidor web similar

### Frontend
- Node.js >= 16
- NPM ou Yarn

## Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/IsaquePhp/api-loja-mc.git
cd api-loja-mc
```

### 2. Configuração do Backend

```bash
# Instale as dependências do PHP
composer install

# Configure o arquivo .env
cp .env.example .env
# Edite o arquivo .env com suas configurações de banco de dados

# Gere a chave da aplicação
php artisan key:generate

# Execute as migrações e seeders
php artisan migrate --seed

# Inicie o servidor (se não estiver usando XAMPP)
php artisan serve
```

### 3. Configuração do Frontend

```bash
# Entre na pasta do frontend
cd frontend

# Instale as dependências
npm install

# Inicie o servidor de desenvolvimento
npm run dev
```

## Acessando o Sistema

### Backend API
- URL: `http://localhost:8000` ou `http://localhost/API LOJA MC` (XAMPP)
- Documentação da API disponível na coleção do Postman incluída no projeto

### Frontend
- URL: `http://localhost:5173`
- Credenciais padrão:
  - Email: admin@admin.com
  - Senha: password

## Estrutura do Projeto

### Backend (Laravel)
- `app/` - Classes principais do Laravel
- `routes/api.php` - Definição das rotas da API
- `database/` - Migrações e seeders
- `config/` - Arquivos de configuração

### Frontend (Vue.js)
- `frontend/src/views/` - Componentes de página
- `frontend/src/components/` - Componentes reutilizáveis
- `frontend/src/stores/` - Stores Pinia
- `frontend/src/router/` - Configuração de rotas

## Principais Funcionalidades

- Dashboard com resumo de vendas e estoque
- Gerenciamento de produtos
- Controle de estoque
- Registro de vendas
- Relatórios
- Sistema de autenticação
- Interface responsiva

## Atualizações e Contribuições

1. Crie um branch para sua feature
```bash
git checkout -b feature/nome-da-feature
```

2. Faça commit das alterações
```bash
git add .
git commit -m "feat: descrição da sua feature"
```

3. Envie para o repositório
```bash
git push origin feature/nome-da-feature
```

## Segurança

- Autenticação via Laravel Sanctum
- Sistema de permissões para controle de acesso
- Validação de dados em todas as requisições
- Proteção contra CSRF
- Sanitização de inputs

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).
