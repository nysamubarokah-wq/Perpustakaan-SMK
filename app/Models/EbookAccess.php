<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookAccess extends Model
{
    protected $fillable = ['user_id', 'ebook_id'];
    
}
