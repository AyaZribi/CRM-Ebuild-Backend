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
        Schema::table('taches', function (Blueprint $table) {
          /*  $table->id();
            $table->string('intitule');
            $table->dateTime('deadline');
            $table->text('description');
            $table->string('file')->nullable();
            $table->string('image')->nullable();
            $table->string('projectname');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->timestamps();*/
            $table->string('status')->default('InProgress');
            $table->string('important')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      //  Schema::dropIfExists('taches');
    }
};
