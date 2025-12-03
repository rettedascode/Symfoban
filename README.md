# Symfoban - Kanban Board System

A modern, lightweight Kanban board application built with Symfony 7.4, featuring drag-and-drop task management, user authentication, and a beautiful dark mode interface.

## Features

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

## Requirements

### Local Development (without Docker)

- **PHP**: >= 8.2
- **Extensions**: 
  - `ext-ctype`
  - `ext-iconv`
  - `ext-pdo` (for database)
- **Composer**: Latest version
- **Database**: SQLite (default) or MySQL/PostgreSQL

### Docker Deployment

- **Docker**: >= 20.10
- **Docker Compose**: >= 2.0

## Installation

> **Note**: For a quicker setup, you can use [Docker Deployment](#docker-deployment) instead of manual installation.

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/Symfoban.git
cd Symfoban
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

Copy the `.env` file and configure your database:

```bash
cp .env .env.local
```

Edit `.env.local` and set your database URL:

```env
# For SQLite (default)
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

# For MySQL
# DATABASE_URL="mysql://user:password@127.0.0.1:3306/symfoban?serverVersion=8.0"

# For PostgreSQL
# DATABASE_URL="postgresql://user:password@127.0.0.1:5432/symfoban?serverVersion=13&charset=utf8"
```

### 4. Run Database Migrations

```bash
php bin/console doctrine:migrations:migrate
```

This will create all necessary database tables (User, Board, Column, Task, Tag, BoardTemplate, ActivityLog).

### 5. Create Admin User

Since user registration is admin-only, create your first admin user:

```bash
php bin/console app:create-admin
```

Follow the prompts to enter email, name, and password.

### 6. Start the Development Server

```bash
symfony server:start
```

Or using PHP's built-in server:

```bash
php -S localhost:8000 -t public
```

### 7. Access the Application

Open your browser and navigate to:

```
http://localhost:8000
```

## Docker Deployment

Symfoban includes a complete Docker setup for easy deployment in development and production environments.

### Prerequisites

- **Docker**: >= 20.10
- **Docker Compose**: >= 2.0

### Quick Start with Docker

1. **Clone the Repository**

```bash
git clone https://github.com/yourusername/Symfoban.git
cd Symfoban
```

2. **Configure Environment Variables**

Create a `.env` file in the project root (or copy from `.env.example` if available):

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

3. **Build and Start Containers**

```bash
# Build and start all services
docker compose up -d

# View logs
docker compose logs -f

# Check container status
docker compose ps
```

4. **Install Dependencies and Setup Database**

```bash
# Install Composer dependencies
docker compose exec php composer install

# Run database migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

# Create admin user
docker compose exec php php bin/console app:create-admin

# (Optional) Load demo data (includes demo board, users, tasks, tags, and templates)
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

5. **Access the Application**

Open your browser and navigate to:

```
http://localhost:8080
```

### Docker Services

The Docker Compose setup includes:

- **`php`** - PHP 8.2-FPM container running the Symfony application
- **`nginx`** - Nginx web server as reverse proxy
- **`postgres`** - PostgreSQL 16 database server

### Common Docker Commands

```bash
# Start services
docker compose up -d

# Stop services
docker compose down

# Stop and remove volumes (WARNING: deletes database data)
docker compose down -v

# Rebuild containers after code changes
docker compose up -d --build

# Execute Symfony console commands
docker compose exec php php bin/console <command>

# Access PHP container shell
docker compose exec php sh

# View logs
docker compose logs -f php
docker compose logs -f nginx
docker compose logs -f postgres

# Clear Symfony cache
docker compose exec php php bin/console cache:clear
```

### Production Deployment

For production deployment:

1. **Update Environment Variables**

```env
APP_ENV=prod
DOCKER_TARGET=production
POSTGRES_PASSWORD=<strong-production-password>
DATABASE_URL=postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@postgres:5432/${POSTGRES_DB}?serverVersion=${POSTGRES_VERSION}&charset=utf8
```

2. **Build Production Image**

```bash
docker compose build --target production
```

3. **Start Production Services**

```bash
docker compose up -d
```

4. **Run Production Setup**

```bash
# Install dependencies (no dev)
docker compose exec php composer install --no-dev --optimize-autoloader

# Clear and warm up cache
docker compose exec php php bin/console cache:clear --env=prod
docker compose exec php php bin/console cache:warmup --env=prod

# Run migrations
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction --env=prod
```

### Dockerfile Details

The Dockerfile uses a multi-stage build:

- **Base Stage**: Installs PHP extensions and Composer
- **Development Stage**: Includes dev dependencies and development PHP configuration
- **Production Stage**: Optimized for production with OPcache enabled

### Volume Mounts

- Application code is mounted as a volume for development (hot-reload)
- PostgreSQL data is persisted in `postgres_data` volume
- Symfony `var/` directory is mounted for cache and logs

### Network Configuration

All services run on a custom Docker network (`symfoban_network`) for isolation and security.

### Troubleshooting

**Port Already in Use:**
```bash
# Change ports in .env file
NGINX_PORT=8081
POSTGRES_PORT=5433
```

**Permission Issues:**
```bash
# Fix file permissions
docker compose exec php chown -R www-data:www-data /var/www/symfony/var
```

**Database Connection Issues:**
```bash
# Check database health
docker compose ps postgres

# View database logs
docker compose logs postgres

# Test PostgreSQL connection
docker compose exec php php -r "try { \$pdo = new PDO('pgsql:host=postgres;dbname=symfoban', 'symfoban', 'ChangeMeInProduction!'); echo 'PostgreSQL: Connected\n'; } catch (Exception \$e) { echo 'PostgreSQL: ' . \$e->getMessage() . '\n'; }"
```

**Clear Everything and Start Fresh:**
```bash
docker compose down -v
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
```

## Tech Stack

- **Backend Framework**: Symfony 7.4
- **Template Engine**: Twig
- **ORM**: Doctrine ORM
- **Styling**: TailwindCSS (via CDN)
- **Authentication**: Symfony Security Bundle
- **Database**: SQLite (default, can be changed to PostgreSQL)

## Project Structure

```
Symfoban/
├── config/              # Symfony configuration files
├── docker/             # Docker configuration files
│   └── nginx/         # Nginx configuration
├── migrations/          # Database migrations
├── public/             # Web root
├── src/
│   ├── Command/        # Console commands
│   ├── Controller/     # Controllers
│   ├── DataFixtures/   # Doctrine fixtures
│   ├── Entity/         # Doctrine entities (Board, Column, Task, User, etc.)
│   ├── Form/           # Form types
│   ├── Repository/     # Doctrine repositories
│   └── Service/        # Business logic services
├── templates/          # Twig templates
│   ├── activity_log/  # Activity log templates
│   ├── board/         # Board templates
│   ├── column/        # Column templates
│   ├── home/          # Landing page
│   ├── registration/  # Registration template
│   ├── search/        # Search templates
│   ├── security/      # Login template
│   ├── task/          # Task templates
│   └── user/          # User management templates
├── var/               # Cache and logs
├── .dockerignore      # Docker ignore file
├── Dockerfile         # Docker image definition
└── compose.yaml       # Docker Compose configuration
```

## Branching Workflow

This project uses a feature branch workflow:

- **`main`** - Production-ready code
- **`feature/kanban-board`** - Active development branch for Kanban features

### Working with Branches

```bash
# Create and switch to a new feature branch
git checkout -b feature/your-feature-name

# Push feature branch to GitHub
git push -u origin feature/your-feature-name
```

## Usage

### Creating Your First Board

1. Login with your admin account
2. Navigate to "Boards" in the navigation
3. Click "+ New Board"
4. Enter a board name and save

### Adding Columns and Tasks

1. Open a board
2. Click "+ Add Column" to create columns
3. Click "+ Add Task" within a column to add tasks
4. Drag and drop tasks to reorder or move between columns

### Managing Users (Admin Only)

1. Navigate to "Users" in the navigation
2. Click "+ Create User" to add new users
3. View user details and manage accounts

## Development

### Running Tests

```bash
# Run PHPUnit tests (if configured)
php bin/phpunit
```

### Clearing Cache

```bash
php bin/console cache:clear
```

### Database Operations

```bash
# Create a new migration
php bin/console make:migration

# Run migrations
php bin/console doctrine:migrations:migrate

# Reset database (WARNING: deletes all data)
php bin/console doctrine:schema:drop --force
php bin/console doctrine:schema:create
php bin/console doctrine:migrations:migrate
```

## Security

- User passwords are hashed using Symfony's password hasher
- CSRF protection enabled for all forms
- Role-based access control (ROLE_USER, ROLE_ADMIN)
- User registration restricted to admins only

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is proprietary software.

## Support

For issues and questions, please open an issue on GitHub.

---

**Built with ❤️ using Symfony 7.4**

