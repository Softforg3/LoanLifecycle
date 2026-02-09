<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->decimal('amount');
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}
