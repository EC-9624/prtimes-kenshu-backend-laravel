<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $tags = [
            ['name' => '総合', 'slug' => 'general'],
            ['name' => 'テクノロジー', 'slug' => 'technology'],
            ['name' => 'モバイル', 'slug' => 'mobile'],
            ['name' => 'アプリ', 'slug' => 'apps'],
            ['name' => 'エンタメ', 'slug' => 'entertainment'],
            ['name' => 'ビューティー', 'slug' => 'beauty'],
            ['name' => 'ファッション', 'slug' => 'fashion'],
            ['name' => 'ライフスタイル', 'slug' => 'lifestyle'],
            ['name' => 'ビジネス', 'slug' => 'business'],
            ['name' => 'グルメ', 'slug' => 'gourmet'],
            ['name' => 'スポーツ', 'slug' => 'sports'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
