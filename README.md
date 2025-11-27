# Gopaddi / Paddi Helper (Juggernaut)

A shared Laravel utility package designed to centralize core logic, schema definitions, enums, helpers, and developer tooling across multiple Gopaddi Laravel microservices.

This package ensures:

- Consistent database schema across services
- Shared business logic (enums, services, helpers)
- Unified scaffolding/boilerplate generation
- Safe migration synchronization
- Improved developer experience
- Reduced code duplication
- Predictable updates across teams

This package is intended for internal use within the Gopaddi engineering ecosystem.

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Installation](#installation)
4. [Local Development (Symlink Mode)](#local-development-symlink-mode)
5. [Migration Management](#migration-management)
6. [Scaffolding System](#scaffolding-system)
7. [Commands](#commands)
8. [Recommended Workflow](#recommended-workflow)
9. [Handling Shared Environments (Staging/Prod)](#handling-shared-environments-stagingprod)
10. [Project Structure](#project-structure)
11. [Future Enhancements](#future-enhancements)

---

## Overview

The **Paddi Helper (Juggernaut)** package serves as a central foundation layer for all Gopaddi Laravel services. It provides:

- Shared database migrations
- Reusable PHP classes
- Shared enums and types
- Tools to generate new package components
- Safe migration publishing logic
- A consistent workflow for multiple dev teams
- Support for multi-service environments sharing the same database

---

## Features

### ✔ Centralized Shared Migrations
Ensures all services stay synced with one source of truth.

### ✔ Safe Migration Publisher
Publishes migrations from the package into each app without duplicating or overwriting files unless requested.

### ✔ Internal Scaffolding System
Commands to generate:

- Migrations
- Classes
- Enums
- Full “resource bundles”

### ✔ Symlink-Aware Development
Automatically writes files into the actual package directory when installed in path-repository mode.

### ✔ Multi-Service Ready
Designed for teams where several Laravel apps share the same database.

---

## Installation

Install via Composer:

```bash
composer require gopaddi/paddi-helper 
```

Then, publish the migrations:



## Local Development (Symlink Mode)

When symlinked, the package becomes editable in real time:

- All generator commands write directly to the package directory  
- No need to modify vendor code  
- Changes are immediately commit-ready  
- Perfect for contributors developing the package itself  

This workflow is ideal when multiple developers collaborate on the shared package.

---

## Migration Management

All shared migrations live inside the package:
To publish the package migrations into a Laravel application:

```bash
php artisan juggernaut:publish-migrations
```

### Publisher Behavior

The migration publisher:

Detects migrations that already exist in the host project

Publishes only new migrations

Summarizes what was added vs. already present

Avoids accidental duplication

Only overwrites existing files when you use --force

Example output:
```bash
Total migrations in package:   1025
Existing in project already:   1023
Newly published migrations:    2

New migrations added:
  ✔ 2025_01_10_000000_add_wallets_table.php
  ✔ 2025_01_11_000000_create_invoice_table.php
```

## Scaffolding System

The Paddi Helper (Juggernaut) package includes a built-in scaffolding framework designed to help developers quickly generate shared components directly inside the package. These generators automate boilerplate creation and ensure consistency across contributors.

All generated files are written **inside the package itself**, not inside the consuming application.  
This ensures shared logic and schema definitions remain centralized.

### Available Generators

The scaffolding system provides the following generators:

- Migration Generator
- Class Generator
- Enum Generator
- Make-All Generator (migration + class + enum)

These commands streamline the process of adding new shared features, database tables, utilities, or domain logic.

---

## Commands

### 1. Publish Migrations

Publishes all package migrations into the host application's `database/migrations` directory.

```bash
php artisan juggernaut:publish-migrations
```

Force overwrite:

```bash
php artisan juggernaut:publish-migrations --force
```

The publisher automatically:

- Detects existing migrations
- Publishes only new ones
- Avoids duplicates
- Displays a summary of added and existing files

---

### 2. Generate a Migration

```bash
php artisan juggernaut:make-migration create_orders_table --create=orders
```

Modify an existing table:

```bash
php artisan juggernaut:make-migration add_wallet_column_to_users --table=users
```

Migrations are generated into:

```
paddi_helper/database/migrations/
```

---

### 3. Generate a Class

```bash
php artisan juggernaut:make-class MoneyFormatter
```

Optional namespace:

```bash
php artisan juggernaut:make-class TaxService --namespace=Services
```

Generated into:

```
paddi_helper/src/
```

---

### 4. Generate an Enum

```bash
php artisan juggernaut:make-enum UserStatus
```

Enums are stored in:

```
paddi_helper/src/Enums/
```

---

### 5. Generate Everything (Migration + Class + Enum)

```bash
php artisan juggernaut:make-all Order --create=orders
```

Ideal for creating new business modules or domain structures.

---

## Maintainer Symlink Mode (Local Development Only)

The Paddi Helper package supports a **local-only symlink workflow** that allows contributors to develop the package while seeing instant updates inside any Laravel application.

This feature is **only for package maintainers** and is never used in staging or production.

### Why Symlink Mode?
- Live editing of the package without reinstalling
- Instant testing in a real Laravel project
- Zero friction when building migrations, enums, helpers, and scaffolds
- Encourages contribution from multiple developers

### Important Rule
Maintainers use symlink mode locally, but the path repository configuration must **never be committed** to the application repository.

Instead, maintainers use a **local override file**:

### Step 1 — Create `composer.local.json`

In the Laravel app that consumes the package:

```
composer.local.json
```

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../paddi_helper",
            "options": { "symlink": true }
        }
    ]
}
```

### Step 2 — Gitignore it

Add to `.gitignore`:

```
composer.local.json
```

### Step 3 — Install the package in symlink mode

```bash
composer update gopaddi/paddi-helper
```

Composer automatically merges `composer.json` + `composer.local.json`.

### Result

- Maintainer gets live-symlink package development
- No changes leak into Git
- Staging & production remain safe
- Team members stay on normal Composer installation

This setup is fully supported by Composer and used by many large teams.

---

## Recommended Workflow

This workflow ensures clean collaboration between package maintainers and microservice developers across the entire Gopaddi engineering ecosystem.

---

### 1. Package Maintainers (Core Contributors)

Maintainers use **symlink mode** to actively develop and update the shared package.

#### Their workflow:

1. Enable symlink mode using `composer.local.json` (not committed).
2. Make changes inside the package:
    - Add migrations
    - Add enums
    - Add classes
    - Use scaffolding commands (`juggernaut:make-*`)
3. Publish the new package migrations into each local microservice:
   ```bash
   php artisan juggernaut:publish-migrations
   ```
4. Test everything (migrations, business logic, scaffolding).
5. Commit changes:
    - Commit package changes to the **package repository**.
    - Commit published migrations to the **microservice repository**.
6. Tag a new release (optional but recommended):
   ```bash
   git tag v1.1.0
   git push origin v1.1.0
   ```
7. Notify the team or update documentation if needed.

**Maintainers never commit:**
- symlink configs
- path repositories
- composer.local.json

---

### 2. Normal Team Members (Consumers of the Package)

Team members simply consume the package. They **never** use symlink mode or scaffolding commands.

Their workflow:

```bash
git pull
composer install
php artisan migrate
```

Everything they need already exists because maintainers:

- committed the migrations into the microservice repo
- versioned the package update

No publishing needed.  
No scaffolding needed.  
No symlink needed.

---

### 3. Staging & Production Servers

Staging and production **never** use symlink mode.

Their workflow:

```bash
composer install --no-dev
php artisan migrate
```

The package is downloaded normally from GitHub/Packagist.  
No reference to local folders or symlinks.

#### Shared Database Rule
If multiple services use the same database:

Only one service (the DB leader) should run migrations.

Use:

```
DB_LEADER_SERVICE=true
```

Other services:

```
DB_LEADER_SERVICE=false
```

This prevents schema corruption.

---

## Workflow Summary Table

| Role | Uses Symlink? | Uses Path Repo? | Runs Scaffolding? | Publishes Migrations? | Runs Migrations? |
|------|---------------|-----------------|--------------------|------------------------|-------------------|
| **Package Maintainer** | YES | YES (local-only) | YES | YES | YES |
| **Team Member** | NO | NO | NO | NO | YES |
| **Staging** | NO | NO | NO | NO | YES (DB Leader Only) |
| **Production** | NO | NO | NO | NO | YES (DB Leader Only) |

---

This updated workflow ensures:

- Safe package development
- Zero risk to deployment environments
- No accidental symlink in staging
- Unified migrations across all services
- Clean separation of maintainers and consumers
- Zero migration duplication
- Predictable deployments



## Project Structure

```
paddi_helper/
├── src/
│   ├── Commands/
│   │   ├── PublishMigrationsCommand.php
│   │   ├── MakeMigrationCommand.php
│   │   ├── MakeClassCommand.php
│   │   ├── MakeEnumCommand.php
│   │   └── MakeAllCommand.php
│   ├── SkeletonServiceProvider.php
│   └── ...
├── database/
│   └── migrations/
├── stubs/
│   ├── class.stub
│   └── enum.stub
└── composer.json
```

This structure maintains modularity, clarity, and extensibility.

---

## Future Enhancements

Planned improvements:

- `--dry` run mode for publishing
- Hash-based migration detection
- JSON output mode for CI integration
- Additional scaffold types (DTOs, services, events, models)
- Module generators (migration + enum + service + class)
- Package diagnostics (`package:doctor`)
- Automatic Pint formatting after generation

---

## Conclusion

The Paddi Helper (Juggernaut) package provides a powerful and scalable shared development framework for the Gopaddi ecosystem. With internal scaffolding tools, safe migration publishing, and consistent workflows, it ensures alignment across all Laravel microservices, reduces duplication, and accelerates development across teams.
