CREATE DATABASE IF NOT EXISTS donasi_db CHARACTER SET utf8 COLLATE utf8_general_ci;
USE donasi_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    target_amount DECIMAL(15,2) DEFAULT 0,
    collected_amount DECIMAL(15,2) DEFAULT 0,
    type ENUM('uang','barang','keduanya') DEFAULT 'keduanya',
    status ENUM('aktif','selesai','ditutup') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    campaign_id INT NOT NULL,
    type ENUM('uang','barang') NOT NULL,
    amount DECIMAL(15,2) DEFAULT NULL,
    item_name VARCHAR(200) DEFAULT NULL,
    item_qty INT DEFAULT NULL,
    item_unit VARCHAR(50) DEFAULT NULL,
    note TEXT,
    status ENUM('diproses','dikonfirmasi','selesai','ditolak') DEFAULT 'diproses',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
);

-- Admin default (password: admin123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@donasi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Sample campaigns
INSERT INTO campaigns (title, description, image, target_amount, type) VALUES
('Bantu Korban Banjir Cianjur', 'Donasi untuk membantu korban banjir di Cianjur yang membutuhkan bantuan segera.', 'banjir.jpg', 50000000, 'keduanya'),
('Beasiswa Anak Yatim', 'Program beasiswa untuk anak yatim agar bisa melanjutkan pendidikan.', 'beasiswa.jpg', 30000000, 'uang'),
('Sembako untuk Dhuafa', 'Pengumpulan sembako untuk keluarga kurang mampu di sekitar kita.', 'sembako.jpg', 0, 'barang');
