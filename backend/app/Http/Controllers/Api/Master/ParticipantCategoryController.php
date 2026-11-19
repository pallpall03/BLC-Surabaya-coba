<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\ParticipantCategory;
use Illuminate\Database\Eloquent\Model;

class ParticipantCategoryController extends SimpleCategoryController
{
    protected function model(): string
    {
        return ParticipantCategory::class;
    }

    protected function table(): string
    {
        return 'participant_categories';
    }

    protected function hasDescription(): bool
    {
        return true;
    }

    protected function isUsed(Model $category): bool
    {
        return $category->participants()->exists();
    }
}
