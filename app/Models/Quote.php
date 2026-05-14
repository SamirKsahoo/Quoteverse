<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quote extends Model
{
    protected $fillable = [
        'title', 'quote_text', 'image', 'author', 'category_id',
        'text_color', 'text_position', 'font_size', 'font_style',
        'overlay_opacity', 'overlay_color', 'status', 'slug', 'views'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($quote) {
            if (empty($quote->slug)) {
                $quote->slug = Str::slug($quote->title) . '-' . Str::random(5);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-quote-bg.jpg');
    }

    public function getTextPositionClassAttribute()
    {
        $positions = [
            'top-left'      => 'items-start justify-start',
            'top-center'    => 'items-start justify-center',
            'top-right'     => 'items-start justify-end',
            'center-left'   => 'items-center justify-start',
            'center'        => 'items-center justify-center',
            'center-right'  => 'items-center justify-end',
            'bottom-left'   => 'items-end justify-start',
            'bottom-center' => 'items-end justify-center',
            'bottom-right'  => 'items-end justify-end',
        ];
        return $positions[$this->text_position] ?? 'items-center justify-center';
    }

    public function getFontSizeClassAttribute()
    {
        $sizes = [
            'sm'   => 'text-sm',
            'base' => 'text-base',
            'lg'   => 'text-lg',
            'xl'   => 'text-xl',
            '2xl'  => 'text-2xl',
            '3xl'  => 'text-3xl',
            '4xl'  => 'text-4xl',
            '5xl'  => 'text-5xl',
        ];
        return $sizes[$this->font_size] ?? 'text-2xl';
    }

    public function getFontStyleClassAttribute()
    {
        return $this->font_style === 'serif' ? 'font-serif' : 'font-sans';
    }

    public function incrementViews()
    {
        $this->increment('views');
    }
}
