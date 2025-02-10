# **OnFlyTrips - API de Pedidos de Viagem**

## 📌 **Descrição**

Este projeto é um microsserviço desenvolvido em **Laravel** para gerenciar pedidos de viagens corporativas. A API segue as boas práticas de arquitetura de microsserviços e inclui autenticação JWT, validação de dados, filtros avançados e notificações.

---

## 📌 **Instalação**

### **1️⃣ Clonar o Repositório**

```sh
git clone https://github.com/seu-usuario/onflytrips.git
cd onflytrips
```

### **2️⃣ Instalar Dependências**

```sh
composer install
npm install
```

### **3️⃣ Configurar o Ambiente**

Crie o arquivo `.env` e configure as credenciais do banco de dados:

```sh
cp .env.example .env
```

Edite as seguintes variáveis no `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=onflytrips
DB_USERNAME=root
DB_PASSWORD=
```

### **4️⃣ Gerar a Chave da Aplicação**

```sh
php artisan key:generate
```

### **5️⃣ Criar as Tabelas no Banco de Dados**

```sh
php artisan migrate
```

### **6️⃣ Iniciar o Servidor**

```sh
php artisan serve
```

A API estará disponível em `http://localhost:8000`

---

## 📌 **Autenticação (JWT)**

A API usa autenticação JWT para proteger as rotas. Utilize o **token** retornado ao fazer login para acessar as rotas protegidas.

### **1️⃣ Registrar Usuário**

**POST** `/api/register`

```json
{
  "name": "Arthur Vilefort",
  "email": "arthur@example.com",
  "password": "123456"
}
```

### **2️⃣ Login**

**POST** `/api/login`

```json
{
  "email": "arthur@example.com",
  "password": "123456"
}
```

### **3️⃣ Visualizar Perfil**

**GET** `/api/profile` **Headers:**

```
Authorization: Bearer SEU_TOKEN_AQUI
```

### **4️⃣ Logout**

**POST** `/api/logout` **Headers:**

```
Authorization: Bearer SEU_TOKEN_AQUI
```

---

## 📌 **Pedidos de Viagem**

### **1️⃣ Criar Pedido de Viagem**

**POST** `/api/trips` **Headers:**

```
Authorization: Bearer SEU_TOKEN_AQUI
```

**Body:**

```json
{
  "destination": "Lisboa, Portugal",
  "departure_date": "2025-03-15",
  "return_date": "2025-03-22"
}
```

### **2️⃣ Listar Todos os Pedidos**

**GET** `/api/trips`

### **3️⃣ Consultar Pedido por ID**

**GET** `/api/trips/{id}`

### **4️⃣ Atualizar Status de um Pedido**

**PUT** `/api/trips/{id}/status` **Body:**

```json
{
  "status": "aprovado" ou "cancelado"
}
```

🚨 **Nota:** O usuário que criou o pedido **não pode** aprová-lo ou cancelá-lo.

---

## 📌 **Filtros**

### **1️⃣ Filtrar por Status**

**GET** `/api/trips?status=aprovado`

### **2️⃣ Filtrar por Destino**

**GET** `/api/trips?destination=Lisboa`

### **3️⃣ Filtrar por Período de Tempo**

**GET** `/api/trips?start_date=2025-03-10&end_date=2025-03-30`

---

## 📌 **Notificações**

Sempre que um pedido for **aprovado ou cancelado**, uma notificação será enviada ao solicitante do pedido.

---

## 📌 **Testes Automatizados**

Para rodar os testes com PHPUnit:

```sh
php artisan test
```

---

## 📌 **Docker**

A aplicação pode ser executada via **Docker**. Para subir os containers:

```sh
docker-compose up -d --build
```

Se necessário, copie o `.env.example` para `.env` e configure as credenciais do banco de dados.

---

## 📌 **Conclusão**

Este microsserviço segue as melhores práticas de desenvolvimento com **Laravel**, incluindo autenticação JWT, validação de dados, tratamento de erros, testes automatizados e execução via Docker.

📌 **Autor:** Arthur Vilefort

🚀 **OnFlyTrips** - Gerenciamento de Pedidos de Viagem Corporativa

