<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    protected $guarded = [];

    use HasFactory;
    public function firstPerson()
    {
        return $this->belongsTo(User::class, 'user1', 'id');
    }
    public function secondPerson()
    {
        return $this->belongsTo(User::class, 'user2', 'id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class, 'id', 'key');
    }
}