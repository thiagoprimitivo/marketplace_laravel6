<?php

namespace App;

//use Spatie\Sluggable\HasSlug;
//use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SlugTrait;

class Category extends Model
{
    //use HasSlug;
    use SlugTrait;

    protected $fillable = ['name', 'description', 'slug'];

    /**
     * Get the options for generating the slug.
     */
    /*public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }*/

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
