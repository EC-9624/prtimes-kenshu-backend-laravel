<?php

namespace Tests\Unit\Models;

use App\Models\Tag;
use Database\Seeders\TagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_tag_seeder_creates_expected_tags()
    {
        $this->seed(TagSeeder::class);

        $expectedTags = [
            '総合',
            'テクノロジー',
            'モバイル',
            'アプリ',
            'エンタメ',
            'ビューティー',
            'ファッション',
            'ライフスタイル',
            'ビジネス',
            'グルメ',
            'スポーツ',
        ];

        $tags = Tag::pluck('name')->toArray();

        $this->assertCount(count($expectedTags), $tags);
        $this->assertSame($expectedTags, $tags);
    }
}
