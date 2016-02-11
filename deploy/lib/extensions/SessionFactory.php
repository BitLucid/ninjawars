<?php
namespace NinjaWars\core\extensions;

use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * Modifies Symfony's session singleton to force one instance regardless of storage
 *
 * @see \Symfony\Component\HttpFoundation\Session\Session
 */
class SessionFactory {
	public static $self = null;

	public static function init(
		$storage = null,
		$attributes = null,
		$flashes = null
	) {
		if (self::$self) {
			return self::$self;
		} else {
			self::$self = new SymfonySession(
				$storage,
				$attributes,
				$flashes
			);

			return self::$self;
		}
	}

	public static function getSession() {
		return self::init();
	}

    public static function annihilate() {
        if (self::$self) {
            self::$self->invalidate();
        }

        self::$self = null;
    }
}
