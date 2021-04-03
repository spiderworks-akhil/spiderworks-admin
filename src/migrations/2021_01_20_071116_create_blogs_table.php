<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 255)->unique();
            $table->string('name', 255);
            $table->string('title', 255);
            $table->string('short_description', 1000)->nullable();
            $table->text('content');
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('featured_image_id')->nullable();
            $table->bigInteger('banner_image_id')->nullable();
            $table->string('browser_title', 255)->nullable();
            $table->string('og_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('og_description')->nullable();
            $table->bigInteger('og_image_id')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('top_description')->nullable();
            $table->text('bottom_description')->nullable();
            $table->text('extra_css')->nullable();
            $table->text('extra_js')->nullable();
            $table->bigInteger('faq_id')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('blogs');
    }
}
