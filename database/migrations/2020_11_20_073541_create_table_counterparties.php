<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreateTableCounterparties extends Migration
  {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('counterparties', function (Blueprint $table) {
        $table->id();
        $table->foreignId('client_id')->constrained();
        $table->foreignId('counterparty_type_id')->constrained();
        $table->boolean('is_active')->default(false);
        $table->boolean('is_resident')->default(true);
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
      Schema::dropIfExists('counterparties');
    }
  }
