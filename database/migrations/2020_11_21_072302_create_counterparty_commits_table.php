<?php

  use Illuminate\Database\Migrations\Migration;
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Support\Facades\Schema;

  class CreateCounterpartyCommitsTable extends Migration
  {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('counterparty_commits', function (Blueprint $table) {
        $table->id();
        $table->foreignId('counterparty_id')->constrained();
        $table->foreignId('legal_address_id')->constrained('addresses');
        $table->foreignId('actual_address_id')->constrained('addresses');
        $table->foreignId('label_address_id')->constrained('addresses');
        $table->string('name');
        $table->string('second_name')->nullable();
        $table->string('patronymic')->nullable();
        $table->string('full_name')->nullable();
        $table->string('passport_series')->nullable();
        $table->string('passport_number')->nullable();
        $table->string('passport_organization')->nullable();
        $table->date('passport_date')->nullable();
        $table->string('passport_code')->nullable();
        $table->string('inn');
        $table->string('kpp')->nullable();
        $table->string('code_1s')->nullable();
        $table->string('okpo')->nullable();
        $table->string('ogrn')->nullable();
        $table->string('egroul')->nullable();
        $table->string('bank')->nullable();
        $table->string('payment_account')->nullable();
        $table->string('correspondent_account')->nullable();
        $table->string('general_manager')->nullable();
        $table->string('сhief_аccountant')->nullable();
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->string('site')->nullable();
        $table->string('act_number')->nullable();
        $table->string('label_text')->nullable();
        $table->jsonb('document_properties')->default('{}');
        $table->timestamps();
        $table->softDeletes();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('counterparty_commits');
    }
  }
