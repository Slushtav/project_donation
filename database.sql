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

-- Admin default (password: password)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@donasi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Admin Campaign', 'campaign@donasi.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Campaign awal: semua menerima donasi uang dan barang
INSERT INTO campaigns (title, description, image, target_amount, type) VALUES
('Korban Banjir Cianjur', 'Bantuan untuk warga terdampak banjir Cianjur berupa dana darurat, makanan, pakaian, dan kebutuhan pokok.', 'banjir-cianjur.jpg', 50000000, 'keduanya'),
('Beasiswa Anak Yatim', 'Program beasiswa untuk anak yatim agar tetap bisa melanjutkan pendidikan dan membeli perlengkapan sekolah.', 'beasiswa-anak-yatim.jpg', 40000000, 'keduanya'),
('Korban Longsor Lombok', 'Bantuan pemulihan untuk korban longsor Lombok, termasuk kebutuhan logistik, obat-obatan, dan tempat tinggal sementara.', 'longsor-lombok.jpg', 60000000, 'keduanya');
