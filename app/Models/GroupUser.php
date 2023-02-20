<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupUser extends Model
{
    use HasFactory;

    protected $table = "group_user";

    protected $fillable = [
        'group_id',
        'user_id'
    ];
}
