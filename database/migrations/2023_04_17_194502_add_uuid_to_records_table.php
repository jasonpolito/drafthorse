<?php

use App\Models\Record;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->uuid('uuid');

            $model = new Record;
            $query = $model->newQuery();

            if (method_exists($query, 'withTrashed')) {
                $query->withTrashed();
            }

            $query->each(function (Record $model) {
                $model->uuid = Str::uuid();
                $model->save();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
