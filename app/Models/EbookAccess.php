<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookAccess extends Model
{
    protected $fillable = ['user_id', 'ebook_id'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function ebook()
    {
        return $this->belongsTo(\App\Models\Ebook::class);
    }
}
