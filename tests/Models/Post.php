<?php

namespace ToneflixCode\Stats\Tests\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ToneflixCode\Stats\HasStats;
use ToneflixCode\Stats\Statable;
use ToneflixCode\Stats\Tests\Database\Factories\PostFactory;

class Post extends Model implements Statable
{
    use HasFactory;
    use HasStats;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return PostFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
    ];

    /**
     * Get the user that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}