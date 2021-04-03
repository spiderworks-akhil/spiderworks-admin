<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug', 255)->unique();
            $table->string('name', 255);
            $table->bigInteger('parent_id')->default(0);
            $table->string('permission', 255);
            $table->string('icon', 255);
            $table->bigInteger('display_order')->default(0);
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->useCurrent();
        });

        $admin_links[] = ['slug'=>'admin/dashboard', 'name'=>'Dashboard', 'parent_id'=>0, 'permission'=>'dashoard', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/blogs', 'name'=>'Blogs', 'parent_id'=>0, 'permission'=>'blogs', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/media', 'name'=>'Medias', 'parent_id'=>0, 'permission'=>'media', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/faq', 'name'=>'FAQs', 'parent_id'=>0, 'permission'=>'faq', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/categories', 'name'=>'Categories', 'parent_id'=>0, 'permission'=>'categories', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/pages', 'name'=>'Pages', 'parent_id'=>0, 'permission'=>'pages', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/testimonials', 'name'=>'Testimonials', 'parent_id'=>0, 'permission'=>'testimonials', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/events', 'name'=>'Events', 'parent_id'=>0, 'permission'=>'events', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/sliders', 'name'=>'Sliders', 'parent_id'=>0, 'permission'=>'sliders', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/menus', 'name'=>'Menus', 'parent_id'=>0, 'permission'=>'menus', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/team', 'name'=>'Team', 'parent_id'=>0, 'permission'=>'team', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/settings', 'name'=>'Settings', 'parent_id'=>0, 'permission'=>'settings', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        $admin_links[] = ['slug'=>'admin/frontend-pages', 'name'=>'Static Pages', 'parent_id'=>0, 'permission'=>'frontend-pages', 'icon'=>'fa fa-list', 'display_order'=>0, 'created_at'=>date('Y-m-d H:i:s')];
        foreach ($admin_links as $link) 
            \DB::table('admin_links')->insert($link);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_links');
    }
}
