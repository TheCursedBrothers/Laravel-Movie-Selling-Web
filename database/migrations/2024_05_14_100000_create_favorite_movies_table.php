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
        // This migration is a duplicate and should not create anything
        // The table has already been created by 2024_05_14_000005_create_favorite_movies_table.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing since we didn't create anything
    }
};
