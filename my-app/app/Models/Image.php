<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Image extends Model
{
    /** @use HasFactory<\Database\Factories\ImageFactory> */
    use HasFactory, HasUuids;

    protected $primaryKey = 'image_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['post_id', 'image_path', 'alt_text'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // Relationships
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', 'post_id');
    }
}
