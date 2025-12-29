<div align="center">

# 📅 Sistema de Agendamentos

### Plataforma completa para gerenciamento de agendamentos desenvolvida com CodeIgniter 4

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.6.4-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)](https://codeigniter.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-4.6-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

[Funcionalidades](#-funcionalidades) •
[Arquitetura](#-arquitetura) •
[Instalação](#-instalação) •
[Screenshots](#-screenshots) •
[Tecnologias](#-tecnologias)

</div>

---

## 🎯 Sobre o Projeto

Sistema de agendamentos **full-stack** desenvolvido com boas práticas de engenharia de software, incluindo:

- **Arquitetura em camadas** (MVC + Service Layer)
- **Autenticação completa** com sessões seguras
- **CRUD completo** com validação server-side
- **Interface responsiva** com Bootstrap 4 / SB Admin 2
- **Proteção CSRF** automática em formulários
- **Soft Deletes** para auditoria de dados

> 💡 Projeto desenvolvido com foco em **código limpo**, **documentação** e **escalabilidade**.

---

## ✨ Funcionalidades

### 🔐 Autenticação & Autorização
- [x] Login/Logout com sessões seguras
- [x] Hash de senhas com `PASSWORD_DEFAULT` (bcrypt)
- [x] Proteção de rotas com Filters
- [x] Sistema de roles (Super Admin, Admin, User)
- [x] Regeneração de sessão após login

### 🏢 Gestão de Unidades
- [x] CRUD completo (Create, Read, Update, Delete)
- [x] Validação de dados com mensagens em português
- [x] Toggle de status (Ativar/Desativar)
- [x] Campos únicos (email, telefone, nome)
- [x] Soft delete para auditoria

### 📊 Interface Administrativa
- [x] Dashboard com métricas
- [x] DataTables com paginação e busca
- [x] Formulários com Bootstrap 4
- [x] Mensagens flash de feedback
- [x] Dropdown de ações por registro

### 🛡️ Segurança
- [x] Proteção CSRF em todos os formulários
- [x] Sanitização de inputs (XSS prevention)
- [x] Validação server-side robusta
- [x] Prepared statements (SQL Injection)
- [x] Escape automático em views

---

## 🏗️ Arquitetura

```
┌─────────────────────────────────────────────────────────────────┐
│                         FRONTEND                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │    Views     │  │   Layouts    │  │  View Cells  │          │
│  │  (Bootstrap) │  │  (SB Admin)  │  │  (Buttons)   │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        BACKEND                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │  Controllers │──│   Services   │──│    Models    │          │
│  │  (HTTP/CRUD) │  │(Business Logic│  │  (Database)  │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│         │                                     │                  │
│         ▼                                     ▼                  │
│  ┌──────────────┐                     ┌──────────────┐          │
│  │   Filters    │                     │   Entities   │          │
│  │    (Auth)    │                     │   (Domain)   │          │
│  └──────────────┘                     └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        DATABASE                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │    Users     │  │    Units     │  │   (future)   │          │
│  │  (Auth/RBAC) │  │ (Scheduling) │  │  Services,   │          │
│  │              │  │              │  │ Appointments │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
└─────────────────────────────────────────────────────────────────┘
```

### 📁 Estrutura de Diretórios

```
app/
├── Cells/              # View Cells (componentes reutilizáveis)
│   └── ButtonsCell.php
├── Config/             # Configurações do framework
│   ├── Filters.php     # Registro de filtros (Auth, CSRF)
│   └── Routes.php      # Definição de rotas
├── Controllers/        # Controladores HTTP
│   ├── AuthController.php
│   └── Super/          # Área administrativa
│       └── UnitsController.php
├── Database/
│   ├── Migrations/     # Estrutura do banco
│   └── Seeds/          # Dados iniciais
├── Entities/           # Objetos de domínio
│   ├── MyBaseEntity.php
│   ├── Unit.php
│   └── User.php
├── Filters/            # Middlewares
│   └── AuthFilter.php
├── Helpers/            # Funções auxiliares
│   └── general_helper.php
├── Libraries/          # Serviços de negócio
│   ├── MyBaseService.php
│   └── UnitService.php
├── Models/             # Acesso a dados
│   ├── UnitModel.php
│   └── UserModel.php
└── Views/              # Templates
    └── Back/
        ├── Auth/       # Telas de login
        ├── Layout/     # Layout master
        └── Units/      # CRUD de unidades
```

---

## 🚀 Instalação

### Pré-requisitos

- PHP 8.2 ou superior
- Composer
- MySQL 8.0 ou MariaDB 10.4+
- Extensões PHP: intl, mbstring, json, mysqlnd

### Passo a Passo

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/agendamentos.git
cd agendamentos

# 2. Instale as dependências
composer install

# 3. Configure o ambiente
cp env .env

# 4. Edite o arquivo .env com suas configurações
# - CI_ENVIRONMENT = development
# - app.baseURL = 'http://localhost:8080/'
# - database.default.hostname = localhost
# - database.default.database = agendamentos
# - database.default.username = root
# - database.default.password = sua_senha

# 5. Execute as migrations
php spark migrate

# 6. Crie os usuários iniciais
php spark db:seed UserSeeder

# 7. Inicie o servidor de desenvolvimento
php spark serve
```

### 🔑 Credenciais de Acesso

| Usuário | Email | Senha | Papel |
|---------|-------|-------|-------|
| Administrador | admin@sistema.com | admin123 | Super Admin |
| Gerente | gerente@sistema.com | gerente123 | Admin |
| Usuário | usuario@sistema.com | usuario123 | User |

> ⚠️ **Importante:** Altere as senhas padrão em ambiente de produção!

---

## 📸 Screenshots

### Tela de Login
Interface moderna e responsiva com validação client-side e server-side.

### Dashboard Administrativo
Painel com navegação lateral, métricas e acesso rápido às funcionalidades.

### Listagem de Unidades
DataTables com paginação, busca, ordenação e dropdown de ações.

### Formulário de Cadastro
Validação em tempo real com feedback visual Bootstrap.

---

## 🛠️ Tecnologias

### Backend
| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| PHP | 8.2+ | Linguagem de programação |
| CodeIgniter | 4.6.4 | Framework MVC |
| MySQL | 8.0 | Banco de dados relacional |

### Frontend
| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| Bootstrap | 4.6 | Framework CSS |
| SB Admin 2 | 2.0 | Template administrativo |
| jQuery | 3.6 | Biblioteca JavaScript |
| DataTables | 1.11 | Plugin para tabelas |
| Font Awesome | 5.15 | Ícones vetoriais |

### Ferramentas
| Ferramenta | Descrição |
|------------|-----------|
| Composer | Gerenciador de dependências PHP |
| PHPUnit | Framework de testes |
| Spark CLI | CLI do CodeIgniter |

---

## 📋 Roadmap

- [x] Sistema de autenticação
- [x] CRUD de Unidades
- [x] CRUD de Serviços
- [x] CRUD de Profissionais
- [x] Sistema de Agendamentos
- [x] Calendário interativo (FullCalendar)
- [x] CRUD de Clientes
- [x] CRUD de Usuários
- [ ] Notificações por email
- [ ] API REST
- [ ] Relatórios e dashboards
- [ ] Multi-tenant (múltiplas empresas)
- [ ] Integração com WhatsApp

---

## 🧪 Testes

```bash
# Executar todos os testes
./vendor/bin/phpunit

# Executar com cobertura
./vendor/bin/phpunit --coverage-html reports/
```

---

## 🤝 Contribuição

Contribuições são bem-vindas! Por favor, leia o guia de contribuição antes de submeter PRs.

1. Fork o projeto
2. Crie sua branch de feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👤 Autor

Desenvolvido com ❤️ por **Tavilo Breno**

[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/tavilo-breno)
[![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/TaviloBreno)
[![Email](https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:tavilo.breno10@gmail.com)

---

<div align="center">

**⭐ Se este projeto foi útil para você, considere dar uma estrela!**

</div>
