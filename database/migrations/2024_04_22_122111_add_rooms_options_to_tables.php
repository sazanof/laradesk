<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('room_id')->nullable()->change();
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('custom_location')->nullable();
        });
        User::whereNotNull('room_id')->update(['room_id' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('custom_location');
        });
    }
};
