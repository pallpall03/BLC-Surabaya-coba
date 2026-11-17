<?php

namespace App\Enums;

enum UserRole: string
{
    case Superadmin = 'superadmin';
    case Admin = 'admin';
    case Koordinator = 'koordinator';
    case Instruktur = 'instruktur';
    case Peserta = 'peserta';
    case Pengunjung = 'pengunjung';

    public function label(): string
    {
        return match ($this) {
            self::Superadmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Koordinator => 'Koordinator',
            self::Instruktur => 'Instruktur',
            self::Peserta => 'Peserta',
            self::Pengunjung => 'Pengunjung',
        };
    }

    /** Role yang mengelola sistem (bukan pengguna layanan). */
    public function isStaff(): bool
    {
        return in_array($this, [self::Superadmin, self::Admin, self::Koordinator, self::Instruktur], true);
    }
}
