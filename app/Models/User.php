<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'is_active', 'type', 'head_id'
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
        'email_verified_at, deleted_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class, 'user_id', 'id');
    }

    public function head()
    {
        return $this->hasOne(User::class, 'id', 'head_id');
    }

    public function getTeam(User $head)
    {
        $team = User::where('is_active', true)
            ->where('head_id', $head->id)
            ->where('type', 'Vendedor')
            ->get()->toArray();

        $retorno = [];
        foreach ($team as $member) {
            $retorno[] = [
                'id' => $member['id'],
                'name' => $member['name'],
                'phone' => $member['phone']
            ];
        }
        $retorno[] = [
            'id' => $head['id'],
            'name' => $head['name'],
            'phone' => $head['phone'],
        ];

        return $retorno;
    }
}
