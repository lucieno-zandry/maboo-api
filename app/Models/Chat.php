<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public function chatUser()
    {
        return $this->hasOne(ChatUser::class, 'chat_id');
    }
}
