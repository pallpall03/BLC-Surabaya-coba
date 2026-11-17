<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['training_class_id', 'participant_id', 'status', 'registered_at'])]
class Enrollment extends Model
{
    protected function casts(): array
    {
        return [
            'status' => \App\Enums\EnrollmentStatus::class,
            'registered_at' => 'datetime',
        ];
    }

    public function trainingClass(): BelongsTo
    {
        return $this->belongsTo(TrainingClass::class, 'training_class_id');
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }
}
