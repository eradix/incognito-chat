<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Chat;
use App\Models\Group;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'profile_image'
    ];

    public function sentMessages()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }
    public function receivedMessages()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }

    public function imageName()
    {
        return array_reduce(
            explode(' ', $this->name),
            function ($initials, $word) {
                return sprintf('%s%s', $initials, substr($word, 0, 1));
            },
            ''
        );
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    // public function getMessageForEachGroups(){
    //     return User::find($this->id)->groups()->orderBy('group_name')->get()->chat()->latest()->first(); 
    // }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
