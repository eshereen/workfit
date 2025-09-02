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
        // Fix media records with null disk names
        DB::table('media')
            ->whereNull('disk')
            ->update(['disk' => config('media-library.disk_name', 'public')]);

        // Also ensure all media records have a disk name
        DB::table('media')
            ->where('disk', '')
            ->update(['disk' => config('media-library.disk_name', 'public')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration as it's a data fix
    }
};
