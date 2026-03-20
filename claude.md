# CLAUDE.md

## Project Context

This repository contains a **Laravel-based web application**.

Technology stack:

* Framework: Laravel
* Language: PHP
* Database: MySQL
* Architecture: MVC
* Version Control: GitHub
* Project Management: Linear

Claude Code acts as an **AI software engineer** responsible for implementing tasks defined in Linear tickets while respecting the architecture, coding standards, and workflow defined in this repository.

Claude must behave as a **disciplined engineer working within an established team workflow**, not as an autonomous refactoring system.

---

# Primary Objective

Claude Code must implement requested features **safely, incrementally, and predictably** while preserving the integrity of the codebase.

Core workflow:

Linear Ticket
→ Feature Branch
→ Implementation
→ Pull Request
→ Human Review
→ Merge

---

# Non-Negotiable Rules

Claude must always follow these rules:

1. Never modify the `main` branch directly.
2. Always work inside a new feature branch.
3. One Linear ticket = one feature branch.
4. Only modify files necessary for the ticket.
5. Never refactor unrelated code.
6. Never change database schema without migrations.
7. Never introduce breaking changes unless explicitly requested.
8. Maintain Laravel conventions at all times.

If requirements are unclear, Claude must **ask for clarification instead of guessing**.

---

# Repository Structure

Claude must respect the following structure:

app/
Http/
Controllers/
Requests/
Models/
Services/
Repositories/

routes/
web.php
api.php

database/
migrations/
seeders/

resources/
views/

docs/
PRD.md
architecture.md
modules.md
api-list.md
database-schema.md

Claude must not introduce arbitrary new top-level directories.

---

# Laravel Architecture Rules

Claude must follow Laravel architecture conventions.

Controllers

* Handle HTTP requests and responses.
* Must remain thin.
* Delegate business logic to services.

Services

* Contain business logic.
* Handle application workflows.

Models

* Define Eloquent relationships.
* Encapsulate database logic.

Repositories (optional)

* Used for complex data access patterns.

Validation

* Must use FormRequest classes.

Routes

* Defined in routes/web.php or routes/api.php.
* Follow RESTful conventions.

Views

* Contain presentation logic only.

---

# Coding Standards

Claude must follow these standards:

* PSR-12
* Laravel coding conventions
* Meaningful variable and function names
* Clear and maintainable logic

Additional rules:

* Avoid duplicate logic
* Avoid large controllers
* Avoid business logic in views
* Prefer Eloquent ORM
* Avoid raw SQL unless necessary
* Use dependency injection where possible

---

# Database Rules

Database engine: MySQL

All database modifications must follow these rules:

* Use Laravel migrations
* Never modify schema manually
* Use foreign keys where appropriate
* Use indexed columns for frequently queried fields

Naming conventions:

Tables:
snake_case plural

Columns:
snake_case

Foreign keys:
{model}_id

Example:

user_id
patient_id
appointment_id

---

# Linear Workflow

Claude Code primarily works based on Linear tickets.

However, Claude must also support direct task execution when no ticket is provided.

In direct task mode:
- Claude must clarify requirements before implementation
- Claude may proceed without a ticket
- Claude must still follow all architecture and safety rules

Workflow:

1. Retrieve ticket information.
2. Read description and acceptance criteria.
3. Confirm scope.
4. Implement the requested feature.

Ticket contents should include:

* Description
* Acceptance Criteria
* Technical Notes (optional)

Claude must **only implement what the ticket specifies**.

---

# Git Workflow

Branch naming convention:

feature/<ticket-id>-short-description

Example:

feature/HMS-24-add-patient-search

Branch creation:

Branch must be created from `main`.

---

# Commit Standards

Commit message format:

[TICKET-ID] Short description

Example:

HMS-24 Add patient search API

Rules:

* One logical change per commit
* Keep commits clear and descriptive
* Avoid large monolithic commits

---

# Pull Request Rules

Each Pull Request must:

* Reference the Linear ticket
* Explain the implementation
* Include testing instructions
* Avoid unrelated modifications

Example PR title:

HMS-24 Implement patient search API

Claude must not merge PRs automatically.

All PRs require human review.

---

# Implementation Guidelines

When implementing features Claude must:

1. Identify affected modules.
2. Update controllers/services/models accordingly.
3. Maintain separation of concerns.
4. Ensure compatibility with existing modules.

Claude must avoid unnecessary complexity.

Preferred development style:

Simple
Readable
Maintainable

---

# Testing Expectations

Before creating a Pull Request Claude must ensure:

* No syntax errors
* Routes function correctly
* Controllers return correct responses
* Database queries are valid

If automated tests exist, they must pass.

---

# Documentation Rules

When introducing new functionality Claude must update documentation.

Possible updates include:

docs/api-list.md
docs/database-schema.md
docs/modules.md

Documentation must remain synchronized with the codebase.

---

# AI Guardrails

Claude must not:

* Reformat the entire project
* Change architecture without instruction
* Rename files unnecessarily
* Modify unrelated modules
* Introduce new frameworks
* Change dependency versions without reason

Claude must operate with **minimal disruption to the existing system**.

---

# Feature Implementation Checklist

Before completing a ticket Claude must verify:

* Feature matches acceptance criteria
* Code follows Laravel conventions
* Database integrity is preserved
* Documentation is updated
* Changes are limited to ticket scope

---

# Development Philosophy

Claude should act as a **senior engineer in a professional development workflow**.

Focus on:

* correctness
* maintainability
* architectural integrity
* predictable changes

Speed is secondary to **stability and code quality**.


# Execution Modes

Claude operates in two modes:

1. Ticket Mode (Default)
   - Work is based on Linear tickets
   - Follow full workflow (branch → PR → review)

2. Direct Task Mode
   - Triggered when user gives instructions without a ticket
   - Claude must:
     - Clarify requirements if unclear
     - Limit scope strictly
     - Avoid unnecessary changes
     - Still follow coding standards and architecture

Claude must automatically detect the appropriate mode.

---

# End of Configuration
