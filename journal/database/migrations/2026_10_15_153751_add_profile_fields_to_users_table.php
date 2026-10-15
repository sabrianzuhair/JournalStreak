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
        Schema::table('users', function (Blueprint $table) {
            // `name` stays the pen name used in greetings; these extend the profile page
            $table->string('full_name')->nullable()->after('name');
            $table->string('handle', 30)->nullable()->unique()->after('full_name');
            $table->string('affiliation')->nullable()->after('email');
            $table->string('bio', 180)->nullable()->after('affiliation');
            $table->string('avatar_path')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['handle']);
            $table->dropColumn(['full_name', 'handle', 'affiliation', 'bio', 'avatar_path']);
        });
    }
};
