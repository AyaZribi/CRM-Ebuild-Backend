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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('devis_id');
            $table->string('nature');
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('taux_tva', 5, 2);
            $table->decimal('montant_ttc', 10, 2);
            $table->timestamps();

            $table->foreign('devis_id')->references('id')->on('devis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
};
