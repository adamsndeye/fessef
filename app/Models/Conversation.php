<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_one',
        'user_two',
        'statut'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
