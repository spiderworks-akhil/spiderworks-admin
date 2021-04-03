<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file_name', 255);
            $table->string('file_path', 255);
            $table->string('thumb_file_path', 255);
            $table->string('file_type', 255);
            $table->integer('file_size');
            $table->string('dimensions', 50)->nullable();
            $table->string('media_type', 50)->default('Image');
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->boolean('is_public')->default(1);
            $table->nullableMorphs('related');
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
        Schema::dropIfExists('medias');
    }
}
