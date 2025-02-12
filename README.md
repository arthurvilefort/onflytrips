# **OnFlyTrips - API de Pedidos de Viagem** 🚀

## 📌 **Descrição**

OnFlyTrips é um microsserviço desenvolvido em **Laravel** para gerenciar pedidos de viagens corporativas. A API segue boas práticas de **arquitetura de microsserviços** e inclui:

✅ **Autenticação JWT**  
✅ **Criação e gerenciamento de pedidos de viagem**  
✅ **Validações e regras de negócios para cancelamento e aprovação**  
✅ **Notificações via banco de dados e e-mail**  
✅ **Testes automatizados com PHPUnit**  
✅ **Execução via Docker**  

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

Adicione a configuração para envio de e-mails:

```ini
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario
MAIL_PASSWORD=sua_senha
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=onflytrips@example.com
MAIL_FROM_NAME="OnFlyTrips"
```

### **4️⃣ Gerar a Chave da Aplicação**

```sh
php artisan key:generate
```

### 5️⃣ Gerar Chave JWT

Execute o seguinte comando para gerar a chave JWT necessária para autenticação:

```sh
php artisan jwt:secret
```

### **5️⃣ Criar as Tabelas no Banco de Dados**

```sh
php artisan migrate --seed
```

### **6️⃣ Iniciar o Servidor**

```sh
php artisan serve
```

A API estará disponível em **`http://localhost:8000`**.

---

## 📌 **Autenticação (JWT)**

A API usa autenticação JWT para proteger as rotas. Utilize o **token** retornado ao fazer login para acessar as rotas protegidas.

## 📌 **Docker**

A aplicação pode ser executada via **Docker**.

### **1️⃣ Criar os Containers**
```sh
docker-compose up -d --build
```

### **2️⃣ Criar as Tabelas**
```sh
docker-compose exec app php artisan migrate --seed
```

### **3️⃣ Acessar a Aplicação**
A API estará disponível em **`http://localhost:8000`**.

---
## 📌 **Acesso**
### **1️⃣ Registrar Usuário**

**POST** `/api/register`

```json
{
  "name": "Arthur Vilefort",
  "email": "arthur@example.com",
  "password": "123456",
  "password_confirmation": "123456"
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

**Resposta:**  
```json
{
  "token": "seu_token_aqui"
}
```

### **3️⃣ Visualizar Perfil**

**GET** `/api/profile`  
**Headers:**
```
Authorization: Bearer SEU_TOKEN_AQUI
```

### **4️⃣ Logout**

**POST** `/api/logout`  
**Headers:**
```
Authorization: Bearer SEU_TOKEN_AQUI
```

---

## 📌 **Gerenciamento de Usuários (Admins)**

### **1️⃣ Promover Usuário a Admin**
**PUT** `/api/users/{id}/make-admin`  
**Headers:**
```
Authorization: Bearer SEU_TOKEN_AQUI (de um admin)
```

### **2️⃣ Remover Permissão de Admin**
**PUT** `/api/users/{id}/remove-admin`  
**Headers:**
```
Authorization: Bearer SEU_TOKEN_AQUI (de um admin)
```

---

## 📌 **Pedidos de Viagem**

### **1️⃣ Criar Pedido de Viagem**
**POST** `/api/trips`  
**Headers:**
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
🔹 **Admins** podem ver todos os pedidos.  
🔹 **Usuários comuns** só veem os seus próprios.

### **3️⃣ Consultar Pedido por ID**
**GET** `/api/trips/{id}`  

### **4️⃣ Atualizar Status de um Pedido**
**PUT** `/api/trips/{id}/status`  
🔹 Somente **admins** podem aprovar/cancelar pedidos.  
🔹 O **usuário que criou o pedido não pode alterá-lo**.  

**Body:**
```json
{
  "status": "aprovado" ou "solicitado"
}
```

### **5️⃣ Cancelar Pedido**
**DELETE** `/api/trips/{id}`  
🔹 **O próprio usuário pode cancelar um pedido desde que a data da viagem esteja a mais de 3 dias**.  
🔹 **Admins podem cancelar pedidos aprovados com pelo menos 3 dias de antecedência**.  
🔹 **Se a viagem estiver muito próxima, não pode ser cancelada**.  

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

Sempre que um pedido for **aprovado ou cancelado**, uma notificação será enviada ao solicitante do pedido **(Banco de Dados e E-mail)**.

### **1️⃣ Consultar Notificações**
**GET** `/api/notifications`  
**Headers:**
```
Authorization: Bearer SEU_TOKEN_AQUI
```

---

## 📌 **Testes Automatizados**

O projeto inclui **testes automatizados com PHPUnit**.

```sh
php artisan test
```

**Testes Implementados:**
- ✅ **Autenticação**
- ✅ **Criação de Pedidos**
- ✅ **Listagem e Filtros**
- ✅ **Cancelamento**
- ✅ **Permissões de Admin**
- ✅ **Notificações**

---


## 📌 **Conclusão**

Este microsserviço segue **boas práticas de desenvolvimento com Laravel**, garantindo segurança, eficiência e organização.

📌 **Autor:** Arthur Vilefort  
🚀 **OnFlyTrips** - Gerenciamento de Pedidos de Viagem Corporativa
