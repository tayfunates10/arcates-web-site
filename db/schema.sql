SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE settings (
  `key` VARCHAR(100) PRIMARY KEY,
  `value` LONGTEXT NULL,
  autoload TINYINT(1) NOT NULL DEFAULT 1,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  status TINYINT(1) NOT NULL DEFAULT 1,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE login_attempts (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARBINARY(16) NOT NULL,
  email VARCHAR(190) NULL,
  success TINYINT(1) NOT NULL DEFAULT 0,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_time (ip, attempted_at),
  INDEX idx_email_time (email, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_log (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  action VARCHAR(60) NOT NULL,
  entity VARCHAR(60) NULL,
  entity_id INT UNSIGNED NULL,
  detail TEXT NULL,
  ip VARBINARY(16) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user_time (user_id, created_at),
  CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE languages (
  code CHAR(2) PRIMARY KEY,
  name VARCHAR(60) NOT NULL,
  direction ENUM('ltr','rtl') NOT NULL DEFAULT 'ltr',
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE migrations (
  filename VARCHAR(190) PRIMARY KEY,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(190) NOT NULL,
  path VARCHAR(255) NOT NULL,
  mime VARCHAR(80) NOT NULL,
  size INT UNSIGNED NOT NULL,
  width SMALLINT UNSIGNED NULL,
  height SMALLINT UNSIGNED NULL,
  variants JSON NULL,
  user_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_media_user (user_id),
  CONSTRAINT fk_media_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED NULL,
  type ENUM('page','service','location','sector') NOT NULL DEFAULT 'page',
  template VARCHAR(60) NOT NULL DEFAULT 'page',
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  cover_id INT UNSIGNED NULL,
  district VARCHAR(60) NULL,
  sort SMALLINT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_type_status (type, status),
  INDEX idx_parent (parent_id),
  CONSTRAINT fk_page_parent FOREIGN KEY (parent_id) REFERENCES pages(id) ON DELETE SET NULL,
  CONSTRAINT fk_page_cover FOREIGN KEY (cover_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE page_translations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  excerpt VARCHAR(400) NULL,
  content LONGTEXT NULL,
  meta_title VARCHAR(180) NULL,
  meta_description VARCHAR(320) NULL,
  og_image_id INT UNSIGNED NULL,
  canonical VARCHAR(255) NULL,
  robots VARCHAR(40) NOT NULL DEFAULT 'index,follow',
  schema_type VARCHAR(40) NULL,
  word_count SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY uq_lang_slug (lang, slug),
  INDEX idx_page_lang (page_id, lang),
  CONSTRAINT fk_pt_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
  CONSTRAINT fk_pt_lang FOREIGN KEY (lang) REFERENCES languages(code) ON DELETE RESTRICT,
  CONSTRAINT fk_pt_og FOREIGN KEY (og_image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  client_name VARCHAR(150) NOT NULL,
  sector VARCHAR(80) NULL,
  district VARCHAR(60) NULL,
  live_url VARCHAR(255) NULL,
  cover_id INT UNSIGNED NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  sort SMALLINT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_project_cover FOREIGN KEY (cover_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_translations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  excerpt VARCHAR(400) NULL,
  content LONGTEXT NULL,
  meta_title VARCHAR(180) NULL,
  meta_description VARCHAR(320) NULL,
  UNIQUE KEY uq_project_lang (project_id, lang),
  UNIQUE KEY uq_project_slug (lang, slug),
  CONSTRAINT fk_prt_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  CONSTRAINT fk_prt_lang FOREIGN KEY (lang) REFERENCES languages(code) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cover_id INT UNSIGNED NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_post_status_date (status, published_at),
  CONSTRAINT fk_post_cover FOREIGN KEY (cover_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_translations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL,
  excerpt VARCHAR(400) NULL,
  content LONGTEXT NULL,
  meta_title VARCHAR(180) NULL,
  meta_description VARCHAR(320) NULL,
  UNIQUE KEY uq_post_lang (post_id, lang),
  UNIQUE KEY uq_post_slug (lang, slug),
  CONSTRAINT fk_pot_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_pot_lang FOREIGN KEY (lang) REFERENCES languages(code) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE faqs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sort SMALLINT NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE faq_translations (
  faq_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  question VARCHAR(255) NOT NULL,
  answer TEXT NOT NULL,
  PRIMARY KEY (faq_id, lang),
  CONSTRAINT fk_fqt_faq FOREIGN KEY (faq_id) REFERENCES faqs(id) ON DELETE CASCADE,
  CONSTRAINT fk_fqt_lang FOREIGN KEY (lang) REFERENCES languages(code) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE faq_page (
  faq_id INT UNSIGNED NOT NULL,
  page_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (faq_id, page_id),
  CONSTRAINT fk_fp_faq FOREIGN KEY (faq_id) REFERENCES faqs(id) ON DELETE CASCADE,
  CONSTRAINT fk_fp_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE home_sections (
  `key` VARCHAR(40) PRIMARY KEY,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort SMALLINT NOT NULL DEFAULT 0,
  config JSON NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE home_section_translations (
  section_key VARCHAR(40) NOT NULL,
  lang CHAR(2) NOT NULL,
  content JSON NOT NULL,
  PRIMARY KEY (section_key, lang),
  CONSTRAINT fk_hst_section FOREIGN KEY (section_key) REFERENCES home_sections(`key`) ON DELETE CASCADE,
  CONSTRAINT fk_hst_lang FOREIGN KEY (lang) REFERENCES languages(code) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE districts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL,
  map_x SMALLINT NOT NULL,
  map_y SMALLINT NOT NULL,
  label_above TINYINT(1) NOT NULL DEFAULT 0,
  page_id INT UNSIGNED NULL,
  sort SMALLINT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_district_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media_translations (
  media_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  alt VARCHAR(255) NULL,
  title VARCHAR(255) NULL,
  PRIMARY KEY (media_id, lang),
  CONSTRAINT fk_mt_media FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
  CONSTRAINT fk_mt_lang FOREIGN KEY (lang) REFERENCES languages(code) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE submissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  form_key VARCHAR(60) NOT NULL DEFAULT 'contact',
  name VARCHAR(150) NULL,
  email VARCHAR(190) NULL,
  phone VARCHAR(40) NULL,
  service VARCHAR(80) NULL,
  message TEXT NULL,
  source_url VARCHAR(255) NULL,
  referrer VARCHAR(255) NULL,
  utm JSON NULL,
  lang CHAR(2) NULL,
  kvkk_consent TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('new','contacted','quoted','won','lost') NOT NULL DEFAULT 'new',
  note TEXT NULL,
  ip VARBINARY(16) NULL,
  user_agent VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status_time (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE redirects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_path VARCHAR(255) NOT NULL UNIQUE,
  to_path VARCHAR(255) NOT NULL,
  code SMALLINT NOT NULL DEFAULT 301,
  hits INT UNSIGNED NOT NULL DEFAULT 0,
  last_hit_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE not_found (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path VARCHAR(255) NOT NULL UNIQUE,
  hits INT UNSIGNED NOT NULL DEFAULT 1,
  referrer VARCHAR(255) NULL,
  last_seen DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE visits (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path VARCHAR(255) NOT NULL,
  session_hash CHAR(64) NOT NULL,
  referrer VARCHAR(255) NULL,
  device ENUM('desktop','mobile','tablet','bot') NOT NULL DEFAULT 'desktop',
  lang CHAR(2) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_time (created_at),
  INDEX idx_path_time (path, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE visits_daily (
  day DATE NOT NULL,
  path VARCHAR(255) NOT NULL,
  views INT UNSIGNED NOT NULL DEFAULT 0,
  sessions INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (day, path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  menu_key VARCHAR(40) NOT NULL,
  parent_id INT UNSIGNED NULL,
  page_id INT UNSIGNED NULL,
  url VARCHAR(255) NULL,
  target ENUM('_self','_blank') NOT NULL DEFAULT '_self',
  sort SMALLINT NOT NULL DEFAULT 0,
  INDEX idx_menu (menu_key, sort),
  CONSTRAINT fk_menu_parent FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE,
  CONSTRAINT fk_menu_page FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu_item_translations (
  menu_item_id INT UNSIGNED NOT NULL,
  lang CHAR(2) NOT NULL,
  label VARCHAR(120) NOT NULL,
  PRIMARY KEY (menu_item_id, lang),
  CONSTRAINT fk_mit_item FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
  CONSTRAINT fk_mit_lang FOREIGN KEY (lang) REFERENCES languages(code) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
