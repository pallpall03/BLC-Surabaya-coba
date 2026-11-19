<?php

namespace App\Http\Controllers\Api\Master;

use App\Models\VisitorCategory;
use Illuminate\Database\Eloquent\Model;

class VisitorCategoryController extends SimpleCategoryController
{
    protected function model(): string
    {
        return VisitorCategory::class;
    }

    protected function table(): string
    {
        return 'visitor_categories';
    }

    protected function isUsed(Model $category): bool
    {
        return $category->guestbookEntries()->exists();
    }
}
