<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('projectname')->unique();
            $table->unsignedBigInteger('typeofproject_id');
            $table->unsignedBigInteger('framework_id');
            $table->string('database');
            $table->string('description');
            $table->date('datecreation');
            $table->date('deadline');
            $table->enum('etat', ['pending', 'in progress', 'completed'])->default('pending');
            $table->string('client');
            $table->string('client_email');
            $table->timestamps();

            $table->foreign('typeofproject_id')->references('id')->on('typeofprojects')->onDelete('cascade');
            $table->foreign('framework_id')->references('id')->on('frameworks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
     //   Schema::dropIfExists('projects');
        Schema::dropIfExists('projects');
    }
};
