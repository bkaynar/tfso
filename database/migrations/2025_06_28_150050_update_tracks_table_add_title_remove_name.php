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
        Schema::table('tracks', function (Blueprint $table) {
            // Önce title sütununu ekleyelim
            $table->string('title')->after('user_id');

            // Mevcut name verilerini title'a kopyalayalım
            // Bu raw SQL ile yapılacak
        });

        // Mevcut name verilerini title'a kopyala
        DB::statement('UPDATE tracks SET title = name');

        Schema::table('tracks', function (Blueprint $table) {
            // Sonra name sütununu kaldıralım
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            // Geri alma işlemi: name sütununu geri ekle
            $table->string('name')->after('user_id');
        });

        // title verilerini name'e kopyala
        DB::statement('UPDATE tracks SET name = title');

        Schema::table('tracks', function (Blueprint $table) {
            // title sütununu kaldır
            $table->dropColumn('title');
        });
    }
};
