# Desafio Técnico Febacapital - Yii2

## Descrição
Este projeto é uma API RESTful desenvolvida em Yii2 para gerenciar um catálogo de livros e clientes.

## Instalação

1. **Clone o repositório:**


2. **Suba o projeto usando Docker Compose:**
    ```bash
    docker-compose up -d
    ```
    Isso fará com que a API esteja rodando na porta `8000` e o banco de dados na porta `3306`.

## Execução

1. **Acesse o container da API:**
    ```bash
    docker exec -it <NOME-DO-CONTAINER-API> /bin/sh
    ```

2. **Instale as dependências do Composer:**
    ```bash
    composer install
    ```

3. **Execute as migrations:**
    ```bash
    php yii migrate
    ```

## Especificações

O projeto foi desenvolvido seguindo boas práticas de desenvolvimento e utiliza tecnologias modernas, incluindo PHP 8.3 e MySQL 8.

## Documentação

### Criação de Usuário

Para criar um usuário via linha de comando, utilize:
php yii create-user --login="alexandre123" --password="123" --name="Alexandre"


### Endpoints da API

- **POST /login**: Autenticação de usuários  
  `Content-Type: application/x-www-form-urlencoded`  
  **Corpo da Requisição:**
  ```json
  {
      "login": "alexandre123",
      "password": "123"
  }
  ```
  - **GET /GET**: Listagem de clientes
  ```json
    Parâmetros:
        name (opcional): Filtro por nome
        cpf (opcional): Filtro por CPF
        sort (opcional): Ordenar por nome
        limit (opcional): Número máximo de registros
        offset (opcional): Pular registros
    ```
    **GET /books**: Listagem de livros
    ```json
    Parâmetros:
        isbn (opcional): Filtro por ISBN
        title (opcional): Filtro por título
        author (opcional): Filtro por autor
        sort (opcional): Ordenar por nome
        limit (opcional): Número máximo de registros
        offset (opcional): Pular registros
    ```

    ### Exemplos

        Listar todos os clientes:
         ```bash
        GET /customers
        ```
        
        Listar clientes com filtro de nome:
         ```bash
        GET /customers?name=John
         ```

        Listar livros com filtro de ISBN:
         ```bash
        GET /books?isbn=9999999
        ```

        Listar livros com filtro de autor e ordenação:
         ```bash
        GET /books?author=John&sort=name
         ```
         
        ```bash
        Listar clientes com paginação (10 clientes por página, começando do 1º):
        GET /customers?limit=10&offset=1
         ```