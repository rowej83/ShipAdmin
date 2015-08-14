<?php

Route::get('/', ['uses'=>'ItemController@index']);

Route::post('/',['as'=>'input','uses'=>'ItemController@input']);

Route::get('load', ['uses'=>'ItemController@load']);


