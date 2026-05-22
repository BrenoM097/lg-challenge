# Dashboard de Produtividade Industrial com IA (Gemini)

Desafio técnico proposto pela LG Eletronics.

Este projeto é um sistema de monitoramento de métricas de produção fabril integrado à Inteligência Artificial (Google Gemini) para análise preditiva de riscos. O objetivo é consolidar dados diários de volume de fabricação e contagem de defeitos por linha de produto, permitindo que gestores realizem simulações preditivas de cenários e analisem riscos operacionais e gargalos de forma automatizada.

---

## 🚀 Como Executar o Projeto Localmente (Sem Docker)

### Pré-requisitos
* PHP 7.4 (ou superior compatível com a versão do framework)
* Composer instalado globalmente
* Banco de dados MySQL instalado e ativo, de preferência versão 8
* Extensões PHP obrigatórias ativas (`pdo_mysql`, `mbstring`, `xml`, `openssl`)

### Passo a Passo

1. **Clonar o Repositório e Acessar a Pasta:**
   ```bash
   git clone git@github.com:BrenoM097/lg-challenge.git
   cd lg-challenge
   ```

2. **Instalar as Dependências do PHP:**
   ```bash
   composer install
   ```

3. **Configurar o Arquivo de Ambiente:**
   Copie o arquivo de exemplo `.env.example` para criar o seu `.env`:
   ```bash
   cp .env.example .env
   ```
   Abra o arquivo `.env` criado e configure as credenciais do seu banco de dados local MySQL e a chave da API do Gemini(opcional):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nome_do_seu_banco
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha

   GEMINI_API_KEY=sua_chave_da_api_aqui
   ```

4. **Gerar a Chave da Aplicação:**
   ```bash
   php artisan key:generate
   ```

5. **Executar as Migrations (Criação das Tabelas):**
   ```bash
   php artisan migrate
   ```

6. **Iniciar o Servidor Local:**
   ```bash
   php artisan serve
   ```
   A aplicação estará disponível em: `http://localhost:8000`

---

## 🐳 Como Executar o Projeto Localmente com Docker (Recomendado)

### Pré-requisitos
* Docker instalado
* Docker Compose instalado

### Passo a Passo

1. **Clonar o Repositório e Acessar a Pasta:**
   ```bash
   git clone git@github.com:BrenoM097/lg-challenge.git
   cd lg-challenge
   ```

2. **Criando arquivo .env:**
   Crie o arquivo .env a partir do modelo usando o terminal da sua maquina::
   ```bash
   cp .env.example .env
   ```
3. **Configurar o Arquivo de Ambiente:**
   Abra o arquivo `.env` e configure o `DB_HOST` para apontar para o serviço do banco do Docker, além de preencher a credencial da IA(opcional):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=root

   GEMINI_API_KEY=sua_chave_da_api_aqui
   ```
4. **Subir os Containers do Docker:**
   Execute o comando abaixo para construir e iniciar os servicos em segundo plano:
   ```bash
   docker compose up -d
   ```
5. **Instalando dependencias:**
   ```bash
   docker compose exec app composer install
   ```

6. **Gerar a Chave da Aplicacao:**
   ```bash
   docker compose exec app php artisan key:generate
   ```
7. **Executar as Migrations:**
   ```bash
   docker compose exec app php artisan migrate
   ```
   A aplicação estará disponível na porta mapeada no seu arquivo compose (ex: `http://localhost:8080`).

---

## 📊 Estrutura da Tabela do Banco de Dados

A aplicação utiliza a tabela `productivities` para armazenar o histórico de fabricação diária. Abaixo está a estrutura DDL (Data Definition Language) utilizada para criar a tabela diretamente no MySQL:

```sql
CREATE TABLE `productivities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `production_unit` varchar(255) NOT NULL,
  `product_line` varchar(255) NOT NULL,
  `produced_quantity` int(11) NOT NULL,
  `defect_count` int(11) NOT NULL,
  `production_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 📥 Massa de Dados para Testes Simultâneos (SQL INSERT manual)

Caso queira povoar o banco manualmente via terminal ou cliente SQL (DBeaver, MySQL Workbench, etc.), você pode rodar o script abaixo para simular o comportamento de Janeiro de 2026:

```sql
INSERT INTO `productivities` (`production_unit`, `product_line`, `produced_quantity`, `defect_count`, `production_date`, `created_at`, `updated_at`) VALUES
('Planta A', 'TV', 150, 2, '2026-01-14', NOW(), NOW()),
('Planta A', 'TV', 145, 42, '2026-01-15', NOW(), NOW()),
('Planta A', 'Geladeira', 80, 1, '2026-01-15', NOW(), NOW()),
('Planta A', 'Geladeira', 88, 12, '2026-01-22', NOW(), NOW()),
('Planta A', 'Máquina de Lavar', 70, 0, '2026-01-15', NOW(), NOW()),
('Planta A', 'Ar-Condicionado', 55, 1, '2026-01-15', NOW(), NOW());
```

---

## Fórmula de eficiência

Eficiência (%) = ((Produzido − Defeitos) / Produzido) × 100

Escolha de nivel de qualidade, OEE (Overall Equipment Effectiveness)

    ≥ 95%: Ótimo
    85% – 94,9%: Regular
    < 85%: Crítico

---

## Testes Automatizados

O projeto possui uma suíte de testes automatizados construídos com PHPUnit e os recursos de validação do Laravel para garantir a estabilidade das principais rotas e regras de negócio.

Como executar os testes:

Se estiver rodando sem Docker, utilize o comando:
```bash
   php artisan test
```
Se estiver utilizando o Docker, utilize o comando:
```bash
   docker compose exec app php artisan test
```
O que está sendo testado:

DashboardTest (Teste de Interface e Renderização):
Garante que a rota principal do dashboard carrega com sucesso (HTTP 200). Ele também cria registros temporários no banco de dados e verifica se o código HTML gerado pela view exibe os nomes dos produtos e as quantidades corretas na tabela.

AiSimulationTest (Teste de Integração com a IA):
Testa o fluxo de envio e resposta da simulação preditiva. Para que o teste rode rápido, não dependa de internet e não consuma requisições reais da sua chave do Google Gemini, o teste utiliza um Mock (Http::fake) que intercepta a chamada para a API do Google. Ele simula uma resposta de sucesso da inteligência artificial e verifica se o Controller processa e devolve o formato JSON exato que o seu frontend espera receber.
