<?php
declare(strict_types=1);

require_once __DIR__ . '/Token.php';
require_once __DIR__ . '/KeywordManager.php';
require_once __DIR__ . '/OperatorManager.php';

class Lexer
{
    private int $index = 0;
    private int $line = 1;
    private array $tokens = [];
    private array $errors = [];
    private KeywordManager $keywords;
    private OperatorManager $operators;

    public function __construct()
    {
        $this->keywords = new KeywordManager();
        $this->operators = new OperatorManager();
    }

    public function analyze(string $source): array
    {
        $this->index = 0;
        $this->line = 1;
        $this->tokens = [];
        $this->errors = [];

        $length = strlen($source);
        while ($this->index < $length) {
            $char = $source[$this->index];

            if (ctype_space($char)) {
                if ($char === "\n") {
                    $this->line++;
                }
                $this->index++;
                continue;
            }

            if ($char === '/' && substr($source, $this->index, 2) === '//') {
                $this->index += 2;
                while ($this->index < $length && $source[$this->index] !== "\n") {
                    $this->index++;
                }
                continue;
            }

            if ($char === '/' && substr($source, $this->index, 2) === '/*') {
                $startLine = $this->line;
                $this->index += 2;
                $closed = false;
                while ($this->index < $length) {
                    if (substr($source, $this->index, 2) === '*/') {
                        $this->index += 2;
                        $closed = true;
                        break;
                    }
                    if ($source[$this->index] === "\n") {
                        $this->line++;
                    }
                    $this->index++;
                }
                if (!$closed) {
                    $this->addError('Unterminated block comment.', $startLine);
                }
                continue;
            }

            if (ctype_alpha($char) || $char === '_') {
                $this->scanIdentifier($source);
                continue;
            }

            if (ctype_digit($char)) {
                $this->scanNumber($source);
                continue;
            }

            if ($char === '"') {
                $this->scanString($source);
                continue;
            }

            if ($char === "'") {
                $this->scanCharacter($source);
                continue;
            }

            $operator = $this->operators->longestMatch($source, $this->index);
            if ($operator !== null) {
                $this->addToken('OPERATOR', $operator);
                $this->index += strlen($operator);
                continue;
            }

            if (in_array($char, [';', ',', '(', ')', '{', '}', '[', ']'], true)) {
                $this->addToken('SEPARATOR', $char);
                $this->index++;
                continue;
            }

            if ($char === '#' || $char === '@') {
                $this->addToken('SPECIAL_CHARACTER', $char);
                $this->index++;
                continue;
            }

            $this->addToken('UNKNOWN/INVALID', $char, false, 'Unsupported character.');
            $this->index++;
        }

        $numbered = [];
        foreach ($this->tokens as $i => $token) {
            $numbered[] = $token->toArray($i + 1);
        }

        $counts = [
            'KEYWORD' => 0, 'IDENTIFIER' => 0, 'CONSTANT' => 0, 'OPERATOR' => 0,
            'SEPARATOR' => 0, 'SPECIAL_CHARACTER' => 0, 'UNKNOWN/INVALID' => 0
        ];
        foreach ($numbered as $token) {
            $category = $token['category'];
            if (isset($counts[$category])) {
                $counts[$category]++;
            }
        }

        return [
            'tokens' => $numbered,
            'counts' => $counts,
            'errors' => $this->errors,
            'total_tokens' => count($numbered),
            'invalid_count' => $counts['UNKNOWN/INVALID'],
        ];
    }

    private function scanIdentifier(string $source): void
    {
        $start = $this->index;
        while ($this->index < strlen($source)) {
            $char = $source[$this->index];
            if (!(ctype_alnum($char) || $char === '_')) {
                break;
            }
            $this->index++;
        }
        $word = substr($source, $start, $this->index - $start);
        if ($word === 'true' || $word === 'false') {
            $this->addToken('CONSTANT', $word);
            return;
        }
        $category = $this->keywords->isKeyword($word) ? 'KEYWORD' : 'IDENTIFIER';
        $this->addToken($category, $word);
    }

