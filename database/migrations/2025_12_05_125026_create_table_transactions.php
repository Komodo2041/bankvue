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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('import_id');
            $table->string('transaction_id', 100);
            $table->string('account_number', 100);
            $table->date('transaction_date');
            $table->integer('amount');
            $table->enum("currency", ['PLN', 'USD']);            
            $table->timestamps();
            $table->foreign("import_id")->references("id")->on("imports");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
