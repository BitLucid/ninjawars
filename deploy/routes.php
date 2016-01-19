<?php
namespace NinjaWars\core;

/*
 * Adding a default entry helps for when the action is not found or not
 * provided, otherwise a 404 will occur.
 */
Router::$routes = [
    'login' => [
        'type'    => 'controller',
        'actions' => [
            'login_request' => 'requestLogin',
        ],
    ],
    'clan' => [
        'type'    => 'controller',
        'actions' => [
            'new'     => 'create',
            'default' => 'listClans',
            'list'    => 'listClans',
        ],
    ],
    'shop' => [
        'type'    => 'controller',
        'actions' => [
            'purchase' => 'buy',
        ],
    ],
    'work' => [
        'type'    => 'controller',
        'actions' => [
            'request_work' => 'requestWork',
        ],
    ],
    'shrine' => [
        'type'    => 'controller',
        'actions' => [
            'heal_and_resurrect' => 'healAndResurrect',
        ],
    ],
    'doshin' => [
        'type'    => 'controller',
        'actions' => [
            'Bribe'        => 'bribe',
            'Offer Bounty' => 'offerBounty',
        ],
    ],
    'stats' => [
        'type'    => 'controller',
        'actions' => [
            'change_details' => 'changeDetails',
            'update_profile' => 'updateProfile',
        ],
    ],
    'messages' => [
        'type'    => 'controller',
        'actions' => [
            'delete_clan'     => 'deleteClan',
            'delete_messages' => 'deletePersonal',
            'delete_message'  => 'deletePersonal',
            'send_clan'       => 'sendClan',
            'send_personal'   => 'sendPersonal',
            'personal'        => 'viewPersonal',
            'clan'            => 'viewClan',
            'default'         => 'viewPersonal',
        ],
    ],
    'account' => [
        'type'    => 'controller',
        'actions' => [
            'show_change_email_form'    => 'showChangeEmailForm',
            'change_email'              => 'changeEmail',
            'show_change_password_form' => 'showChangePasswordForm',
            'change_password'           => 'changePassword',
            'show_confirm_delete_form'  => 'deleteAccountConfirmation',
            'delete_account'            => 'deleteAccount',
        ],
    ],
    'consider' => [
        'type'    => 'controller',
        'actions' => [
            'add'    => 'addEnemy',
            'delete' => 'deleteEnemy',
        ],
    ],
    'chat' => [
        'type'    => 'controller',
        'actions' => [
            'receive' => 'receive',
        ],
    ],
    'rules' => [
        'type'  => 'simple',
        'title' => 'rules',
    ],
    'staff' => [
        'type'  => 'simple',
        'title' => 'NinjaWars Staff',
    ],
    'public' => [
        'type'  => 'simple',
        'title' => 'Public Discussion',
    ],
    'interview' => [
        'type'  => 'simple',
        'title' => 'Interview with John Facey',
    ],
    'about' => [
        'type'  => 'simple',
        'title' => 'About NinjaWars',
    ],
];

Router::$controllerAliases = [
    'doshin_office' => 'doshin',
    'village'       => 'chat',
    'enemies'       => 'consider',
];
