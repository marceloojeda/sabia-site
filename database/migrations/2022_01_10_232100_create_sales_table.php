<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('seller')->nullable();
            $table->boolean('is_ecommerce')->nullable();
            $table->enum('payment_method', Config::get('constants.PAYMENT_METHOD'))->nullable();
            $table->double('amount');
            $table->double('amount_paid')->nullable();
            $table->string('buyer');
            $table->string('buyer_email')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->smallInteger('ticket_number')->nullable();
            $table->enum('payment_status', Config::get('constants.PAYMENT_STATUS'))->nullable();
            $table->dateTime('payment_date')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
