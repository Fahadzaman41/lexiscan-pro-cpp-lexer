<?php
declare(strict_types=1);

class Token
{
    public function __construct(
        public string $category,
        public string $lexeme,
        public int $lineNumber,
        public bool $isValid = true,
        public ?string $error = null
    ) {}

    public function toArray(int $tokenNumber): array
    {
        return [
            'token_number' => $tokenNumber,
            'category' => $this->category,
            'lexeme' => $this->lexeme,
            'line_number' => $this->lineNumber,
            'is_valid' => $this->isValid,
            'error' => $this->error,
        ];
    }
}
