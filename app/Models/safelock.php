<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class safelock extends Model
{
    use HasFactory;

    protected $table = 'safe_locks';
    protected $guarded=[];

}
