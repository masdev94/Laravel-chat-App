<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ai_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->default('AI Chat');
            $table->text('description')->nullable();
            $table->json('ai_settings')->nullable(); // Store AI personality, model preferences
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['room_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_rooms');
    }
}
