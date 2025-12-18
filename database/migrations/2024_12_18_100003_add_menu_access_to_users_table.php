<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuAccessToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('user', 'allowed_menus')) {
            Schema::table('user', function (Blueprint $table) {
                $table->json('allowed_menus')->nullable()->after('user_role_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('user', 'allowed_menus')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropColumn('allowed_menus');
            });
        }
    }
}
