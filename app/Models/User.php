<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, CanResetPassword;

    const TYPE_ADMIN = 'ADMIN';
    const TYPE_CUSTOMER = 'CUSTOMER';
    const TYPE_SELLER = 'SELLER';
    const TYPE_PROFESSIONAL = 'PROFESSIONAL';

    public function types(): array
    {
        return [
            self::TYPE_ADMIN,
            self::TYPE_CUSTOMER,
            self::TYPE_SELLER,
            self::TYPE_PROFESSIONAL,
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'firstname',
        'email',
        'password',
        'phone_number',
        'email_verified_at',
        'validated_at',
        'adress',
        'type',
        'image',
    ];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function get_name()
    {
        return "{$this->name} {$this->firstname}";
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function professional_data()
    {
        return $this->hasOne(ProfessionalData::class, 'user_id');
    }
}
