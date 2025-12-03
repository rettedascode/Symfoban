# Tests

This directory contains unit and integration tests for the Symfoban application.

## Test Structure

```
tests/
├── Unit/              # Unit tests (isolated, no database)
│   ├── Entity/       # Entity tests
│   ├── Service/      # Service tests
│   └── Repository/   # Repository tests
├── Integration/      # Integration tests (with database)
└── bootstrap.php     # Test bootstrap file
```

## Running Tests

### Install Dependencies

First, make sure PHPUnit is installed:

```bash
composer install
```

### Run All Tests

```bash
php bin/phpunit
```

### Run Specific Test Suite

```bash
# Run only unit tests
php bin/phpunit --testsuite=Unit

# Run only integration tests
php bin/phpunit --testsuite=Integration
```

### Run Specific Test File

```bash
php bin/phpunit tests/Unit/Entity/BoardTest.php
```

### Run with Coverage

```bash
php bin/phpunit --coverage-html coverage/
```

## Test Configuration

Tests use the `test` environment by default. The configuration is in `phpunit.xml.dist`.

For test-specific environment variables, create a `.env.test.local` file.

## Writing Tests

### Unit Tests

Unit tests should test individual components in isolation, without database access:

```php
<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Board;
use PHPUnit\Framework\TestCase;

class BoardTest extends TestCase
{
    public function testBoardCreation(): void
    {
        $board = new Board();
        $board->setName('Test Board');
        
        $this->assertSame('Test Board', $board->getName());
    }
}
```

### Integration Tests

Integration tests can use the database and Symfony container:

```php
<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BoardIntegrationTest extends KernelTestCase
{
    // Tests that interact with the database
}
```

## Test Coverage

Current test coverage includes:

- ✅ Entity tests (Board, Column, Task, User)
- ✅ Service tests (ActivityLogService)
- ✅ Repository tests (basic structure)

More tests can be added as needed.

