PRAGMA foreign_keys = ON;

-- Clients (Klienti)
CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    firstname TEXT NOT NULL,
    lastname TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    points INTEGER DEFAULT 0
);

-- Delivery Companies (Piegādes uzņēmumi)
CREATE TABLE IF NOT EXISTS delivery_companies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    comments TEXT,
    base_cost REAL DEFAULT 0,
    cost_per_kg REAL DEFAULT 0
);

-- Products (Preces)
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price REAL NOT NULL, -- Sell Price (Pārdošanas cena)
    buy_price REAL DEFAULT 0, -- Buy Price (Iepirkuma cena)
    weight REAL DEFAULT 0, -- Weight in kg (Svars kg)
    source TEXT -- Origin/Supplier description
);

-- Orders (Pasūtījumi)
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    delivery_company_id INTEGER,
    order_date TEXT DEFAULT (datetime('now')),
    status TEXT NOT NULL,
    delivery_date TEXT,
    tax_rate REAL DEFAULT 0.21, -- VAT 21%
    shipping_cost REAL DEFAULT 0,
    total_profit REAL DEFAULT 0,
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (delivery_company_id) REFERENCES delivery_companies(id)
);

-- Order Items (Pasūtījuma preces)
CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    price_at_purchase REAL NOT NULL, -- Sell price at purchase
    buy_price_at_purchase REAL NOT NULL, -- Buy price at purchase
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
