<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeIncludes($query) {
        return $query->with('user');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}