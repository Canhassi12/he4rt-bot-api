<?php

declare(strict_types=1);

use App\Http\Controllers\Feedbacks\FeedbacksController;
use Laravel\Lumen\Routing\Router;

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

if (config('app.env') !== "production") {
    $router->get('swagger', function () {
        return view('swagger');
    });
}

$router->get('/auth/oauth/{provider}', 'AuthController@authenticate');
$router->get('/auth/logout', 'AuthController@logout');

$router->group(['prefix' => 'users', 'middleware' => 'bot-auth'], function (Router $router) {
    /*
    |--------------------------------------------------------------------------
    | Basic User Routes
    |--------------------------------------------------------------------------
    | Only for CRUD operations
    */

    $router->get('/', 'Users\UsersController@getUsers');
    $router->post('/', ['uses' => 'Users\UsersController@postUser', 'as' => 'users.store']);
    $router->get('/{discordId}', ['uses' => 'Users\UsersController@getUser', 'as' => 'users.show']);
    $router->put('/{discordId}', ['uses' => 'Users\UsersController@putUser', 'as' => 'users.update']);
    $router->delete('/{discordId}', ['uses' => 'Users\UsersController@deleteUser', 'as' => 'users.destroy']);

    /*
    |--------------------------------------------------------------------------
    | Specific Gamefication Routes
    |--------------------------------------------------------------------------
    | For gamefication and other stuff
    */

    $router->post('/{discordId}/daily', ['uses' => 'Users\UsersController@postDaily', 'as' => 'users.dailyPoints']);
    $router->post('/{discordId}/message', ['uses' => 'Users\MessagesController@postMessage', 'as' => 'users.messages.store']);
    $router->post('/{discordId}/claim-badge', ['uses' => 'Events\BadgesController@postClaimBadge', 'as' => 'users.badges.claim']);

});

$router->group(['prefix' => 'events', 'middleware' => 'bot-auth'], function (Router $router) {
    $router->group(['prefix' => 'badges'], function (Router $router) {
        $router->post('/', ['uses' => 'Events\BadgesController@postBadge', 'as' => 'events.badges.store']);
    });
});


$router->group(['prefix' => 'bot', 'middleware' => 'bot-auth'], function (Router $router) {
    $router->group(['prefix' => 'gambling'], function (Router $router) {
        $router->put('money', 'Gamification\GamblingController@putMoney');
    });
});

$router->group(['prefix' => 'ranking'], function (Router $router) {
    $router->get('general', 'Gamification\RankingController@getGeneralLevelRanking');
    $router->get('messages', 'Gamification\RankingController@getGeneralMessageRanking');
});

$router->group(['prefix' => 'feedback'], function (Router $router) {
    $router->post('/', FeedbacksController::class.'@create');
});

if (config('features.gamification.badges')) {
    $router->group(['prefix' => 'badges'], function (Router $router) {
        $router->get('/', 'Gamification\BadgeController@getBadges');
        $router->post('/', 'Gamification\BadgeController@postBadge');
        $router->get('/{badgeId}', 'Gamification\BadgeController@getBadge');
        $router->delete('/{badgeId}', 'Gamification\BadgeController@deleteBadge');
    });
}
