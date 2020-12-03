<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreatePlaceTypesTable extends Migration
  {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('place_types', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('alias');
        $table->softDeletes();
        $table->timestamps();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('place_types');
    }
  }
