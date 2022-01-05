<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class UserHobby extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'hobby_id',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }
}
