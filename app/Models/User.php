<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'm_user';
    protected $primaryKey = 'UserID';
    public $timestamps = false;
    protected $guarded = [
        'UserID'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'Password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'Password' => 'hashed',
    ];

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }
    public function username()
    {
        return 'UserName';
    }
    public function getAuthIdentifierName()
    {
        return 'UserID';
    }

    public function erps()
    {
        return $this->belongsToMany(ERP::class, 'user_erp', 'UserID', 'ERPID');
    }

    public function creation()
    {
        return $this->hasMany(User::class, 'CreateUserID');
    }

    public function updation()
    {
        return $this->hasMany(User::class, 'UpdateUserID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'CreateUserID', 'UserID');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'UpdateUserID', 'UserID');
    }
}
