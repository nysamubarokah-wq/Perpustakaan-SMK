<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorit extends Model
{
    protected $table = 'favorit';
    protected $fillable = ['user_id', 'buku_id'];

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}