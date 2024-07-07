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
        Schema::disableForeignKeyConstraints();

        Schema::create('credential__attachments', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('type');
            $table->integer('size');
            $table->string('url');
            $table->dateTime('review_date');
            $table->foreignId('reviewer_id')->constrained('users');
            $table->foreignId('credential_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credential__attachments');
    }
};
