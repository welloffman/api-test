<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auths', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->dateTime('expire_date');
            $table->string('uuid', 36);
            $table->string('external_uuid', 36);
            $table->string('lastname');
            $table->string('firstname');
            $table->string('thirdname');
            $table->date('birthday');
            $table->string('token')->unique();
            $table->enum('status', ['active', 'disabled']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auths');
    }
}
