<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMenuAccessToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->json('allowed_menus')->nullable()->after('user_role_id');
        });
    }

    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('allowed_menus');
        });
    }
}
