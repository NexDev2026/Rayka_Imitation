<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'hero_image',
        'hero_title',
        'hero_subtitle',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function navGroups(): BelongsToMany
    {
        return $this->belongsToMany(NavGroup::class, 'category_nav_group')
            ->withPivot('sort_order');
    }

    public function attributeGroups(): BelongsToMany
    {
        return $this->belongsToMany(AttributeGroup::class, 'category_attribute_group');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
