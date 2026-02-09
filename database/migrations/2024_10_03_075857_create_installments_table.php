<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentsTable extends Migration {
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->decimal('principal');
            $table->decimal('interest');
            $table->timestamp('due_date');
            $table->timestamps();

            $table->foreign('loan_id')->references('id')->on('loans');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
}
