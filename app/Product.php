<?php

namespace App;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    //use HasSlug;

    protected $fillable = ['name', 'description', 'body', 'price', 'slug'];

    /**
     * Get the options for generating the slug.
     */
    /*public function getSlugOptions() : SlugOptions {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }*/

    public function getThumbAttribute() {
        return $this->photos->first()->image;
    }

    public function setNameAttribute($value) {

        $slug = Str::slug($value);
        $matchs = $this->uniqueSlug($slug);

        $this->attributes['name'] = $value;
        $this->attributes['slug'] = $matchs ? $slug . '-' .$matchs : $slug;
    }

    public function uniqueSlug($slug) {
        $matchs = $this->whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->count();
    }

    public function store() {
        return $this->belongsTo(Store::class);
    }

    public function categories() {
        return $this->belongsToMany(Category::class);
    }

    public function photos() {
        return $this->hasMany(ProductPhoto::class);
    }
}
