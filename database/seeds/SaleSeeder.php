<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = self::getUsers();
        $sales = factory(App\Models\Sale::class, 100)->make();
        foreach ($sales as $sale) {
            if($sale->payment_status === 'Pago') {
                $rand = array_rand($users);
                $sale->user_id = $users[$rand]->id;
                $sale->seller = $users[$rand]->name;
            }
            App\Models\Sale::create($sale->toArray());
        }
    }

    private static function getUsers()
    {
        $users = DB::table('users')->get(['id', 'name']);
        return $users->toArray();
    }
}
