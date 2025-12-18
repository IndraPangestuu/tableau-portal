<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecentDashboardsTable extends Migration
{
    public function up()
    {
        Schema::create('recent_dashboards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('menu_id');
            $table->timestamp('accessed_at');

            $table->index(['user_id', 'accessed_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('recent_dashboards');
    }
}
