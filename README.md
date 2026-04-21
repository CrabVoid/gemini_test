# Tasker - Service-Oriented Client Management

A lightweight PHP application designed to manage and visualize hierarchical client data with a clean, service-oriented architecture.

---

## Quick Start Guide

### Prerequisites
- PHP 7.4+ with SQLite support
- Windows (or modify the batch file for other OS)

### Step 1: Start the Web Server
Double-click `start-server.bat` in the project root directory. You should see:
```
Starting PHP Development Server...
Navigate to: http://localhost:8000
Press Ctrl+C to stop the server
```

### Step 2: Initialize the Database (IMPORTANT - DO THIS FIRST!)
Open your browser and navigate to:
```
http://localhost:8000/init-db.php
```

This will:
- Create all database tables (clients, orders, products, order_items)
- Populate with test data
- Display a confirmation message

**⚠️ CRITICAL: You MUST visit this URL first before accessing the main app!**

### Step 3: View the Application
After initialization, navigate to:
```
http://localhost:8000/
```

You should see a list of customers with their orders and purchase history.

---

## Features

- **Client Overview:** View a complete list of customers with contact details
- **Order Hierarchy:** Drill down into specific orders for each client
- **Detailed Itemization:** See purchases with quantities and prices
- **Dynamic Calculation:** Automatic calculation of order totals
- **Toggle View:** Show/Hide Orders functionality via GET parameters
  - `http://localhost:8000/?with-orders=none` - Hide orders
  - `http://localhost:8000/?with-orders=full` - Show orders (default)
- **Secure Configuration:** Environment-based settings (.env support)

---

## Project Structure

```
tasker/
├── config.php                    # Configuration manager (.env loader)
├── ClientRepository.php          # Data fetching & mapping logic
├── Models.php                    # Data entity classes (Client, Order, Item)
├── start-server.bat              # Quick server startup script
├── db/
│   ├── Database.php              # SQLite PDO connection (Singleton)
│   ├── init.sql                  # Database schema
│   └── tasker.db                 # SQLite database file
├── public/
│   ├── index.php                 # Main entry point
│   └── init-db.php               # Database initialization (RUN THIS FIRST!)
├── src/
│   └── views/
│       └── customers.php         # Customer list HTML view
├── README.md                      # This file
└── .env.example                  # Environment variables template
```

---

## Database Schema

### Clients Table
- `id` - Primary key
- `firstname` - Client's first name
- `lastname` - Client's last name
- `email` - Unique email address
- `points` - Loyalty points

### Products Table
- `id` - Primary key
- `name` - Product name
- `price` - Product price

### Orders Table
- `id` - Primary key
- `client_id` - Foreign key to clients
- `order_date` - When the order was placed
- `status` - Order status (pending, shipped, completed)
- `delivery_date` - Delivery date (optional)

### Order Items Table
- `id` - Primary key
- `order_id` - Foreign key to orders
- `product_id` - Foreign key to products
- `quantity` - Number of units
- `price_at_purchase` - Price paid at time of order

---

## Setup for New Users (Step-by-Step)

1. **Extract/Clone the project** to your desired location
2. **Run `start-server.bat`** - This starts the PHP development server on port 8000
3. **Open browser to `http://localhost:8000/init-db.php`** - This initializes the database with tables and test data
4. **Open browser to `http://localhost:8000/`** - View the customer list
5. **To stop the server**, press `Ctrl+C` in the terminal window

---

## Test Data Included

The initialization script automatically creates:
- **4 Customers:** John Doe, Jane Smith, Bob Johnson, Alice Williams
- **5 Products:** Laptop, Mouse, Keyboard, Monitor, Headphones
- **5 Orders** across multiple customers
- **7 Order Items** distributed across the orders

---

## Configuration

Create a `.env` file in the project root to override defaults:

```
DB_FILE=/path/to/database.db
APP_ENV=development
```

If no `.env` file exists, defaults are used (database at `db/tasker.db`).

---

## Architecture

### Service-Oriented Design
- **ClientRepository** - Handles all data queries and object mapping
- **Database** - Singleton pattern ensures one connection
- **Models** - Simple data entities (Client, Order, OrderItem)
- **Views** - Separated HTML presentation logic

### Block Format Code Style
All PHP files use a "Block Format" style with clear section markers:
```php
// =========================================================================
// SECTION: Section Name
// Purpose: What this section does
// =========================================================================
```

This improves readability and maintainability for large code blocks.

---

## Troubleshooting

### "Cannot find php command"
- Ensure PHP is installed and added to your system PATH
- Or install PHP: https://www.php.net/downloads

### "SQLSTATE[HY000]: no such table"
- You must run `http://localhost:8000/init-db.php` first
- This creates and populates all required tables

### Server won't start
- Ensure port 8000 is not in use
- Close any other applications using that port
- Or modify `start-server.bat` to use a different port: `php -S localhost:9000 -t public`

---

## License

This project is part of VIBE-CODING Tasker suite.

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
