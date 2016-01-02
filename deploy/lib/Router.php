<?php
namespace app\Core;

class Router{
	public function all(){
		$routes = [
			'clan' => [
				'new'     => 'create',
				'default' => 'listClans',
			],
			'shop' => [
				'default'  => 'index',
				'purchase' => 'buy',
			],
			'casino' => [
				'default'  => 'index',
			],
			'work' => [
				'request_work'=>'requestWork',
				'default'  => 'index',
			],
			'messages' => [
				'delete_clan'=>'deleteClan',
				'delete_messages'=>'deletePersonal',
				'send_clan'=>'sendClan',
				'send_personal'=>'sendPersonal',
				'clan'=>'viewClan',
				'default'  => 'viewPersonal',
			],
		];
		return $routes;
	}
}