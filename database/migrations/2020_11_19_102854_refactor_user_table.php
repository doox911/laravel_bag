<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class RefactorUserTable extends Migration
  {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('users', function (Blueprint $table) {
        $table->string('nickname')->after('id');
        $table->string('name')->nullable()->change();
        $table->string('second_name')->nullable()->after('name');
        $table->string('patronymic')->nullable()->after('second_name');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('users', function (Blueprint $table) {
        $table->string('name')->change();
        $table->dropColumn(['nickname', 'second_name', 'patronymic']);
      });
    }
  }
