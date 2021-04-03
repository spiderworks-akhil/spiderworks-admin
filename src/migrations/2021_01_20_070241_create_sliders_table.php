<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slider_name', 255)->unique();
            $table->string('width', 255);
            $table->string('height', 255);
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();
        });

        Schema::create('slider_photos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('sliders_id');
            $table->bigInteger('media_id');
            $table->text('crop_data')->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->string('button_text', 255)->nullable();
            $table->string('button_link', 255)->nullable();
            $table->string('button_link_target', 20)->nullable();
            $table->string('button2_text', 255)->nullable();
            $table->string('button2_link', 255)->nullable();
            $table->string('button2_link_target', 20)->nullable();
            $table->bigInteger('created_by');
            $table->bigInteger('updated_by');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slider_photos');
        Schema::dropIfExists('sliders');
    }
}
