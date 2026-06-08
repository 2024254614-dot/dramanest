-- =============================================
--  DramaNest — drama_mainframe.sql
--  Struktur pangkalan data sahaja (tanpa data sampel)
--  Jalankan dalam phpMyAdmin atau MySQL CLI
-- =============================================

-- -----------------------------------------------
-- Jadual utama drama
-- -----------------------------------------------
CREATE TABLE dramas (
    id          INT           NOT NULL AUTO_INCREMENT,
    title       VARCHAR(255)  NOT NULL,
    drama_type  VARCHAR(20)   NOT NULL COMMENT 'melayu|chinese|indo|jepun|korea|thailand|taiwan',
    genres      TEXT          NOT NULL,
    male_lead   VARCHAR(255)  NOT NULL DEFAULT '',
    female_lead VARCHAR(255)  NOT NULL DEFAULT '',
    episodes    INT           NOT NULL DEFAULT 0,
    start_date  DATE          DEFAULT NULL,
    end_date    DATE          DEFAULT NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_drama_type (drama_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Genre yang pernah ditulis (cadangan autocomplete)
-- -----------------------------------------------
CREATE TABLE saved_genres (
    id         INT          NOT NULL AUTO_INCREMENT,
    genre_name VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_genre_name (genre_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Galeri pelakon
-- -----------------------------------------------
CREATE TABLE actors (
    id            INT           NOT NULL AUTO_INCREMENT,
    name          VARCHAR(255)  NOT NULL,
    photo_url     VARCHAR(500)  NOT NULL DEFAULT '',
    drama_type    VARCHAR(20)   NOT NULL DEFAULT '',
    official_link VARCHAR(500)  NOT NULL DEFAULT '',
    role_type     ENUM('male','female') NOT NULL DEFAULT 'male',
    created_at    TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_actor_type (drama_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Log aktiviti (digunakan oleh logActivity() dalam config.php)
-- -----------------------------------------------
CREATE TABLE activity_log (
    id             INT          NOT NULL AUTO_INCREMENT,
    activity_type  VARCHAR(50)  NOT NULL,
    drama_title    VARCHAR(255) NOT NULL,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------
-- Genre awal untuk autocomplete
-- (benih sahaja — tiada data drama)
-- -----------------------------------------------
INSERT INTO saved_genres (genre_name) VALUES
('Romantik'),
('Drama'),
('Komedi'),
('Aksi'),
('Thriller'),
('Fantasi'),
('Sejarah'),
('Keluarga'),
('Remaja'),
('Sekolah'),
('BL'),
('Horror'),
('Seram'),
('Sukan'),
('Muzik'),
('Bisnes'),
('Keagamaan'),
('Politik'),
('Universiti'),
('Misteri'),
('Psikologi'),
('Jenayah'),
('Sains Fiksyen'),
('Animasi');
