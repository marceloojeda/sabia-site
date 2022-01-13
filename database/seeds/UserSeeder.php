<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class, 10)->make();
        $nAdm = 0;
        $nCoo = 0;
        foreach ($users as $user) {
            if($user->type === 'Administrador' && $nAdm >= 2) {
                $user->type = 'Vendedor';
            }
            if($user->type === 'Coordenador' && $nCoo >= 3) {
                $user->type = 'Vendedor';
            }

            $nAdm = $user->type === 'Administrador' ? $nAdm + 1 : 0;
            $nCoo = $user->type === 'Coordenador' ? $nCoo + 1 : 0;


            User::create($user->makeVisible(['password', 'remember_token'])->toArray());
        }
    }
}
