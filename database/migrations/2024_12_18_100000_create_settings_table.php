<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => 'DAKGAR LANTAS', 'type' => 'string', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_subtitle', 'value' => 'Dashboard Portal', 'type' => 'string', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_logo', 'value' => null, 'type' => 'string', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'app_favicon', 'value' => null, 'type' => 'string', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_text', 'value' => 'KORLANTAS POLRI', 'type' => 'string', 'group' => 'general', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'dashboard_refresh_interval', 'value' => '0', 'type' => 'integer', 'group' => 'dashboard', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_fullscreen', 'value' => '1', 'type' => 'boolean', 'group' => 'dashboard', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
