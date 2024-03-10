<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $table = 'claim';
    protected $guarded=[];

    function parentData()
    {
        return $this->belongsTo(givaway::class, 'giveaway_id','id');
    }
}
