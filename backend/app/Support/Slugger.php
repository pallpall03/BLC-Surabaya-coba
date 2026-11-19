<?php

namespace App\Support;

use Illuminate\Support\Str;

class Slugger
{
    /**
     * Slug unik untuk model yang punya kolom `slug`. Kalau sudah dipakai, ditambah -2, -3, dst.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     */
    public static function unique(string $modelClass, string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'data';
        $slug = $base;
        $i = 2;

        while ($modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
