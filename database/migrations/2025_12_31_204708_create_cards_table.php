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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->uuid('scryfall_id')->unique();
            $table->uuid('oracle_id')->index();
            $table->string('name')->index();
            $table->string('mana_cost')->nullable();
            $table->decimal('cmc', 8, 2)->default(0);
            $table->string('type_line');
            $table->text('oracle_text')->nullable();
            $table->string('power')->nullable();
            $table->string('toughness')->nullable();
            $table->json('colors')->nullable();
            $table->json('color_identity')->nullable();
            $table->json('keywords')->nullable();
            $table->string('set_code')->index();
            $table->string('set_name');
            $table->string('rarity');
            $table->string('image_uri_small')->nullable();
            $table->string('image_uri_normal')->nullable();
            $table->string('image_uri_large')->nullable();
            $table->json('prices')->nullable();
            $table->boolean('is_multi_faced')->default(false);
            $table->json('card_faces')->nullable();
            $table->json('legalities')->nullable();
            $table->date('released_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index(['name', 'set_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
