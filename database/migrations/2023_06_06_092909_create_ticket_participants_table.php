<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_participants', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ticket_id')->index();
            $table->integer('user_id')->index();
            $table->tinyInteger('role');
            $table->boolean('approved')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['ticket_id', 'user_id', 'role'], 'tur_ids_index');
            $table->unique(['ticket_id', 'user_id', 'role'], 'tur_ids_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_participants');
    }
};
