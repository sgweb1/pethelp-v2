<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add temporary column and business priority
        Schema::table('map_items', function (Blueprint $table) {
            if (!Schema::hasColumn('map_items', 'business_priority')) {
                $table->tinyInteger('business_priority')->default(5)->index()->after('content_type');
            }
            $table->string('new_content_type', 20)->nullable()->after('content_type');
        });

        // Step 2: Map old content types to new ones
        DB::table('map_items')->where('content_type', 'event')->update(['new_content_type' => 'event_public']);
        DB::table('map_items')->where('content_type', 'adoption')->update(['new_content_type' => 'adoption']);
        DB::table('map_items')->where('content_type', 'supplies')->update(['new_content_type' => 'service']);

        // Handle service -> split between pet_sitter and service
        DB::table('map_items')
            ->where('content_type', 'service')
            ->where('category_name', 'Pet sitter')
            ->update(['new_content_type' => 'pet_sitter']);

        DB::table('map_items')
            ->where('content_type', 'service')
            ->where('category_name', '!=', 'Pet sitter')
            ->update(['new_content_type' => 'service']);

        // Update supplies to be under service category
        DB::table('map_items')
            ->where('content_type', 'supplies')
            ->update(['category_name' => 'Sklep zoologiczny']);

        // Step 3: Drop old column and rename new one
        Schema::table('map_items', function (Blueprint $table) {
            $table->dropColumn('content_type');
        });

        Schema::table('map_items', function (Blueprint $table) {
            $table->renameColumn('new_content_type', 'content_type');
        });

        // Step 4: Create new enum constraint
        DB::statement("ALTER TABLE map_items MODIFY COLUMN content_type ENUM(
            'pet_sitter',      -- 🐕‍🦺 CORE BUSINESS - individual pet sitters (Priority 1)
            'service',         -- 🏥 Professional services (vets, shops, groomers) (Priority 2)
            'event_public',    -- 🗓️ Public community events (Priority 3)
            'event_private',   -- 👥 Private events between users (Priority 3)
            'adoption',        -- 🏠 Pet adoption (social mission) (Priority 4)
            'sale',           -- 💰 Marketplace (commercial) (Priority 5)
            'lost_pet',       -- 😢 Lost pets alert system (Priority 0 - URGENT)
            'found_pet',      -- 😊 Found pets alert system (Priority 0 - URGENT)
            'supplies'        -- 🛍️ Pet supplies and accessories (Priority 6)
        ) NOT NULL");

        // Step 5: Set business priorities
        DB::table('map_items')->update([
            'business_priority' => DB::raw("CASE content_type
                WHEN 'lost_pet' THEN 0
                WHEN 'found_pet' THEN 0
                WHEN 'pet_sitter' THEN 1
                WHEN 'service' THEN 2
                WHEN 'event_public' THEN 3
                WHEN 'event_private' THEN 3
                WHEN 'adoption' THEN 4
                WHEN 'sale' THEN 5
                WHEN 'supplies' THEN 6
                ELSE 9
            END")
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Pet Sitter records back to service
        DB::table('map_items')
            ->where('content_type', 'pet_sitter')
            ->update(['content_type' => 'service', 'category_name' => 'Pet sitter']);

        // Revert to original enum values
        DB::statement("ALTER TABLE map_items MODIFY COLUMN content_type ENUM(
            'event',
            'adoption',
            'sale',
            'lost_pet',
            'found_pet',
            'supplies',
            'service'
        ) NOT NULL");
    }
};
