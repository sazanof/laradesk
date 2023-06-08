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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('number')->unique();
            $table->integer('user_id');
            $table->integer('category_id');
            $table->string('subject');
            $table->text('content');
            $table->integer('status')->default(\App\Helpdesk\TicketStatus::NEW);
            $table->integer('priority')->default(\App\Helpdesk\TicketPriority::NORMAL);
            $table->boolean('need_approval')->default(false);
            $table->timestamp('solved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('category_id');
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
