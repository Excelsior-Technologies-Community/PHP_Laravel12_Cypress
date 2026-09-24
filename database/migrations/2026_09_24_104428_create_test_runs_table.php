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
        Schema::create('test_runs', function (Blueprint $table) {
            $table->id();
            $table->string('spec_name');
            $table->string('test_name')->nullable();
            $table->enum('status', [
                'passed',
                'failed',
                'skipped',
            ])->default('passed');
            $table->string('browser')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('spec_name');
            $table->index('executed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_runs');
    }
};