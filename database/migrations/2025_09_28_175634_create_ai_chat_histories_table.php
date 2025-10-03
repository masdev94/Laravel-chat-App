<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiChatHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ai_chat_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('room_id');
            $table->text('user_message');
            $table->text('ai_response');
            $table->string('ai_model')->default('gpt-3.5-turbo');
            $table->json('context')->nullable(); // Store conversation context
            $table->timestamps();

            $table->index(['user_id', 'room_id']);
            $table->index(['room_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_chat_histories');
    }
}
