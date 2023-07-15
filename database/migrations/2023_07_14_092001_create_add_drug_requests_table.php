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
        Schema::create('add_drug_requests', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name')->index('name');
            $table->string('scientific_name');
            $table->string('capacity');
            $table->string('titer');
            $table->string('contraindications');
            $table->string('side_effects');
            $table->boolean('is_prescription')->default(false);
            $table->enum('status',['pending','accepting','rejecting']);
            $table->string('category');
            $table->string('dosage_form');
            $table->string('manufacture_company');
            $table->string('repository_id')->constrained('repositories');
            $table->softDeletes();
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
        Schema::dropIfExists('add_drug_requests');
    }
};
