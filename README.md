# LexiScan Pro (lexiscan-pro-cpp-lexer)

LexiScan Pro is a full-stack PHP/MySQL C++ lexical-analysis and token-management application for local XAMPP use.

## XAMPP setup

1. Install XAMPP and copy this project to `C:\xampp\htdocs\lexical_analyer`.
2. Start Apache and MySQL.
3. Open phpMyAdmin and import `database/lexiscan_db.sql`.
4. Confirm `config/database.php` credentials (default XAMPP host is `127.0.0.1`, user `root`, empty password).
5. Open `http://localhost/lexical_analyer/`.
6. Register a normal account through the registration page.

## Administrator setup

Create the first admin without storing a plain-text password in SQL:

1. Set `LEXISCAN_SETUP_TOKEN` in Apache's environment or the Windows process environment.
2. Open `http://localhost/lexical_analyer/create_admin.php?token=YOUR_TOKEN`.
3. Submit the administrator form.
4. Remove the environment token and restrict or remove `create_admin.php` after setup.

Example PowerShell session:

```powershell
$env:LEXISCAN_SETUP_TOKEN='a-long-random-token'
& C:\xampp\apache\bin\httpd.exe -k start
```

Restart Apache after changing environment variables. SQL credentials can also be overridden with `LEXISCAN_DB_HOST`, `LEXISCAN_DB_NAME`, `LEXISCAN_DB_USER`, and `LEXISCAN_DB_PASS`.

## Database relationships

- `users` owns many `projects`, `analyses`, `test_runs`, and `activity_logs`.
- `projects` owns many `analyses` through nullable `project_id`.
- `analyses` owns many `tokens`.
- `test_cases` owns many `test_runs`; `users` creates test cases through nullable `created_by`.
- Deleting a user cascades their projects, analyses, test runs, and activity logs. Deleting a project sets associated analyses' `project_id` to `NULL`. Deleting an analysis cascades tokens.

## Lexical algorithm

The PHP engine scans the complete source character-by-character. It maintains an index and line counter, skips whitespace and comments, recognizes identifiers and keywords, handles numeric, string, and character literals, applies longest-match operators, recognizes separators and supported special characters, and records unknown symbols and malformed constructs.

## Troubleshooting

- Database connection failure: start MySQL, import the SQL, and verify credentials.
- 403/CSRF errors: reload the page to refresh the session token and do not open multiple old tabs after logout.
- Upload failure: use `.cpp`, `.cc`, or `.h`, and keep files below 1 MB.
- 404 pages: use lowercase project path or configure XAMPP's `lexical_analyer` directory exactly.
- Blank page: enable PHP display temporarily or inspect `C:\xampp\apache\logs\error.log`.
- Permission errors: ensure Apache can read project files and write only required upload/report directories.

## Academic note

This PHP adaptation preserves the required six broad token categories and lexical behavior. If the instructor requires C++ as the implementation language, the PHP implementation must not be represented as a C++ deliverable without approval.
