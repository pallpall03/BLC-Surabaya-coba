<?php

namespace App\Enums;

enum BlcRegion: string
{
    case Pusat = 'pusat';
    case Utara = 'utara';
    case Selatan = 'selatan';
    case Timur = 'timur';
    case Barat = 'barat';

    public function label(): string
    {
        return 'Surabaya '.ucfirst($this->value);
    }
}
