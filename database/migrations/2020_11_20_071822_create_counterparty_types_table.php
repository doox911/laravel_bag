<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreateCounterpartyTypesTable extends Migration
  {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('counterparty_types', function (Blueprint $table) {
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
      Schema::dropIfExists('counterparty_types');
    }
  }
