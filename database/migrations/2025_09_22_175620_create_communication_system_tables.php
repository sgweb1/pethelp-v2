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
        // Check if this is a fresh installation
        $isFreshInstallation = !Schema::hasTable('conversations');

        if ($isFreshInstallation) {
            // Fresh installation - create everything from scratch
            $this->createConversationsTable();
            $this->createMessagesTable();
            $this->createNotificationsTable();
        } else {
            // Existing installation - ensure all tables exist
            $this->ensureConversationsTable();
            $this->ensureMessagesTable();
            $this->ensureNotificationsTable();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }

    private function createConversationsTable(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index('last_message_at');
        });
    }

    private function createMessagesTable(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['sender_id', 'created_at']);
        });
    }

    private function createNotificationsTable(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    // Ensure methods for existing installations
    private function ensureConversationsTable(): void
    {
        if (!Schema::hasTable('conversations')) {
            $this->createConversationsTable();
        }
    }

    private function ensureMessagesTable(): void
    {
        if (!Schema::hasTable('messages')) {
            $this->createMessagesTable();
        }
    }

    private function ensureNotificationsTable(): void
    {
        if (!Schema::hasTable('notifications')) {
            $this->createNotificationsTable();
        }
    }
};