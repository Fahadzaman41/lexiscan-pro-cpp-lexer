<?php
declare(strict_types=1);

session_start();

const APP_NAME = 'LexiScan Pro';
const APP_BASE_URL = '/lexical_analyer';

require_once __DIR__ . '/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/csrf.php';
