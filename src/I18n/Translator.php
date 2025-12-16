<?php

namespace Experteam\IndentificationNumberValidator\I18n;

class Translator
{
    private static string $locale = 'en';
    private static array $messages = [];

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
        self::$messages = [];
    }

    public static function trans(string $key, array $replace = []): string
    {
        if (empty(self::$messages)) {
            self::loadMessages();
        }

        $message = self::$messages[$key] ?? $key;

        foreach ($replace as $search => $value) {
            $message = str_replace(':' . $search, $value, $message);
        }

        return $message;
    }

    private static function loadMessages(): void
    {
        $file = __DIR__ . "/lang/" . self::$locale . ".php";

        if (!file_exists($file)) {
            $file = __DIR__ . "/lang/en.php";
        }

        self::$messages = require $file;
    }
}
