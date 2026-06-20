<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    protected $fillable = ['slug', 'nama', 'harga', 'type', 'value'];
}