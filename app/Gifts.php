<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gifts extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    /**
     * Get the rating's product.
     *
     * @return string
     */
    public function getRatingAttribute($value)
    {
        return get_format_rating($value);
    }
}
