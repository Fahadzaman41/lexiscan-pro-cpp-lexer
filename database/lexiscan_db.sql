CREATE DATABASE IF NOT EXISTS lexiscan_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lexiscan_db;

CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  username VARCHAR(80) NOT NULL UNIQUE,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role_status (role,status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  project_name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_projects_user (user_id),
  CONSTRAINT fk_projects_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE analyses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  project_id INT UNSIGNED NULL,
  analysis_title VARCHAR(180) NOT NULL,
  source_code LONGTEXT NOT NULL,
  source_filename VARCHAR(255) NULL,
  total_tokens INT UNSIGNED NOT NULL DEFAULT 0,
  keyword_count INT UNSIGNED NOT NULL DEFAULT 0,
  identifier_count INT UNSIGNED NOT NULL DEFAULT 0,
  constant_count INT UNSIGNED NOT NULL DEFAULT 0,
  operator_count INT UNSIGNED NOT NULL DEFAULT 0,
  separator_count INT UNSIGNED NOT NULL DEFAULT 0,
  special_character_count INT UNSIGNED NOT NULL DEFAULT 0,
  invalid_count INT UNSIGNED NOT NULL DEFAULT 0,
  analysis_status ENUM('completed','failed') NOT NULL DEFAULT 'completed',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_analyses_user_created (user_id,created_at),
  CONSTRAINT fk_analyses_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_analyses_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tokens (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  analysis_id INT UNSIGNED NOT NULL,
  token_number INT UNSIGNED NOT NULL,
  category VARCHAR(40) NOT NULL,
  lexeme TEXT NOT NULL,
  line_number INT UNSIGNED NOT NULL,
  is_valid TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_tokens_analysis_number (analysis_id,token_number),
  INDEX idx_tokens_category (category),
  CONSTRAINT fk_tokens_analysis FOREIGN KEY (analysis_id) REFERENCES analyses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE test_cases (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  test_name VARCHAR(160) NOT NULL,
  description TEXT NULL,
  source_code LONGTEXT NOT NULL,
  expected_behavior TEXT NOT NULL,
  category VARCHAR(60) NOT NULL DEFAULT 'General',
  created_by INT UNSIGNED NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_test_cases_active (is_active),
  CONSTRAINT fk_test_cases_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE test_runs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  test_case_id INT UNSIGNED NOT NULL,
  total_tokens INT UNSIGNED NOT NULL DEFAULT 0,
  passed_checks INT UNSIGNED NOT NULL DEFAULT 0,
  failed_checks INT UNSIGNED NOT NULL DEFAULT 0,
  status ENUM('passed','failed') NOT NULL,
  execution_time_ms INT UNSIGNED NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_test_runs_user_created (user_id,created_at),
  CONSTRAINT fk_test_runs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_test_runs_test FOREIGN KEY (test_case_id) REFERENCES test_cases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  action VARCHAR(100) NOT NULL,
  description TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_activity_logs_created (created_at),
  CONSTRAINT fk_activity_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO test_cases (test_name, description, source_code, expected_behavior, category) VALUES
('Variable assignment','Basic keyword, identifier, operator and separator','int value = 10;','At least one KEYWORD, IDENTIFIER, CONSTANT, OPERATOR and SEPARATOR','Basics'),
('Relational condition','Relational and logical operators','if (a >= 5 && b != 0)','Detect >=, && and != as operators','Operators'),
('Increment and assignment','Multiple longest-match operators','count++; total += 2;','Detect ++ and += using longest-match','Operators'),
('Stream output','Stream and string literals','cout << "a + b";','Detect << as operator and the complete string as CONSTANT','Operators'),
('Preprocessor include','#include with angle brackets','#include <iostream>','Detect #, < and > as applicable symbols/operators','Special'),
('Complete even-odd program','Comments and control flow','int main() {\n    cout << "Enter an integer: ";\n    cin >> n;\n    if (n % 2 == 0)\n        cout << n << " is even."; \n    else\n        cout << n << " is odd.";\n    return 0;\n}','Detect keywords, identifiers, constants, operators and separators','University'),
('Floating point','Numeric literals','float pi = 3.14;','Classify 3.14 as CONSTANT','Literals'),
('Character literal','Character and escape literals',"char c = '\\n';",'Classify the character literal as CONSTANT','Literals'),
('Escaped string','Quotation escape handling','cout << "She said \"Hello\"";','Keep the complete escaped string as one CONSTANT','Literals'),
('Line comment','Ignore comments','int x = 1; // comment','Ignore comment text and return normal tokens','Comments'),
('Block comment','Ignore multiline comments','int x = 1; /* one\ntwo */ x++;','Ignore comment text and retain line information','Comments'),
('Invalid character','Unsupported symbol','int x = 10 @ 2;','Report @ as SPECIAL_CHARACTER or INVALID','Errors'),
('Unterminated string','Malformed string','cout << "hello','Return an unterminated string error','Errors'),
('Unterminated comment','Malformed comment','int x = 1; /* comment','Return an unterminated block comment error','Errors');
