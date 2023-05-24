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
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name');
            $table->string('scientific_name');
            $table->string('capacity');
            $table->string('titer');
            $table->string('contraindications');
            $table->string('side_effects');
            $table->boolean('is_prescription')->default(false);
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('dosage_form_id')->constrained('dosage_forms');
            $table->foreignId('manufacture_company_id')->constrained('manufacture_companies');
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
        Schema::dropIfExists('drugs');
    }
};
