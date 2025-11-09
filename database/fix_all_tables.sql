-- Создаем таблицу categories
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    parent_id INTEGER NULL,
    is_active BOOLEAN DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    meta_title TEXT NULL,
    meta_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);

-- Создаем таблицу brands
CREATE TABLE IF NOT EXISTS brands (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    logo VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    meta_title TEXT NULL,
    meta_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создаем таблицу products
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    short_description TEXT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0,
    sale_price DECIMAL(10, 2) NULL,
    cost_price DECIMAL(10, 2) NULL,
    stock INTEGER DEFAULT 0,
    low_stock_threshold INTEGER DEFAULT 5,
    weight DECIMAL(8, 2) NULL,
    dimensions VARCHAR(100) NULL,
    image VARCHAR(255) NULL,
    gallery TEXT NULL,
    category_id INTEGER NULL,
    brand_id INTEGER NULL,
    is_active BOOLEAN DEFAULT 1,
    is_featured BOOLEAN DEFAULT 0,
    is_virtual BOOLEAN DEFAULT 0,
    needs_shipping BOOLEAN DEFAULT 1,
    tax_rate DECIMAL(5, 2) DEFAULT 0,
    views INTEGER DEFAULT 0,
    rating DECIMAL(3, 2) DEFAULT 0,
    popularity INTEGER DEFAULT 0,
    meta_title TEXT NULL,
    meta_description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (brand_id) REFERENCES brands(id)
);

-- Создаем индексы
CREATE INDEX IF NOT EXISTS products_category_id_index ON products(category_id);
CREATE INDEX IF NOT EXISTS products_brand_id_index ON products(brand_id);
CREATE INDEX IF NOT EXISTS products_is_active_index ON products(is_active);
CREATE INDEX IF NOT EXISTS products_price_index ON products(price);
CREATE INDEX IF NOT EXISTS categories_parent_id_index ON categories(parent_id);