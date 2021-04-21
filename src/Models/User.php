<?php

namespace Antares\Acl\Models;

use Antares\Acl\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get username key depending on request login field
     *
     * @return string
     */
    public function username()
    {
        $fieldValue = request('login');
        $fieldName = (!empty($fieldValue) and filter_var($fieldValue, FILTER_VALIDATE_EMAIL)) ? 'email' : 'username';

        if (empty(request($fieldName))) {
            request()->merge(["{$fieldName}" => "{$fieldValue}"]);
        }

        return $fieldName;
    }
}
