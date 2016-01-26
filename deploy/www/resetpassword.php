<?php
/*
Eventual route definitions
    'resetpassword' => [
        'type'    => 'controller',
        'actions' => [
            'email'      => 'postEmail',
            'reset'      => 'getReset',
            'post_reset' => 'postReset',
        ],
    ],
 */

use NinjaWars\core\control\PasswordController;
use NinjaWars\core\data\PasswordResetRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

$command = (string) in('command');

$controller = new PasswordController();

$request = Request::createFromGlobals();

switch (true) {
        case ($command == 'post_reset'):
        $response = $controller->postReset($request);
        break;
    case ($command == 'reset'):
        $response = $controller->getReset($request);
        break;
    case ($command == 'email'):
        $response = $controller->postEmail($request);
        break;
    default:
        $command == 'index';
        $response = $controller->index($request);
        break;
}

if ($response instanceof RedirectResponse) {
    $response->send();
} else {
    display_page(
        $response['template'],
        $response['title'],
        $response['parts'],
        $response['options']
    );
}
