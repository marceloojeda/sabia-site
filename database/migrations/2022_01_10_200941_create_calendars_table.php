<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', Config::get('constants.EVENTO_TIPO'));
            $table->enum('audience', Config::get('constants.EVENTO_ALVO'));
            $table->dateTime('begin_at');
            $table->dateTime('finish_at')->nullable();
            $table->enum('status', Config::get('constants.EVENTO_STATUS'));
            $table->boolean('is_active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('calendars');
    }
}
