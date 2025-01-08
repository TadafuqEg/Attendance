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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->json('data')->nullable();
            $table->string('type')->nullable();
            
            $table->enum('seen', ['0', '1'])->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    <a href="{{ url('terms_conditions?lang=en') }}">English</a>
<a href="{{ url('terms_conditions?lang=ar') }}">العربية</a>
<a href="{{ url('terms_conditions?lang=de') }}">Deutsch</a>
<a href="{{ url('terms_conditions?lang=fr') }}">Français</a>
<a href="{{ url('terms_conditions?lang=es') }}">Español</a>
<a href="{{ url('terms_conditions?lang=tr') }}">Türkçe</a>
<a href="{{ url('terms_conditions?lang=ru') }}">Русский</a>
<a href="{{ url('terms_conditions?lang=zh') }}">中文</a>

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
