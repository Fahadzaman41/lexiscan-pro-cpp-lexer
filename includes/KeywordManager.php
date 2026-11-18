<?php
declare(strict_types=1);

class KeywordManager
{
    private array $keywords = [
        'int','float','double','char','void','bool','if','else','while','for','do','return','break',
        'continue','class','public','private','protected','const','static','switch','case','default',
        'using','namespace','struct','new','delete','try','catch','main','include','cin','cout','std'
    ];

    public function isKeyword(string $word): bool
    {
        return in_array($word, $this->keywords, true);
    }
}
