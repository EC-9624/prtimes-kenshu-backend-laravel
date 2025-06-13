<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\TagSeeder;
use App\Models\Tag;

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
