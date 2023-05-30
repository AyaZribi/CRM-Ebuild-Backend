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
        Schema::create('operationfactures', function (Blueprint $table) {

            $table->id();
            $table->string('nature');
            $table->string('quantité');
            $table->decimal('montant_ht', 10, 2);
            $table->decimal('taux_tva', 5, 2)->default(19);
            $table->decimal('montant_ttc', 10, 2)->nullable();
            $table->unsignedBigInteger('facture_id'); // Add the facture_id column
            $table->timestamps();
            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('cascade'); // Add the foreign key constraint
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operationfactures');
    }
};
