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
        Schema::create('collection_card', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->boolean('is_foil')->default(false);
            $table->text('notes')->nullable();
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();

            $table->unique(['collection_id', 'card_id', 'is_foil']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_card');
    }
};
