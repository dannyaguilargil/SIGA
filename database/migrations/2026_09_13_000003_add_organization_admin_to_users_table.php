<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->boolean('is_organization_admin')->default(false)->after('organization_id');
        });

        // Preserve a sensible owner for organizations created before this role existed.
        DB::table('users')
            ->whereNotNull('organization_id')
            ->orderBy('organization_id')
            ->orderBy('id')
            ->get(['id', 'organization_id'])
            ->unique('organization_id')
            ->each(fn (object $user) => DB::table('users')->where('id', $user->id)->update(['is_organization_admin' => true]));
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('is_organization_admin');
        });
    }
};
