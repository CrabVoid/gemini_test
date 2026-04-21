# Design Spec: Service-Oriented Block Refactor for Tasker

**Date:** 2026-04-21  
**Author:** Gemini CLI  
**Topic:** Code Refactoring into Service-Oriented Block Format

## 1. Goal
Refactor the current PHP codebase to improve readability, simplify logic, and enforce a consistent "block-based" visual structure. Each file will be divided into clearly labeled blocks separated by visual dividers.

## 2. Architecture & File Roles
The project will follow a clean separation of concerns:
- **`Database.php`**: Single-responsibility block for the Singleton PDO connection.
- **`Models.php`**: Simple, lightweight data classes (`Client`, `Order`, `OrderItem`).
- **`ClientRepository.php`**: Centralized logic for fetching and mapping nested relational data into objects.
- **`index.php`**: Clean entry point for orchestration and HTML rendering.
- **`db_connect.php`**: Deprecated; logic absorbed by `Database.php`.

## 3. The "Block Format" Visual Standard
Every file will use the following standard for marking sections:
```php
// =========================================================================
// SECTION: [Name]
// Purpose: [One-sentence description]
// =========================================================================
// ... code ...

// -------------------------------------------------------------------------
// END SECTION: [Name]
// -------------------------------------------------------------------------
```

## 4. Implementation Details
### 4.1 Simplification of Data Mapping
In `ClientRepository.php`, the complex `foreach` logic will be replaced with a more idiomatic "lookup-and-assign" pattern (using reference-based mapping or simpler key-checks) to reduce cognitive load.

### 4.2 Standardizing index.php
The current `index.php` manually queries the database. It will be refactored to use the `ClientRepository` exclusively, keeping the "View" logic separate from the "Data" logic.

### 4.3 Error Handling
Each file's "Execution" or "Connection" block will include basic `try-catch` logging to ensure the app doesn't crash silently on database errors.

## 5. Success Criteria
- [ ] Code is visually divided into the specified "block format".
- [ ] `index.php` no longer contains SQL queries.
- [ ] All data is correctly nested (Client -> Orders -> Items).
- [ ] Code is easier to explain line-by-line.
