<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $tagSlug)
 * @method static pluck(string $string)
 */
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;
    protected $primaryKey = 'tag_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['name', 'slug'];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relationships
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags', 'tag_id', 'post_id')
            ->withTimestamps();
    }
}
