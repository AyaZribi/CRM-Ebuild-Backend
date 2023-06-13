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
        Schema::table('devis', function (Blueprint $table) {
        /*    $table->id();
            $table->string('client');
            $table->string('client_email');
            $table->date('date_creation');
            $table->integer('nombre_operations')->default(0)
            $table->timestamps()
            $table->integer('invoiced')->default(0);*/
            $table->string('note')->default('');
        });    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('devis');
    }
};
