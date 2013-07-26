<?php

use Slim\Slim;
use Guzzle\Http\Client;
use Slim\Extras\Views\Twig;

require '../vendor/autoload.php';
require '../config/config.php';

$app = new Slim([
//    'view' => new Twig,
    'custom' => $config
]);

$app->get('/', 'mainAction');
$app->get('/leaderboard', 'leaderboardAction');
$app->post('/update', 'addPointsAction');

$app->run();

function mainAction()
{
    Slim::getInstance()->render('index.html', []);
}

function leaderboardAction()
{
    return jsonResponse(getLeaderboard());
}

function addPointsAction()
{
    $request = Slim::getInstance()->request();
    $data = json_decode($request->getBody());

    return jsonResponse(addPoints($data));
}

function getLeaderboard()
{
    $client = new Client(getDbHost());
    $url = "_design/points/_view/Points?group=true";
    $data = $client->get($url)->send();

    return $data->json();
}

function addPoints($data)
{
    $client = new Client(getDbHost());
    $uuid =uniqid().uniqid();
    $data->name = filter_var($data->name, FILTER_SANITIZE_STRING);
    $body = json_encode($data);
    $client->put($uuid, null, $body)->send();

    $row = getUserRow($data->name);

    return $row;
}

function getUserRow($user)
{
    $leaderboard = getLeaderboard();
    $user = filter_var($user, FILTER_SANITIZE_STRING);
    foreach ($leaderboard['rows'] as $record) {
        if ($record['key'] == $user) {
            return ['rows'=>$record];
        }
    }

    return null;
}

function getDbHost()
{
    $config = Slim::getInstance()->config('custom');

    return $config['db']['host'];
}

function jsonResponse($data)
{
    $response = Slim::getInstance()->response();
    $response['Content-Type'] = 'application/json';
    $response->body(json_encode($data));

    return $response;
}
