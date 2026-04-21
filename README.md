# Tasker - Service-Oriented Client Management

// =========================================================================
// SECTION: Project Overview
// Purpose: High-level summary of the application's purpose.
// =========================================================================

Tasker is a lightweight PHP application designed to manage and visualize hierarchical client data. It features a clean, service-oriented architecture with a unique "Block Format" coding style for maximum readability and maintainability.

// -------------------------------------------------------------------------
// END SECTION: Project Overview
// -------------------------------------------------------------------------

// =========================================================================
// SECTION: Features (Funkcionalitāte)
// Purpose: List of key user-facing capabilities.
// =========================================================================

- **Client Overview:** View a complete list of customers including their IDs and contact details.
- **Order Hierarchy:** Drill down into specific orders for each client.
- **Detailed Itemization:** See exactly what was purchased, including quantities and unit prices.
- **Dynamic Calculation:** Automatic calculation of order totals.
- **Toggle View:** Flexible "Show/Hide Orders" functionality via GET parameters.
- **Secure Configuration:** Environment-based settings (.env) to keep sensitive data safe.

// -------------------------------------------------------------------------
// END SECTION: Features
// -------------------------------------------------------------------------

// =========================================================================
// SECTION: Project Structure (Struktūra)
// Purpose: Documentation of the file organization and responsibilities.
// =========================================================================

```text
tasker/
├── config.php          # Global configuration manager (.env loader)
├── ClientRepository.php # Centralized data mapping logic
├── Models.php          # Simple data entity classes (Client, Order, Item)
├── db/
│   ├── Database.php    # Singleton PDO connection wrapper
│   ├── tasker.db       # SQLite database file
│   └── init.sql        # Database schema definition
├── public/
│   ├── index.php       # Main Entry Point (Controller)
│   └── customers.php   # Specialized Customers Controller
├── src/
│   └── views/
│       └── customers.php # Reusable HTML View component
├── .env.example        # Template for environment variables
└── .gitignore          # Prevents sensitive files from being tracked
```

// -------------------------------------------------------------------------
// END SECTION: Project Structure
// -------------------------------------------------------------------------

// =========================================================================
// SECTION: Installation (Uzstādīšana)
// Purpose: Step-by-step guide to get the project running.
// =========================================================================

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd tasker
   ```

2. **Configure environment:**
   Copy the example environment file and update the `DB_FILE` path to match your local absolute path.
   ```bash
   cp .env.example .env
   ```

3. **Start the server:**
   Use the built-in PHP development server pointing to the `public` directory.
   ```bash
   php -S localhost:8000 -t public
   ```

4. **Access the app:**
   Open your browser and navigate to [http://localhost:8000/](http://localhost:8000/).

// -------------------------------------------------------------------------
// END SECTION: Installation
// -------------------------------------------------------------------------
