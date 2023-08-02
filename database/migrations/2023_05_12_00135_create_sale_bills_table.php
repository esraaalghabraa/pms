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
        Schema::create('sale_bills', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->double('total_Sale_price')->default(0);
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('pharmacy_id')->constrained('pharmacies');
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
        Schema::dropIfExists('sale_bills');
    }
};
