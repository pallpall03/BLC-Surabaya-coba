<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['blc_id', 'training_class_id', 'training_session_id', 'reported_by', 'activity_type', 'title', 'activity_date', 'kecamatan_id', 'kelurahan_id', 'location_detail', 'participant_count', 'summary'])]
class ActivityReport extends Model
{
    protected function casts(): array
    {
        return [
            'activity_type' => \App\Enums\ActivityType::class,
            'activity_date' => 'date',
            'participant_count' => 'integer',
        ];
    }

    public function blc(): BelongsTo
    {
        return $this->belongsTo(Blc::class);
    }

    public function trainingClass(): BelongsTo
    {
        return $this->belongsTo(TrainingClass::class, 'training_class_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class);
    }
}
