# Symfoban - Kanban Board System

A modern, lightweight Kanban board application built with Symfony 7.4, featuring drag-and-drop task management, user authentication, and a beautiful dark mode interface.

---

## 📑 Table of Contents

- [Features](#-features)
- [Quick Start](#-quick-start)
- [Installation](#-installation)
  - [Docker Deployment (Recommended)](#docker-deployment-recommended)
  - [Local Development](#local-development)
- [Usage](#-usage)
- [Development](#-development)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

- 📋 **Kanban Board Management** - Create and manage multiple boards with custom columns
- 🔄 **Drag & Drop** - Intuitive HTML5 drag-and-drop for reordering tasks
- 👥 **User Authentication** - Secure login system with role-based access control
- 🎨 **Dark Mode** - Beautiful dark/light theme toggle with persistent preference
- 📊 **Task Tracking** - Track task creators, assignments, priorities, and due dates
- 🏷️ **Tags & Labels** - Color-coded tags for task organization
- 📅 **Due Dates** - Visual indicators for overdue and upcoming tasks
- ⚡ **Task Priorities** - Four priority levels (Low, Medium, High, Critical)
- 🔍 **Advanced Search** - Search across tasks, boards, and users
- 📝 **Activity Log** - Track all board changes and user actions
- 🎯 **Board Templates** - Quick board creation from predefined templates
- 🐳 **Docker Support** - Complete Docker setup for easy deployment
- 🔐 **Admin Controls** - Admin-only user management

---

## 🚀 Quick Start

### Docker Deployment (Recommended)

```bash
# 1. Clone the repository
git clone https://github.com/yourusername/Symfoban.git
cd Symfoban

# 2. Configure environment (create .env file)
# See Docker Deployment section below for details

# 3. Start services
docker compose up -d

# 4. Setup application
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console app:create-admin

# 5. Access the application
# Open http://localhost:8080 in your browser
```

### Local Development

```bash
# 1. Clone and install
git clone https://github.com/yourusername/Symfoban.git
cd Symfoban
composer install

# 2. Configure database in .env.local
# 3. Run migrations and create admin user
php bin/console doctrine:migrations:migrate
php bin/console app:create-admin

# 4. Start server
symfony server:start
# or: php -S localhost:8000 -t public
```

---

## 📦 Installation

### Requirements

**Docker Deployment:**
- Docker >= 20.10
- Docker Compose >= 2.0

**Local Development:**
- PHP >= 8.2
- Composer
- Extensions: `ext-ctype`, `ext-iconv`, `ext-pdo`
- Database: SQLite (default), PostgreSQL, or MySQL

---

### Docker Deployment (Recommended)

#### 1. Configure Environment

Create a `.env` file in the project root:

```env
# Application Environment
APP_ENV=dev

# PostgreSQL Configuration
POSTGRES_DB=symfoban
POSTGRES_USER=symfoban
POSTGRES_PASSWORD=ChangeMeInProduction!
POSTGRES_VERSION=16
POSTGRES_PORT=5432

# Docker Configuration
DOCKER_TARGET=development
NGINX_PORT=8080

# Database URL
DATABASE_URL=postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@postgres:5432/${POSTGRES_DB}?serverVersion=${POSTGRES_VERSION}&charset=utf8
```

#### 2. Start Services

```bash
# Build and start all services
docker compose up -d

# View logs
docker compose logs -f

# Check status
docker compose ps
```

#### 3. Setup Application

```bash
# Install dependencies
docker compose exec php composer install

# Run migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

# Create admin user
docker compose exec php php bin/console app:create-admin

# (Optional) Load demo data
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

#### 4. Access Application

Open your browser: **http://localhost:8080**

#### Docker Services

- **`php`** - PHP 8.2-FPM container
- **`nginx`** - Nginx web server (port 8080)
- **`postgres`** - PostgreSQL 16 database (port 5432)

#### Common Commands

```bash
# Start/Stop
docker compose up -d              # Start services
docker compose down               # Stop services
docker compose down -v            # Stop and remove volumes

# Development
docker compose exec php sh         # Access PHP container
docker compose exec php php bin/console <command>  # Run Symfony commands
docker compose logs -f php         # View logs

# Production
docker compose build --target production
docker compose exec php composer install --no-dev --optimize-autoloader
docker compose exec php php bin/console cache:warmup --env=prod
```

#### Troubleshooting

**Port conflicts:** Change `NGINX_PORT` or `POSTGRES_PORT` in `.env`

**Database connection:** Check `docker compose ps postgres` and logs

**Permissions:** `docker compose exec php chown -R www-data:www-data /var/www/symfony/var`

---

### Local Development

#### 1. Clone Repository

```bash
git clone https://github.com/yourusername/Symfoban.git
cd Symfoban
```

#### 2. Install Dependencies

```bash
composer install
```

#### 3. Configure Environment

Create `.env.local`:

```env
# For SQLite (default)
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

# For PostgreSQL
# DATABASE_URL="postgresql://user:password@127.0.0.1:5432/symfoban?serverVersion=16&charset=utf8"
```

#### 4. Setup Database

```bash
# Run migrations
php bin/console doctrine:migrations:migrate

# Create admin user
php bin/console app:create-admin
```

#### 5. Start Server

```bash
# Symfony CLI (recommended)
symfony server:start

# Or PHP built-in server
php -S localhost:8000 -t public
```

Access: **http://localhost:8000**

---

## 💡 Usage

### Creating Boards

1. Login with your admin account
2. Navigate to **"Boards"** in the navigation
3. Click **"+ New Board"**
4. Enter a board name and save

### Managing Tasks

1. Open a board
2. Click **"+ Add Column"** to create columns
3. Click **"+ Add Task"** within a column to add tasks
4. **Drag and drop** tasks to reorder or move between columns
5. Click on tasks to edit details, set priorities, due dates, and tags

### User Management (Admin Only)

1. Navigate to **"Users"** in the navigation
2. Click **"+ Create User"** to add new users
3. View user details and manage accounts

---

## 🛠️ Development

### CI/CD Pipeline

This project uses GitHub Actions for continuous integration:

- ✅ **Tests** - Runs PHPUnit tests on PHP 8.2 and 8.3
- ✅ **Code Quality** - Validates PHP syntax and composer.json
- ✅ **Docker Build** - Tests Docker image builds (development & production)

The pipeline runs automatically on:
- Push to `main` or `feature/kanban-board` branches
- Pull requests to `main` or `feature/kanban-board` branches

View workflow status: [![CI](https://github.com/yourusername/Symfoban/actions/workflows/ci.yml/badge.svg)](https://github.com/yourusername/Symfoban/actions/workflows/ci.yml)

### Running Tests

```bash
# Run all tests
php bin/phpunit

# Run specific test suite
php bin/phpunit --testsuite=Unit
php bin/phpunit --testsuite=Integration

# Run with coverage
php bin/phpunit --coverage-html coverage/
```

See [tests/README.md](tests/README.md) for detailed test documentation.

### Common Commands

```bash
# Cache
php bin/console cache:clear

# Database
php bin/console make:migration              # Create migration
php bin/console doctrine:migrations:migrate # Run migrations
php bin/console doctrine:fixtures:load      # Load fixtures

# Docker
docker compose exec php php bin/console <command>
```

### Project Structure

```
Symfoban/
├── config/              # Symfony configuration
├── docker/              # Docker configuration
│   └── nginx/          # Nginx config
├── migrations/          # Database migrations
├── public/             # Web root
├── src/
│   ├── Command/        # Console commands
│   ├── Controller/     # Controllers
│   ├── Entity/         # Doctrine entities
│   ├── Form/           # Form types
│   ├── Repository/     # Repositories
│   └── Service/        # Services
├── templates/          # Twig templates
└── tests/              # PHPUnit tests
```

---

## 🏗️ Tech Stack

- **Framework**: Symfony 7.4
- **Template Engine**: Twig
- **ORM**: Doctrine ORM
- **Styling**: TailwindCSS (via CDN)
- **Authentication**: Symfony Security Bundle
- **Database**: PostgreSQL (Docker) / SQLite (local)
- **Testing**: PHPUnit 11.5

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Branching Workflow

- **`main`** - Production-ready code
- **`feature/*`** - Feature development branches

---

## 🔒 Security

- User passwords hashed with Symfony's password hasher
- CSRF protection on all forms
- Role-based access control (ROLE_USER, ROLE_ADMIN)
- Admin-only user registration

---

## 📄 License

This project is proprietary software.

---

## 📞 Support

For issues and questions, please open an issue on [GitHub](https://github.com/yourusername/Symfoban/issues).

---

**Built with ❤️ using Symfony 7.4**
