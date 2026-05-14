<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'color'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function publishedQuotes()
    {
        return $this->hasMany(Quote::class)->where('status', 'publish');
    }
}
