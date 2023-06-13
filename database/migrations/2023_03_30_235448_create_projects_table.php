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
        Schema::table('projects', function (Blueprint $table) {
           /* $table->id();
            $table->string('client');
            $table->string('client_email');
            $table->string('projectname');
            $table->string('typeofproject');
            $table->string('frameworks');
            $table->string('database');
            $table->text('description');
            $table->date('datecreation');
            $table->date('deadline');*/
            $table->string('etat')->default('InProgress');
            //$table->timestamps();
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
       $table->dropColumn('etat');
    }
};
