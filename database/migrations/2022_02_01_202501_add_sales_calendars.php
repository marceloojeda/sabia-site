<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesCalendars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->smallInteger('billets_old')->after('status')->nullable();
            $table->smallInteger('billets_actual')->after('billets_old')->nullable();
            $table->double('billets_goal')->after('billets_actual')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('billets_old');
            $table->dropColumn('billets_actual');
            $table->dropColumn('billets_goal');
        });
    }
}
