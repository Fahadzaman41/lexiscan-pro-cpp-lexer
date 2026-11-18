<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/Lexer.php';

$cases = [
    'if (a >= 5 && b != 0)',
    'count++; total += 2;',
    'cout << "a + b";',
    'int x = 3.14;',
    'int x = 1; // comment',
    'int x = 1; /* comment',
    'cout << "hello',
    'int x = 10 @ 2;',
];

$lexer = new Lexer();
foreach ($cases as $source) {
    $result = $lexer->analyze($source);
    echo 'TOKENS=' . $result['total_tokens'] . ' INVALID=' . $result['invalid_count'] . ' ERRORS=' . count($result['errors']) . PHP_EOL;
}
