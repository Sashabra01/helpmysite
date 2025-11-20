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