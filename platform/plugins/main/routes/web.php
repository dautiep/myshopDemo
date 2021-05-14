<?php

Route::group(['namespace' => 'Platform\Main\Http\Controllers', 'middleware' => 'web'], function () {

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'mains', 'as' => 'main.'], function () {
            Route::resource('', 'MainController')->parameters(['' => 'main']);
            Route::delete('items/destroy', [
                'as'         => 'deletes',
                'uses'       => 'MainController@deletes',
                'permission' => 'main.destroy',
            ]);
        });
    });

});
