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
        Schema::create('dj_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_manager_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dj_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('place_id')->constrained('places')->onDelete('cascade');
            $table->date('event_date');
            $table->time('event_time');
            $table->decimal('duration', 4, 2); // Hours (e.g., 3.5 for 3.5 hours)
            $table->decimal('budget', 10, 2); // Money amount
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['dj_id', 'status']);
            $table->index(['place_manager_id', 'status']);
            $table->index('event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dj_offers');
    }
};
