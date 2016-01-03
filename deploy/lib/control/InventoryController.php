<?php
namespace NinjaWars\core\control;

require_once(LIB_ROOT."control/lib_inventory.php");

use \Player as Player;

/**
 * Control the display of items and gold (and maybe some day armor) for a char
 */
class InventoryController {
	const PRIV  = true;
	const ALIVE = false;

	/**
	 * View items and gold of char
	 */
	public function index() {
		$char = new Player(self_char_id());

		$inv_counts = inventory_counts($char->id());
		$inventory = array();

		if ($inv_counts) {
			// Standard item info.
			$standard_items = standard_items();
			// Make the information into a single, trivially usable, array.
			foreach ($inv_counts as $item_info) {
				$l_id    = $item_info['item_type'];
				$l_name  = $item_info['name'];
				$l_count = $item_info['count'];

				if (isset($standard_items[$l_id]) && isset($l_count)) {
					// If a type of item exists and has a non-zero count, join the array of it's count with it's standard info.
					$inventory[$l_name] = array('count'=>$l_count) + $standard_items[$l_id];
				}
			}
		} else {
			$inventory = false;
		}

		$parts = [
			'gold'         => $char->gold(),
			'gold_display' => number_format($char->gold()),
			'inventory'    => $inventory,
			'username'     => $char->name(),
			'char_id'      => $char->id(),
		];

		return $this->render($parts);
	}

	public function render($parts) {
		return [
			'template' => 'inventory.tpl',
			'title'    => 'Your Inventory',
			'parts'    => $parts,
			'options'  => [
				'body_classes' => 'inventory',
				'quickstat'    => 'viewinv',
			],
		];
	}
}
