<?php

namespace App;

use App\Notifications\StoreReceiveNewOrder;
//use Spatie\Sluggable\HasSlug;
//use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use App\Traits\SlugTrait;

class Store extends Model
{
    //use HasSlug;
    use SlugTrait;

    protected $fillable = ['name', 'description', 'phone', 'mobile_phone', 'slug', 'logo'];

    /**
     * Get the options for generating the slug.
     */
    /*public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }*/

    /**
     * Relação com o usuário - Store belongsTo User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->belongsToMany(UserOrder::class, 'order_store', 'store_id', 'order_id');
    }

    public function notifyStoreOwners(array $storesId = [])
    {
        $stores = $this->whereIn('id', $storesId)->get();

        $stores->map(function($store){
            return $store->user;
        })->each->notify(new StoreReceiveNewOrder);
    }
}
