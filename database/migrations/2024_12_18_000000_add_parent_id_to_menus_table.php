<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdToMenusTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('menus', 'parent_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('menus')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('menus', 'parent_id')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            });
        }
    }
}
