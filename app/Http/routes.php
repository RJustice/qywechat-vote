<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware'=>'web'],function(){
    Route::get('vote/{id}',['as'=>'vote','uses'=>'QyVoteController@vote']);
    Route::get('voteapp',['as'=>'voteapp','uses'=>'QyVoteController@voteApp']);
    Route::post('vote','QyVoteController@postVote');
    Route::get('vlist','QyVoteController@voteList')->name('vlist');
    // Route::get('statistics/{id?}','QyVoteController@WechatStatistics');
    Route::get('statistics/{id?}/{order?}','QyVoteController@wechatStatistics');
    Route::get('success/{id}','QyVoteController@voteSuccess');
});

Route::group(['middleware'=>'web'],function(){
    Route::auth();
    Route::get('/','HomeController@index');
});

Route::group(['middleware' => 'web','prefix'=>'manage','namespace'=>'Manage','as'=>'manage::'], function () {
    Route::auth();
    Route::group(['prefix'=>'department'],function(){
        Route::get('/',['as'=>'index','uses'=>'DepartmentController@index']);
    });
    Route::group(['prefix'=>'sync'],function(){
        Route::get('/',['as'=>'index','uses'=>'SyncContactController@sync']);
        Route::get('/rs','SyncContactController@syncRs');
    });

    Route::resource('role','RoleController');
    Route::resource('users','ManageController');

    Route::group(['prefix'=>'contact'],function(){
        Route::get('glist','ContactController@getGroupList');
        Route::get('uinfo/{id}','ContactController@getMemberInfo');
        Route::get('ulist/{id}','ContactController@getGroupMembers');
    });

    Route::group(['prefix'=>'vote'],function(){
        Route::get('/','VoteController@index');
        Route::get('create','VoteController@create');
        Route::post('store','VoteController@store');
        Route::get('statistics/{id?}/{order?}','VoteController@statistics');
        Route::get('youxiu/{id?}/{order?}','VoteController@youxiu');
        Route::get('lianghao/{id?}/{order?}','VoteController@lianghao');
        Route::get('hege/{id?}/{order?}','VoteController@hege');
        Route::get('buhege/{id?}/{order?}','VoteController@buhege');
        Route::get('records/{id?}/{extra?}','VoteController@records');
        Route::get('list','VoteController@vlist');
        Route::get('show/{id}','VoteController@show');
        Route::get('more/{id}/{vuid}/{uid}','VoteController@more')->name('more');
        Route::get('edit/{id}','VoteController@edit')->name('edit');
        Route::get('del/{id}','VoteController@del');
        Route::get('copy/{id}','VoteController@copyx');
        Route::put('update/{id}','VoteController@update');

        Route::get('dps/{id}','VoteController@departStatistics');
    });
});
