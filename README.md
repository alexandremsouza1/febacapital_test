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
 ```bash
php yii create-user --login="alexandre123" --password="123" --name="Alexandre"
```


- `POST /login`: Autenticação de usuários  
  `Content-Type: application/x-www-form-urlencoded`  
  **Corpo da Requisição:**
  ```json
  {
      "login": "alexandre123",
      "password": "123"
  }

- `POST/GET /api/customers`: Listagem de clientes
    - **Parâmetros:**
    - `name` (opcional): Filtro por nome
    - `cpf` (opcional): Filtro por CPF
    - `sort` (opcional): Ordenar por nome
    - `limit` (opcional): Número máximo de registros
    - `offset` (opcional): Pular registros

- `POST/GET /books`: Listagem de livros
    - **Parâmetros:**
    - `isbn` (opcional): Filtro por ISBN
    - `title` (opcional): Filtro por título
    - `author` (opcional): Filtro por autor
    - `sort` (opcional): Ordenar por nome
    - `limit` (opcional): Número máximo de registros
    - `offset` (opcional): Pular registros


### Exemplos de Uso


- **Requisição com Autenticação**

    ```bash
    curl -X POST http://localhost:8000/login \
        -H "Content-Type: application/x-www-form-urlencoded" \
        -d "login=alexandre123&password=123"
    ```

    ```bash
    curl -X GET "http://localhost:8000/customers?name=Alexandre&cpf=12345678900&sort=name&limit=10&offset=0" \
        -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
    ```

    ```bash
        curl -X GET "http://localhost:8000/books?isbn=9788545702870&title=Clean%20Code&author=Robert%20C.%20Martin&sort=title&limit=10&offset=0" \
    -H "Authorization: Bearer <YOUR_ACCESS_TOKEN>"
    ```

