<?php
namespace NinjaWars\core;

/*
 * Adding a default entry helps for when the action is not found or not
 * provided, otherwise a 404 will occur.
 */
Router::$routes = [
    'signup' => [
        'type'    => 'controller',
    ],
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
    'list' => [
        'type'    => 'controller',
        'actions' => [
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
    'duel' => [
        'type'    => 'controller',
        'actions' => [
        ],
    ],
    'stats' => [
        'type'    => 'controller',
        'actions' => [
            'change_details' => 'changeDetails',
            'update_profile' => 'updateProfile',
        ],
    ],
    'npc' => [
        'type'    => 'controller',
        'actions' => [
        ],
    ],
    'events' => [
        'type'    => 'controller',
        'actions' => [
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
            'message'         => 'viewPersonal',
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
    'inventory' => [
        'type'    => 'controller',
        'actions' => [
            'use'      => 'useItem',
            'self_use' => 'selfUse',
        ],
    ],
    'skill' => [
        'type'    => 'controller',
        'actions' => [
            'use'      => 'go',
            'self_use' => 'selfUse',
            'post_use' => 'postUse',
            'post_self_use' => 'postSelfUse'
        ],
    ],
    'quest' => [
        'type'  => 'controller',
        'actions' => [
        ],
    ],
    'consider' => [
        'type'    => 'controller',
        'actions' => [
            'add'    => 'addEnemy',
            'delete' => 'deleteEnemy',
        ],
    ],
    'assistance' => [
        'type'    => 'controller',
        'actions' => [
        ],
    ],
    'chat' => [
        'type'    => 'controller',
        'actions' => [
            'receive' => 'receive',
        ],
    ],
    'map' => [
        'type'    => 'controller',
        'actions' => [
            'view' => 'index'
        ],
    ],
    'player' => [
        'type'    => 'controller',
        'actions' => [],
    ],
    'news' => [
        'type'    => 'controller',
        'actions' => [],
    ],
    'error' => [
        'type'    => 'controller',
        'action' => [],
    ],
    'rules' => [
        'type'  => 'simple',
        'title' => 'Rules',
    ],
    'staff' => [
        'type'  => 'simple',
        'title' => 'Staff',
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
    'intro' => [
        'type'  => 'simple',
        'title' => 'Intro to the game'
    ],
];

Router::$controllerAliases = [
    'doshin_office' => 'doshin',
    'village'       => 'chat',
    'enemies'       => 'consider',
    'duel'          => 'rumor',
    'item'          => 'inventory',
];
