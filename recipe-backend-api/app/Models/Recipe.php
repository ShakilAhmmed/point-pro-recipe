<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;

    const PUBLIC_VISIBLE = 1;

    protected $table = 'recipes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'cuisine_type',
        'image',
        'user_id',
        'visibility'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_no');
    }

    public function isPublic(): bool
    {
        return $this->visibility === self::PUBLIC_VISIBLE;
    }
}
