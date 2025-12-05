<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('file_name', 100); 
            $table->integer('total_records')->nullable(true);
            $table->integer('successful_records')->nullable(true);;
            $table->integer('failed_records')->nullable(true);; 
            $table->enum("status", ['success', 'partial', 'failed'])->nullable(true);;
            $table->timestamps(); 
        });
    }
 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
