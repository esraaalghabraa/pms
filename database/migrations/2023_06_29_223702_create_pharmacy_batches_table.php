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
        Schema::create('pharmacy_batches', function (Blueprint $table) {
            $table->id();
            $table->string('barcode');
            $table->integer('batch_number');
            $table->date('expired_date');
            $table->date('date_of_entry');
            $table->integer('batch_quantity');
            $table->foreignId('pharmacy_storage_id')->constrained('pharmacy_storages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pharmacy_batches');
    }
};
