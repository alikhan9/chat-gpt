<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
    ];
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }
}
