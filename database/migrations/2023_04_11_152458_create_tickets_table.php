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
   /* public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('object');
            $table->text('description');
            $table->date('closing_date');
            $table->timestamps();
        });
    }
*/
    /**
     * Reverse the migrations.
     *
     * @return void
     */
   /* public function down()
    {
        Schema::dropIfExists('tickets');
        
    }*/
       public function up()
        {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('status')->default('Pending');
                $table->string('priority')->default('Low');
            });
        }

        public function down()
        {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('status');
                $table->dropColumn('priority');
            });
        }
};
