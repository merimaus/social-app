<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasUlids;

    protected $fillable = ['body', 'user_id', 'image_path'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // This is what the error is missing!
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    // Remember to use reactions instead of likes
    /**
 * Get all likes/reactions for the post.
 */
/**
 * Get all reactions for the post.
 */
public function reactions()
{
    // If you created a Reaction model:
    return $this->hasMany(Reactions::class);
    
    // OR if you named your model Like, change it to:
    // return $this->hasMany(Like::class);
}
}