<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Operator = 'operator';
    case Koordinator = 'koordinator';
    case Instruktur = 'instruktur';
    case Pengguna = 'pengguna';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Operator => 'Operator',
            self::Koordinator => 'Koordinator',
            self::Instruktur => 'Instruktur',
            self::Pengguna => 'Pengguna',
        };
    }

    /** Role yang mengelola sistem (bukan pengguna layanan). */
    public function isStaff(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Operator, self::Koordinator, self::Instruktur], true);
    }

    /** Role yang bisa mengakses panel admin/master data. */
    public function canManageMasterData(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Operator], true);
    }

    /** Role yang bisa mengelola koordinator. */
    public function canManageKoordinator(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Operator], true);
    }

    /** Role yang bisa mengelola instruktur. */
    public function canManageInstruktur(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Operator], true);
    }

    /** Role yang bisa mengelola BLC. */
    public function canManageBlc(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Operator], true);
    }

    /** Role yang bisa melihat data koordinator. */
    public function canViewKoordinator(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Operator, self::Koordinator], true);
    }

    /** Role yang bisa melihat data instruktur. */
    public function canViewInstruktur(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Operator, self::Koordinator], true);
    }
}
