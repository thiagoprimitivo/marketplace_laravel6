<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    /**
     * Relação com o usuário - Store belongsTo User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
