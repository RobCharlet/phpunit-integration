# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Symfony 6.3 application demonstrating PHPUnit testing strategies (unit, integration, and functional tests) through a dinosaur park management system. The project is based on SymfonyCasts' "PHPUnit: Testing with a Bite!" tutorial series.

## Development Commands

### Running Tests

```bash
# Run all tests
bin/phpunit

# Run a specific test file
bin/phpunit tests/Unit/Entity/DinosaurTest.php

# Run a specific test method
bin/phpunit --filter testCanGetAndSetData

# Run tests with coverage (requires xdebug or pcov)
bin/phpunit --coverage-html var/coverage
```

### Database Management

```bash
# Create database (using Symfony CLI with Docker awareness)
symfony console doctrine:database:create --if-not-exists

# Create/update schema
symfony console doctrine:schema:create
symfony console doctrine:schema:update --force

# Load fixtures
symfony console doctrine:fixtures:load

# Run migrations
symfony console doctrine:migrations:migrate
```

### Development Server

```bash
# Start Symfony local server
symfony serve

# Start Docker database container
docker compose up -d

# Stop Docker containers
docker compose down
```

### General Symfony Commands

```bash
# Clear cache
symfony console cache:clear

# Run any Symfony console command
symfony console [command]
# or
bin/console [command]
```

## Architecture

### Domain Model

The application models a dinosaur park with health monitoring via GitHub issues:

- **Dinosaur Entity**: Core domain entity with business logic for size classification and visitor acceptance based on health status
  - Uses PHP 8.1 enums for `HealthStatus` (Healthy, Sick, Hungry)
  - Contains domain logic methods: `getSizeDescription()`, `isAcceptingVisitors()`

- **LockDown Entity**: Tracks park lockdown events with status transitions
  - Uses `LockDownStatus` enum (active, ended, run_for_your_life)
  - Automatically sets `endedAt` timestamp when status changes to ENDED

### Service Layer

- **GithubService**: External integration that fetches dinosaur health status from GitHub issues
  - Uses Symfony HTTP Client with mocking support for tests
  - Implements caching with Symfony Cache component
  - Parses GitHub issue labels matching pattern "Status: {HealthStatus}"
  - Throws RuntimeException for unknown status labels

### Testing Strategy

The project demonstrates different testing approaches:

1. **Unit Tests** (`tests/Unit/`): Test isolated components without Symfony container
   - Entity business logic (DinosaurTest)
   - Service layer with mocked dependencies (GithubServiceTest)
   - Uses PHPUnit's `TestCase` base class
   - Demonstrates data providers for parameterized testing

2. **Integration Tests**: Would use `KernelTestCase` to test services with real dependencies

3. **Functional Tests**: Would use `WebTestCase` to test full HTTP request/response cycle

### Key Patterns

- **Data Providers**: Used extensively for parameterized tests (e.g., `sizeDescriptionProvider()`, `healthStatusProvider()`)
- **Test Doubles**: Mock objects for external dependencies (HttpClient, Logger) in unit tests
- **Symfony Test Components**: ArrayAdapter for cache, MockHttpClient for HTTP requests
- **Named Arguments**: PHP 8.0+ named arguments used in constructors and test methods

## Git Workflow Rules

### Branch Management

- **CRITICAL: NEVER commit directly to `main` branch**
- Always create a feature/fix/chore branch for any changes
- Branch naming conventions:
  - `feature/description` - for new features
  - `fix/description` - for bug fixes
  - `chore/description` - for maintenance tasks (dependencies, config, etc.)
- Example: `fix/github-actions-sqlite-path`, `feature/add-dinosaur-feeding`

### Pull Request Workflow

1. Create a branch for your changes
2. Make commits on the branch
3. Push the branch to GitHub
4. Create a Pull Request to `main`
5. Wait for CI checks to pass
6. Merge the PR (squash merge preferred)

### Required for All Changes

- All changes MUST go through Pull Requests
- PRs MUST have passing CI checks before merge
- Use descriptive PR titles and detailed descriptions
- Include test plan in PR description

### Commit Message Format

- Use clear, descriptive commit messages
- Start with a verb in imperative mood (Add, Fix, Update, Remove)
- Include AI co-author attribution: `Co-Authored-By: Claude <noreply@anthropic.com>`
- Add emoji prefix when using Claude Code: `🤖 Generated with [Claude Code](https://claude.com/claude-code)`

### Branch Protection

- The `main` branch is protected:
  - No force pushes allowed
  - No direct commits allowed
  - Requires pull request reviews (when configured)

## Important Notes

- **PHP Version**: Project uses PHP 8.1 (specified in `.php-version` file)
  - The `.php-version` file ensures Symfony CLI uses PHP 8.1 for all developers
  - Avoids deprecation warnings present in PHP 8.2+
  - Required for enums, named arguments, constructor property promotion
- Database can run in Docker or locally; Symfony CLI detects Docker containers
- Tests use Symfony PHPUnit Bridge (`bin/phpunit` wrapper) which manages PHPUnit versions
- The test environment is configured via `phpunit.xml.dist` with test-specific settings
- Cache directory: `var/cache/`, logs: `var/log/`
