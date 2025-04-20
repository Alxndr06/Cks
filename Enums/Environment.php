<?php
namespace Enums;

enum Environment: string {
    case Development = 'development';
    case Production = 'production';

    public static function fromEnv(): self {
        return match ($_ENV['APP_ENV'] ?? 'production') {
            'development' => self::Development,
            default => self::Production,
        };
    }

}

