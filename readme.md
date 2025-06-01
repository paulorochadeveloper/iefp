# Projeto de Loja Virtual em PHP

## Descrição do Projeto
Este é um projeto básico de loja virtual desenvolvido em PHP como parte da UFCD ministrada por João Monge. O sistema permite a gestão de produtos e carrinhos de compras, com funcionalidades essenciais para uma loja online simples.

---

## Funcionalidades Principais

### Gestão de Produtos
- Cadastro de produtos com nome, descrição, preço e imagem
- Listagem de produtos disponíveis
- Visualização de detalhes dos produtos

### Carrinho de Compras
- Adição/remoção de produtos ao carrinho
- Ajuste de quantidades dos produtos
- Cálculo automático do valor total

### Usuários
- Sistema básico de autenticação (login/logout)
- Associação de carrinhos a usuários específicos

---

## Estrutura do Banco de Dados (MySQL)

### `produto`
- `id` (INT)
- `nome` (VARCHAR)
- `descricao` (TEXT)
- `preco` (DECIMAL)
- `imagem` (LONGBLOB)

### `carrinho`
- `id` (INT)
- `userId` (INT)
- `produtoId` (INT)
- `quantidade` (INT)

---

## Requisitos do Sistema
- Servidor web (Apache ou Nginx)
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensão PDO habilitada no PHP

---

## 🚀 Instalação

1. Clone o repositório para seu servidor web:
   ```bash
   git clone git@github.com:paulorochadeveloper/iefp.git
