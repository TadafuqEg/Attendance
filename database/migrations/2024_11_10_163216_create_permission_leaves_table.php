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
        Schema::create('leave_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code',191);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['pending','accepted','rejected'])->default('pending');
            $table->time('from');
            $table->time('to');
            $table->date('date');
            $table->longText('message')->nullable();
            $table->enum('hr_approval', ['pending','accepted','rejected'])->default('pending');
            $table->enum('Manager_approval', ['pending','accepted','rejected'])->default('pending');
            $table->longText('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_permissions');
    }
};
