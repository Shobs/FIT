<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->unique();
            $table->bigInteger('location_id')->unsigned();
            $table->string('account_number');
            $table->string('account_routing');
            $table->decimal('totalEarnings', 8, 2);
            $table->boolean('is_available')->comment('false not available, true available');
            $table->longText('car');
            $table->string('license_plate');
            $table->string('license_number');
            $table->date('license_expiration');
            $table->string('insurance_number');
            $table->timestamps();
        });

        /**
         * Adding foreign key constraints to drivers table
         */
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreign('location_id')->references('id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