    private function scanNumber(string $source): void
    {
        $start = $this->index;
        $length = strlen($source);
        $hasDot = false;
        $hasExponent = false;

        while ($this->index < $length && ctype_digit($source[$this->index])) {
            $this->index++;
        }
        if ($this->index < $length && $source[$this->index] === '.') {
            $hasDot = true;
            $this->index++;
            while ($this->index < $length && ctype_digit($source[$this->index])) {
                $this->index++;
            }
        }
        if ($this->index < $length && ($source[$this->index] === 'e' || $source[$this->index] === 'E')) {
            $hasExponent = true;
            $this->index++;
            if ($this->index < $length && ($source[$this->index] === '+' || $source[$this->index] === '-')) {
                $this->index++;
            }
            $exponentStart = $this->index;
            while ($this->index < $length && ctype_digit($source[$this->index])) {
                $this->index++;
            }
            if ($this->index === $exponentStart) {
                $lexeme = substr($source, $start, $this->index - $start);
                $this->addToken('UNKNOWN/INVALID', $lexeme, false, 'Invalid exponent in numeric literal.');
                return;
            }
        }

        $lexeme = substr($source, $start, $this->index - $start);
        if (($this->index < $length) && (ctype_alnum($source[$this->index]) || $source[$this->index] === '_')) {
            while ($this->index < $length && (ctype_alnum($source[$this->index]) || $source[$this->index] === '_')) {
                $this->index++;
            }
            $lexeme = substr($source, $start, $this->index - $start);
            $this->addToken('UNKNOWN/INVALID', $lexeme, false, 'Invalid numeric literal.');
            return;
        }
        $this->addToken('CONSTANT', $lexeme);
    }

    private function scanString(string $source): void
    {
        $startLine = $this->line;
        $start = $this->index++;
        $closed = false;
        $length = strlen($source);
        while ($this->index < $length) {
            $char = $source[$this->index];
            if ($char === '\\') {
                $this->index += 2;
                continue;
            }
            if ($char === '"') {
                $this->index++;
                $closed = true;
                break;
            }
            if ($char === "\n") {
                $this->line++;
            }
            $this->index++;
        }
        $lexeme = substr($source, $start, $this->index - $start);
        if (!$closed) {
            $this->addToken('UNKNOWN/INVALID', $lexeme, false, 'Unterminated string literal.');
            $this->addError('Unterminated string literal.', $startLine);
        } else {
            $this->addToken('CONSTANT', $lexeme);
        }
    }

    private function scanCharacter(string $source): void
    {
        $startLine = $this->line;
        $start = $this->index++;
        $closed = false;
        $length = strlen($source);
        while ($this->index < $length) {
            $char = $source[$this->index];
            if ($char === '\\') {
                $this->index += 2;
                continue;
            }
            if ($char === "'") {
                $this->index++;
                $closed = true;
                break;
            }
            if ($char === "\n") {
                $this->line++;
            }
            $this->index++;
        }
        $lexeme = substr($source, $start, $this->index - $start);
        if (!$closed) {
            $this->addToken('UNKNOWN/INVALID', $lexeme, false, 'Unterminated character literal.');
            $this->addError('Unterminated character literal.', $startLine);
        } else {
            $this->addToken('CONSTANT', $lexeme);
        }
    }

    private function addToken(string $category, string $lexeme, bool $isValid = true, ?string $error = null): void
    {
        $this->tokens[] = new Token($category, $lexeme, $this->line, $isValid, $error);
        if ($error) {
            $this->errors[] = ['message' => $error, 'line' => $this->line];
        }
    }

    private function addError(string $message, int $line): void
    {
        $this->errors[] = ['message' => $message, 'line' => $line];
    }
}
