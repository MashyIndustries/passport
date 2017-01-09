<?php

namespace Laravel\Passport;

use Illuminate\Support\Facades\Route;

class RouteRegistrar
{

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {  
        Route::group(['prefix' => 'auth', "middleware" => 'api'], function(){
            $this->forAuthorization();
            $this->forAccessTokens();
            $this->forTransientTokens();
            $this->forClients();
            $this->forPersonalAccessTokens();
        });
       
    }

    /**
     * Register the routes needed for authorization.
     *
     * @return void
     */
    public function forAuthorization()
    {
        Route::group(['middleware' => ['auth:api']], function ($router) {
            Route::get('authorize', [
                'uses' => 'AuthorizationController@authorize',
            ]);

            Route::post('authorize', [
                'uses' => 'ApproveAuthorizationController@approve',
            ]);

            Route::delete('authorize', [
                'uses' => 'DenyAuthorizationController@deny',
            ]);
        });
    }

    /**
     * Register the routes for retrieving and issuing access tokens.
     *
     * @return void
     */
    public function forAccessTokens()
    {
        Route::post('token', [
            'uses' => 'AccessTokenController@issueToken',
            'middleware' => 'throttle'
        ]);

        Route::group(['middleware' => ['auth:api']], function ($router) {
            Route::get('tokens', [
                'uses' => 'AuthorizedAccessTokenController@forUser',
            ]);

            Route::delete('tokens/{token_id}', [
                'uses' => 'AuthorizedAccessTokenController@destroy',
            ]);
        });
    }

    /**
     * Register the routes needed for refreshing transient tokens.
     *
     * @return void
     */
    public function forTransientTokens()
    {
        Route::post('token/refresh', [
            'middleware' => ['auth:api'],
            'uses' => 'TransientTokenController@refresh',
        ]);
    }

    /**
     * Register the routes needed for managing clients.
     *
     * @return void
     */
    public function forClients()
    {
        Route::group(['middleware' => ['auth:api']], function ($router) {
            Route::get('clients', [
                'uses' => 'ClientController@forUser',
            ]);

            Route::post('clients', [
                'uses' => 'ClientController@store',
            ]);

            Route::put('clients/{client_id}', [
                'uses' => 'ClientController@update',
            ]);

            Route::delete('clients/{client_id}', [
                'uses' => 'ClientController@destroy',
            ]);
        });
    }

    /**
     * Register the routes needed for managing personal access tokens.
     *
     * @return void
     */
    public function forPersonalAccessTokens()
    {
        Route::group(['middleware' => ['auth:api']], function ($router) {
            Route::get('scopes', [
                'uses' => 'ScopeController@all',
            ]);

            Route::get('personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@forUser',
            ]);

            Route::post('personal-access-tokens', [
                'uses' => 'PersonalAccessTokenController@store',
            ]);

            Route::delete('personal-access-tokens/{token_id}', [
                'uses' => 'PersonalAccessTokenController@destroy',
            ]);
        });
    }
}
