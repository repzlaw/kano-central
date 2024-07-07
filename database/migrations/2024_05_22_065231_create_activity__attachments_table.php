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

        Schema::create('activity__attachments', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('type');
            $table->integer('size');
            $table->string('url');
            $table->dateTime('review_date');
            $table->foreignId('reviewer_id')->constrained('users');
            $table->foreignId('activity_id')->constrained();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity__attachments');
    }
};
