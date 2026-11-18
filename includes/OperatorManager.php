<?php
declare(strict_types=1);

class OperatorManager
{
    private array $operators = [
        '<<=', '>>=', '+=', '-=', '*=', '/=', '%=', '++', '--', '&&', '||', '==', '!=',
        '<=', '>=', '<<', '>>', '->', '::', '=', '+', '-', '*', '/', '%', '<', '>', '!',
        '&', '|', '^', '~', '.', '?', ':'
    ];

    public function longestMatch(string $source, int $index): ?string
    {
        foreach ($this->operators as $operator) {
            if (substr($source, $index, strlen($operator)) === $operator) {
                return $operator;
            }
        }
        return null;
    }

    public function isOperator(string $text): bool
    {
        return in_array($text, $this->operators, true);
    }
}
