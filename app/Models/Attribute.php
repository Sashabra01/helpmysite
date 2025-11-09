<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'is_filterable'];

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    public function products()
    {
        return $this->hasManyThrough(Product::class, AttributeValue::class);
    }
}
