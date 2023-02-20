<?php

namespace App\Models;

use App\Models\Chat;
use App\Models\User;
use App\Models\GroupUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'creator_id'
    ];

    public function members()
    {
        return $this->belongsToMany(User::class);
    }

    public function chat()
    {
        return $this->hasMany(Chat::class);
    }

    public function imageName()
    {
        return array_reduce(
            explode(' ', $this->group_name),
            function ($initials, $word) {
                return sprintf('%s%s', $initials, substr($word, 0, 1));
            },
            ''
        );
    }
}
