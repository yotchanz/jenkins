<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keys extends Model
{
        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'keyid', 'keyname', 'accept_startdate', 'accept_enddate','cylinder_info'
    ];
}
