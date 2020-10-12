<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollegerProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colleger_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('nrp')->unique();
            $table->string('birthday');
            $table->string('address');
            $table->unsignedInteger('role_id');
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colleger_profiles');
    }
}
