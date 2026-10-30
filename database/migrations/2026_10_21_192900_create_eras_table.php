<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eras', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_en', 60);
            $table->string('name_ar', 60);
            $table->timestamps();
        });

        // The eras are fixed reference data the app cannot work without,
        // so they are inserted together with the table.
        $now = now();

        DB::table('eras')->insert(array_map(fn (array $era) => $era + ['created_at' => $now, 'updated_at' => $now], [
            ['slug' => 'pre-islamic', 'name_en' => 'Pre-Islamic', 'name_ar' => 'العصر الجاهلي'],
            ['slug' => 'early-islamic', 'name_en' => 'Early Islamic', 'name_ar' => 'صدر الإسلام'],
            ['slug' => 'umayyad', 'name_en' => 'Umayyad', 'name_ar' => 'العصر الأموي'],
            ['slug' => 'abbasid', 'name_en' => 'Abbasid', 'name_ar' => 'العصر العباسي'],
            ['slug' => 'andalusian', 'name_en' => 'Andalusian', 'name_ar' => 'العصر الأندلسي'],
            ['slug' => 'mamluk-ottoman', 'name_en' => 'Mamluk & Ottoman', 'name_ar' => 'العصر المملوكي والعثماني'],
            ['slug' => 'modern', 'name_en' => 'Modern', 'name_ar' => 'العصر الحديث'],
        ]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eras');
    }
};
