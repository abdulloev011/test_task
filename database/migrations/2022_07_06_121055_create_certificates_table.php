<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->foreign()->references("id")->on("users")->onDelete("cascade");
            $table->string('plantation_year');
            $table->integer('number_of_tree');
            $table->string('currency');
            $table->double('price');
            $table->boolean('is_active')->default(0);
            $table->string('unique_code');
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
        Schema::dropIfExists('certificates');
    }
}
