<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telegram_chats', function (Blueprint $table): void {
            $table->id();
            $table->bigInteger('chat_id')->unique();
            $table->string('chat_type');
            $table->string('title')->nullable();
            $table->string('board_id')->nullable();
            $table->string('user_email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('chat_id');
            $table->index('user_email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_chats');
    }
};
