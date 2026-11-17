<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'description', 'created_by', 'is_active'])]
class Material extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function moduleFiles(): HasMany
    {
        return $this->hasMany(ModuleFile::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(TrainingClass::class);
    }
}
