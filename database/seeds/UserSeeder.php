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
        $adms = $this->createAdministradores();
        $heads = $this->createCoordenadores();

        $users = factory(User::class, 50)->make(['type' => 'Vendedor']);
        $nAdm = 0;
        $nCoo = 0;
        foreach ($users as $user) {
            $headRand = array_rand($heads);
            $user->head_id = $heads[$headRand]['id'];
            User::create($user->makeVisible(['password', 'remember_token'])->toArray());
        }
    }

    private function createAdministradores()
    {
        $users = factory(User::class, 4)
            ->create([
                'type' => 'Administrador'
            ])
            ->makeVisible(['password', 'remember_token']);

        return $users->toArray();
    }

    private function createCoordenadores()
    {
        $users = factory(User::class, 10)
            ->create([
                'type' => 'Coordenador'
            ])
            ->makeVisible(['password', 'remember_token']);

        return $users->toArray();
    }
}
