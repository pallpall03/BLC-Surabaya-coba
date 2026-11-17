<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['blc_id', 'user_id', 'visitor_category_id', 'group_name', 'visitor_count', 'purpose', 'visited_at'])]
class GuestbookEntry extends Model
{
    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'visitor_count' => 'integer',
        ];
    }

    public function blc(): BelongsTo
    {
        return $this->belongsTo(Blc::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VisitorCategory::class, 'visitor_category_id');
    }
}
