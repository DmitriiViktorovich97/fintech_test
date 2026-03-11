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
            $table->foreignId('user_id')->constrained();
            $table->foreignId('cryptodictionaries_id')->constrained();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 40, 18);
            $table->decimal('balance_before', 40, 18);
            $table->decimal('balance_after', 40, 18);
            $table->string('txid')->nullable();
            $table->string('status')->default('pending');
            $table->text('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
