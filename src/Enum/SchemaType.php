<?php

namespace App\Enum;

enum SchemaType: string
{
    case CreativeWork = 'CreativeWork';
    case WebSite = 'WebSite';
    case WebApplication = 'WebApplication';

    public function label(): string
    {
        return match ($this) {
            self::CreativeWork => 'Projet (générique)',
            self::WebSite => 'Site web',
            self::WebApplication => 'Application web',
        };
    }
}
