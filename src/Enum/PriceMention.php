<?php

namespace App\Enum;

enum PriceMention: string
{
    case StartingFrom = 'starting_from';
    case HostingNote  = 'hosting_note';

    public function label(): string
    {
        return match($this) {
            self::StartingFrom => 'À partir de',
            self::HostingNote  => 'Hébergement et nom de domaine non compris : entre 50 et 100 € par an, à votre nom, payés directement à l’hébergeur.',
        };
    }
}
