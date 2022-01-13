<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = self::getUsers();
        $calendars = factory(App\Models\Calendar::class, 10)->make();
        foreach ($calendars as $calendar) {
            $rand = array_rand($users);
            $calendar->user_id = $users[$rand]->id;
            App\Models\Calendar::create($calendar->toArray());
        }
    }

    private static function getUsers()
    {
        $users = DB::table('users')->get('id');
        return $users->toArray();
    }
}
