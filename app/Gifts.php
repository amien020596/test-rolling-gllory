<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gifts extends Model
{

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
