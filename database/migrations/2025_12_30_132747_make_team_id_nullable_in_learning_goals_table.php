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
        Schema::table('learning_goals', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['team_id']);
            // Make team_id nullable
            $table->unsignedBigInteger('team_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learning_goals', function (Blueprint $table) {
            // Make team_id required again
            $table->unsignedBigInteger('team_id')->nullable(false)->change();
            // Re-add the foreign key constraint
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }
};
