<?php

$prefix = (config()->has('webadmin.url_prefix'))?config()->get('webadmin.url_prefix'):'admin';
$middleware = (config()->has('webadmin.webadmin_middleware'))?config()->get('webadmin.webadmin_middleware'):'auth';

Route::get('webadmin/validation/unique-slug', ['as' => 'spiderworks.webadmin.unique-slug', 'uses' => 'Spiderworks\Webadmin\Controllers\WebadminController@unique_slug' ]);
Route::get('webadmin/select2/faq', ['as' => 'spiderworks.webadmin.select2.faq', 'uses' => 'Spiderworks\Webadmin\Controllers\WebadminController@select2_faq' ]);
Route::get('webadmin/select2/categories/{type}', ['as' => 'spiderworks.webadmin.select2.categories', 'uses' => 'Spiderworks\Webadmin\Controllers\WebadminController@select2_categories' ]);

Route::group(['prefix' => $prefix, 'namespace' => 'Spiderworks\Webadmin\Controllers', 'middleware' => ['web']], function () use($middleware) {

	Route::get('/', ['as' => 'spiderworks.webadmin.login', 'uses' => 'WebadminController@login' ]);

	Route::group(['middleware' => $middleware], function(){
		Route::get('/dashboard', ['as' => 'spiderworks.webadmin.dashboard.index', 'uses' => 'WebadminController@index' ]);
    	
    	Route::get('change-password', array('as' => 'spiderworks.webadmin.change-password', function(){
	        return View::make('spiderworks.webadmin.change_password');
	    }));

	    Route::post('/changePassword','WebadminController@changePassword')->name('spiderworks.webadmin.update-password');

		//blogs
	    Route::get('blogs', 'BlogController@index')->name('spiderworks.webadmin.blogs.index');
        Route::get('blogs/create', 'BlogController@create')->name('spiderworks.webadmin.blogs.create');
        Route::get('blogs/edit/{id}', 'BlogController@edit')->name('spiderworks.webadmin.blogs.edit');
        Route::get('blogs/destroy/{id}', 'BlogController@destroy')->name('spiderworks.webadmin.blogs.destroy');
        Route::get('blogs/change-status/{id}', 'BlogController@changeStatus')->name('spiderworks.webadmin.blogs.change-status');
        Route::post('blogs/store', 'BlogController@store')->name('spiderworks.webadmin.blogs.store');
        Route::post('blogs/update', 'BlogController@update')->name('spiderworks.webadmin.blogs.update');

        //faq
        Route::get('/faq', 'FaqController@index')->name('spiderworks.webadmin.faq.index');
        Route::get('/faq/edit/{id}', 'FaqController@edit')->name('spiderworks.webadmin.faq.edit');
        Route::get('/faq/destroy/{id}', 'FaqController@destroy')->name('spiderworks.webadmin.faq.destroy');
        Route::get('/faq/create', 'FaqController@create')->name('spiderworks.webadmin.faq.create');
        Route::post('/faq/update/{id}', 'FaqController@update')->name('spiderworks.webadmin.faq.update');
        Route::post('/faq/store', 'FaqController@store')->name('spiderworks.webadmin.faq.store');
        Route::get('/faq/change-status/{id}', 'FaqController@changeStatus')->name('spiderworks.webadmin.faq.change-status');

		//media
		Route::get('/media', 'MediaController@index')->name('spiderworks.webadmin.media.index');
		Route::post('/media', 'MediaController@index')->name('spiderworks.webadmin.media.index.post');
	    Route::get('/media/popup/{popup_type?}/{type?}/{holder_attr?}/{related_id?}', 'MediaController@popup')->name('spiderworks.webadmin.media.popup');
	    Route::post('/media/save', 'MediaController@save')->name('spiderworks.webadmin.media.save');
	    Route::get('/media/edit/{id}', 'MediaController@edit')->name('spiderworks.webadmin.media.edit');
	    Route::post('/media/store-extra/{id}', 'MediaController@storeExtra')->name('spiderworks.webadmin.media.store-extra');

	    //category
	    Route::get('/categories/edit/{id}', 'CategoryController@edit')->name('spiderworks.webadmin.categories.edit');
        Route::get('/categories/destroy/{id}', 'CategoryController@destroy')->name('spiderworks.webadmin.categories.destroy');
        Route::get('/categories/create/{parent?}', 'CategoryController@create')->name('spiderworks.webadmin.categories.create');
        Route::post('/categories/update', 'CategoryController@update')->name('spiderworks.webadmin.categories.update');
        Route::post('/categories/store', 'CategoryController@store')->name('spiderworks.webadmin.categories.store');
        Route::get('/categories/change-status/{id}', 'CategoryController@changeStatus')->name('spiderworks.webadmin.categories.change-status');
        Route::get('/categories/{parent?}', 'CategoryController@index')->name('spiderworks.webadmin.categories.index');

        //category
        Route::get('/pages/edit/{id}', 'PageController@edit')->name('spiderworks.webadmin.pages.edit');
        Route::get('/pages/destroy/{id}', 'PageController@destroy')->name('spiderworks.webadmin.pages.destroy');
        Route::get('/pages/create/{parent?}', 'PageController@create')->name('spiderworks.webadmin.pages.create');
        Route::post('/pages/update', 'PageController@update')->name('spiderworks.webadmin.pages.update');
        Route::post('/pages/store', 'PageController@store')->name('spiderworks.webadmin.pages.store');
        Route::get('/pages/change-status/{id}', 'PageController@changeStatus')->name('spiderworks.webadmin.pages.change-status');
        Route::get('/pages/{parent?}', 'PageController@index')->name('spiderworks.webadmin.pages.index');

        //menus
        Route::get('/menus/edit/{id}', 'MenuController@edit')->name('spiderworks.webadmin.menus.edit');
		Route::get('/menus/destroy/{id}', 'MenuController@destroy')->name('spiderworks.webadmin.menus.destroy');
		Route::get('/menus/create', 'MenuController@create')->name('spiderworks.webadmin.menus.create');
		Route::post('/menus/update', 'MenuController@update')->name('spiderworks.webadmin.menus.update');
		Route::post('/menus/store', 'MenuController@store')->name('spiderworks.webadmin.menus.store');
		Route::get('/menus/change-status/{id}', 'MenuController@changeStatus')->name('spiderworks.webadmin.menus.change-status');
		Route::get('/menus', 'MenuController@index')->name('spiderworks.webadmin.menus.index');

		//frontend page
	    Route::get('frontend-pages', 'FrontendPageController@index')->name('spiderworks.webadmin.frontend-pages.index');
	    Route::get('frontend-pages/destroy/{id}', function(){
	    	echo "Not possible";exit;
	    })->name('spiderworks.webadmin.frontend-pages.destroy');
        Route::get('frontend-pages/edit/{id}', 'FrontendPageController@edit')->name('spiderworks.webadmin.frontend-pages.edit');
        Route::post('frontend-pages/update', 'FrontendPageController@update')->name('spiderworks.webadmin.frontend-pages.update');
        Route::get('frontend-pages/change-status/{id}', function(){
            echo "Not possible";exit;
        })->name('spiderworks.webadmin.frontend-pages.change-status');

        //testimonials
        Route::get('testimonials', 'TestimonialController@index')->name('spiderworks.webadmin.testimonials.index');
        Route::get('testimonials/create', 'TestimonialController@create')->name('spiderworks.webadmin.testimonials.create');
        Route::get('testimonials/edit/{id}', 'TestimonialController@edit')->name('spiderworks.webadmin.testimonials.edit');
        Route::get('testimonials/destroy/{id}', 'TestimonialController@destroy')->name('spiderworks.webadmin.testimonials.destroy');    
        Route::post('testimonials/store', 'TestimonialController@store')->name('spiderworks.webadmin.testimonials.store');
        Route::post('testimonials/update', 'TestimonialController@update')->name('spiderworks.webadmin.testimonials.update');
        Route::get('testimonials/change-status/{id}', 'TestimonialController@changeStatus')->name('spiderworks.webadmin.testimonials.change-status');

        //events
        Route::get('events', 'EventController@index')->name('spiderworks.webadmin.events.index');
        Route::get('events/create', 'EventController@create')->name('spiderworks.webadmin.events.create');
        Route::get('events/edit/{id}', 'EventController@edit')->name('spiderworks.webadmin.events.edit');
        Route::get('events/destroy/{id}', 'EventController@destroy')->name('spiderworks.webadmin.events.destroy');
        Route::get('events/change-status/{id}', 'EventController@changeStatus')->name('spiderworks.webadmin.events.change-status');
        Route::post('events/store', 'EventController@store')->name('spiderworks.webadmin.events.store');
        Route::post('events/update', 'EventController@update')->name('spiderworks.webadmin.events.update');

        //sliders
        Route::get('sliders/edit/{id}/{type?}', 'SliderController@edit')->name('spiderworks.webadmin.sliders.edit');
        Route::get('sliders/destroy/{id}', 'SliderController@destroy')->name('spiderworks.webadmin.sliders.destroy');
        Route::get('sliders/create', 'SliderController@create')->name('admin.spiderworks.webadmin.create');
        Route::post('sliders/update/{id}', 'SliderController@update')->name('spiderworks.webadmin.sliders.update');
        Route::post('sliders/update-photo/{id}', 'SliderController@updatePhoto')->name('spiderworks.webadmin.sliders.update-photo');
        Route::post('sliders/store', 'SliderController@store')->name('spiderworks.webadmin.sliders.store');
        Route::get('sliders/photo-edit/{id}/{slider_id}/{type}', 'SliderController@photo_edit')->name('spiderworks.webadmin.sliders.photo_edit');
        Route::get('sliders/photo-delete/{slider_id}/{id}/{type}', 'SliderController@photo_delete')->name('spiderworks.webadmin.sliders.photo-delete');
        Route::get('sliders', 'SliderController@index')->name('spiderworks.webadmin.sliders.index');
        Route::get('sliders/validation/unique-name', 'SliderController@validate_name')->name('spiderworks.webadmin.sliders.unique-name');
        Route::get('sliders/change-status/{id}', function(){
            echo "Not possible";exit;
        })->name('spiderworks.webadmin.sliders.change-status');

        //team
        Route::get('team', 'TeamController@index')->name('spiderworks.webadmin.team.index');
        Route::get('team/create', 'TeamController@create')->name('spiderworks.webadmin.team.create');
        Route::get('team/edit/{id}/{tab?}', 'TeamController@edit')->name('spiderworks.webadmin.team.edit');
        Route::get('team/destroy/{id}', 'TeamController@destroy')->name('spiderworks.webadmin.team.destroy');
        Route::get('team/change-status/{id}', 'TeamController@changeStatus')->name('spiderworks.webadmin.team.change-status');
        Route::post('team/store', 'TeamController@store')->name('spiderworks.webadmin.team.store');
        Route::post('team/update', 'TeamController@update')->name('spiderworks.webadmin.team.update');

        //settings
        Route::get('/settings/edit/{id}', 'SettingController@edit')->name('spiderworks.webadmin.settings.edit');
        Route::get('/settings/create', 'SettingController@create')->name('spiderworks.webadmin.settings.create');
        Route::post('/settings/update', 'SettingController@update')->name('spiderworks.webadmin.settings.update');
        Route::post('/settings/store', 'SettingController@store')->name('spiderworks.webadmin.settings.store');
        Route::get('/settings', 'SettingController@index')->name('spiderworks.webadmin.settings.index');
        Route::get('/settings/destroy/{id}', 'SettingController@destroy')->name('spiderworks.webadmin.settings.destroy');
        Route::get('settings/change-status/{id}', function(){
            echo "Not possible";exit;
        })->name('spiderworks.webadmin.settings.change-status');
	});
});