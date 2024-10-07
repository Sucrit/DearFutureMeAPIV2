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
        Schema::create('capsules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->string('title');
            $table->string('message');
            $table->text('content')->nullable();
            $table->timestamp('opens_at')->nullable(true);
            
            // Add the receiver_email as a string first
            $table->string('receiver_email');
        
            // Now add the foreign key constraint for receiver_email
            $table->foreign('receiver_email')->references('email')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capsules');
    }
};
