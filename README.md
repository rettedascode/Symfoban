# Symfoban - Kanban Board System

A modern, lightweight Kanban board application built with Symfony 7.4, featuring drag-and-drop task management, user authentication, and a beautiful dark mode interface.

## Features

- 📋 **Kanban Board Management** - Create and manage multiple boards with custom columns
- 🔄 **Drag & Drop** - Intuitive HTML5 drag-and-drop for reordering tasks
- 👥 **User Authentication** - Secure login system with role-based access control
- 🎨 **Dark Mode** - Beautiful dark/light theme toggle
- 📊 **Task Tracking** - Track task creators and positions
- 🔐 **Admin Controls** - Admin-only user management

## Requirements

- **PHP**: >= 8.2
- **Extensions**: 
  - `ext-ctype`
  - `ext-iconv`
  - `ext-pdo` (for database)
- **Composer**: Latest version
- **Database**: SQLite (default) or MySQL/PostgreSQL

## Installation

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

This will create all necessary database tables (User, Board, Column, Task).

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

## Tech Stack

- **Backend Framework**: Symfony 7.4
- **Template Engine**: Twig
- **ORM**: Doctrine ORM
- **Styling**: TailwindCSS (via CDN)
- **Authentication**: Symfony Security Bundle
- **Database**: SQLite (default, can be changed to MySQL/PostgreSQL)

## Project Structure

```
Symfoban/
├── config/              # Symfony configuration files
├── migrations/          # Database migrations
├── public/             # Web root
├── src/
│   ├── Command/        # Console commands
│   ├── Controller/     # Controllers
│   ├── Entity/         # Doctrine entities (Board, Column, Task, User)
│   ├── Form/           # Form types
│   └── Repository/     # Doctrine repositories
├── templates/          # Twig templates
│   ├── board/         # Board templates
│   ├── column/        # Column templates
│   ├── home/          # Landing page
│   ├── security/      # Login template
│   ├── task/          # Task templates
│   └── user/          # User management templates
└── var/               # Cache and logs
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

