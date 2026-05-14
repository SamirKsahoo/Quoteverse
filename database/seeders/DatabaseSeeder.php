<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@quoteverse.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Categories
        $categories = [
            ['name' => 'Motivation', 'slug' => 'motivation', 'icon' => '🔥', 'color' => '#f97316'],
            ['name' => 'Love',       'slug' => 'love',       'icon' => '❤️', 'color' => '#ec4899'],
            ['name' => 'Life',       'slug' => 'life',       'icon' => '🌿', 'color' => '#22c55e'],
            ['name' => 'Success',    'slug' => 'success',    'icon' => '⭐', 'color' => '#eab308'],
            ['name' => 'Happiness',  'slug' => 'happiness',  'icon' => '😊', 'color' => '#06b6d4'],
            ['name' => 'Wisdom',     'slug' => 'wisdom',     'icon' => '🦉', 'color' => '#8b5cf6'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }

        // Sample quotes
        $quotes = [
            [
                'title'           => 'The Power Within',
                'quote_text'      => 'Believe you can and you\'re halfway there.',
                'author'          => 'Theodore Roosevelt',
                'category_id'     => 1,
                'text_color'      => '#ffffff',
                'text_position'   => 'center',
                'font_size'       => '3xl',
                'font_style'      => 'serif',
                'overlay_opacity' => 50,
                'overlay_color'   => '#000000',
                'status'          => 'publish',
            ],
            [
                'title'           => 'Rise Every Time',
                'quote_text'      => 'Our greatest glory is not in never falling, but in rising every time we fall.',
                'author'          => 'Confucius',
                'category_id'     => 1,
                'text_color'      => '#fef3c7',
                'text_position'   => 'bottom-center',
                'font_size'       => '2xl',
                'font_style'      => 'serif',
                'overlay_opacity' => 60,
                'overlay_color'   => '#1e1b4b',
                'status'          => 'publish',
            ],
            [
                'title'           => 'Love Deeply',
                'quote_text'      => 'The best thing to hold onto in life is each other.',
                'author'          => 'Audrey Hepburn',
                'category_id'     => 2,
                'text_color'      => '#fce7f3',
                'text_position'   => 'center',
                'font_size'       => '2xl',
                'font_style'      => 'serif',
                'overlay_opacity' => 45,
                'overlay_color'   => '#831843',
                'status'          => 'publish',
            ],
            [
                'title'           => 'Life is Beautiful',
                'quote_text'      => 'In the end, it\'s not the years in your life that count. It\'s the life in your years.',
                'author'          => 'Abraham Lincoln',
                'category_id'     => 3,
                'text_color'      => '#dcfce7',
                'text_position'   => 'center',
                'font_size'       => '2xl',
                'font_style'      => 'serif',
                'overlay_opacity' => 55,
                'overlay_color'   => '#14532d',
                'status'          => 'publish',
            ],
            [
                'title'           => 'Path to Success',
                'quote_text'      => 'Success is not the key to happiness. Happiness is the key to success.',
                'author'          => 'Albert Schweitzer',
                'category_id'     => 4,
                'text_color'      => '#fefce8',
                'text_position'   => 'center',
                'font_size'       => '2xl',
                'font_style'      => 'serif',
                'overlay_opacity' => 50,
                'overlay_color'   => '#713f12',
                'status'          => 'publish',
            ],
            [
                'title'           => 'Choose Joy',
                'quote_text'      => 'Happiness is not something ready-made. It comes from your own actions.',
                'author'          => 'Dalai Lama',
                'category_id'     => 5,
                'text_color'      => '#cffafe',
                'text_position'   => 'center',
                'font_size'       => '2xl',
                'font_style'      => 'serif',
                'overlay_opacity' => 50,
                'overlay_color'   => '#164e63',
                'status'          => 'publish',
            ],
        ];

        foreach ($quotes as $q) {
            $q['slug'] = \Illuminate\Support\Str::slug($q['title']) . '-' . \Illuminate\Support\Str::random(5);
            Quote::create($q);
        }
    }
}
