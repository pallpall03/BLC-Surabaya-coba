<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['test_id', 'participant_id', 'training_class_id', 'score', 'started_at', 'finished_at'])]
class TestAttempt extends Model
{
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function trainingClass(): BelongsTo
    {
        return $this->belongsTo(TrainingClass::class, 'training_class_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class, 'test_attempt_id');
    }
}
