<?php
namespace app\Controller;

use \Item as Item;

/**
 * Handles all user actions related to the in-game Shop
 */
class ShopController { // extends Controller
	public static $alive   = true;  // *** must be alive to access the shop ***
	public static $private = false; // *** do not need to be logged in ***

	protected $itemCosts   = [];
	protected $sessionData = [];

	/**
	 * Grabs data from external state for other methods to us
	 *
	 * @return ShopController
	 * @see item_for_sale_costs
	 */
	public function __construct() {
		$this->itemCosts   = item_for_sale_costs();
		$this->sessionData = [
			'username'         => self_name(),
			'char_id'          => self_char_id(),
			'is_logged_in'     => is_logged_in(),
			'quantity_setting' => get_setting('items_quantity'),
		];
	}

	/**
	 * Display the initial shop view
	 *
	 * @return Array
	 */
	public function index() {
		$parts = array(
			'quantity'  => $this->sessionData['quantity_setting'],
			'view_part' => 'index',
		);

		return $this->render($parts);
	}

	/**
	 * Command for current user to purchase a quantity of a specific item
	 *
	 * @param quantity int The quantity of the item to purchase
	 * @param item string The identity of the item to purchase
	 * @return Array
	 */
	public function buy() {
		$in_quantity       = in('quantity');
		$in_item           = in('item');

		$gold = get_gold($this->sessionData['char_id']);

		$current_item_cost = 0;
		$no_funny_business = false;

		// Pull the item info from the database
		$item_costs        = item_for_sale_costs();
		$item              = getItemByID(item_id_from_display_name($in_item));
		$quantity 		   = whichever(positive_int($in_quantity), $this->sessionData['quantity_setting'], 1);
		$item_text 	       = null;

		if ($item instanceof Item) {

			$item_text = ($quantity > 1 ? $item->getPluralName() : $item->getName());
			$purchaseOrder = new PurchaseOrder();

			// Determine the quantity from input, or settings, or as a fallback, default of 1.
			$purchaseOrder->quantity = $quantity;
			$purchaseOrder->item     = $item;

			$potential_cost    = (isset($item_costs[$purchaseOrder->item->identity()]['item_cost']) ? $item_costs[$purchaseOrder->item->identity()]['item_cost'] : null);
			$current_item_cost = first_value($potential_cost, 0);
			$current_item_cost = $current_item_cost * $purchaseOrder->quantity;

			if (!$this->sessionData['char_id'] || !$purchaseOrder->item || $purchaseOrder->quantity < 1) {
				$no_funny_business = true;
			} else if ($gold >= $current_item_cost) { // Has enough gold.
				try {
					add_item($this->sessionData['char_id'], $purchaseOrder->item->identity(), $purchaseOrder->quantity);
					subtract_gold($this->sessionData['char_id'], $current_item_cost);
				} catch (Exception $e) {
					$invalid_item = $e->getMessage();
					error_log('Invalid Item attempted :'.$invalid_item);
					$no_funny_business = true;
				}
			}
		}

		set_setting('items_quantity', $quantity);

		$parts = array(
			'current_item_cost' => $current_item_cost,
			'quantity'          => $quantity,
			'item_text'         => $item_text,
			'no_funny_business' => $no_funny_business,
			'view_part'         => 'buy',
		);

		return $this->render($parts);
	}

	/**
	 * Generates the view spec hash for displaying a template
	 *
	 * @param p_parts Array Name/Value pairings to pass to the view
	 * @return Array
	 */
	private function render($p_parts) {
		$p_parts['gold']         = get_gold($this->sessionData['char_id']);
		$p_parts['item_costs']   = $this->itemCosts;
		$p_parts['is_logged_in'] = $this->sessionData['is_logged_in'];

		return [
			'template' => 'shop.tpl',
			'title'    => 'Shop',
			'parts'    => $p_parts,
			'options'  => [ 'quickstat' => 'viewinv' ],
		];
	}
}

/**
 * A game-level representation of a request to buy something
 */
class PurchaseOrder {
	public $quantity;
	public $item;
}
