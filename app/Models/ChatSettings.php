<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'temperature',
    ];
}
