<?php

namespace NinjaWars\core\control;

use NinjaWars\core\control\AbstractController;
use NinjaWars\core\Filter;
use NinjaWars\core\data\Item;
use NinjaWars\core\data\Shop;
use NinjaWars\core\data\PurchaseOrder;
use NinjaWars\core\data\Player;
use NinjaWars\core\data\Inventory;
use NinjaWars\core\extensions\StreamedViewResponse;
use NinjaWars\core\environment\RequestWrapper;
use Pimple\Container;

/**
 * Handles all user actions related to the in-game Shop
 */
class ShopController extends AbstractController
{
    public const ALIVE = true;  // *** must be alive to access the shop ***
    public const PRIV  = false; // *** do not need to be logged in ***

    public const MARKUP = 1.5;
    public const DEFAULT_QUANTITY = 1;
    public $ad_divisor = 10; // Only show the ad 1/10th of the time
    // because it keeps breaking the page

    /**
     * Display the initial shop view
     *
     * @return Array
     */
    public function index(Container $p_dependencies): StreamedViewResponse
    {
        $player = $p_dependencies['current_player'];
        $authenticated = $p_dependencies['session'] ? $p_dependencies['session']->get('authenticated') : false;
        $parts = [
            'shopSections' => ['index'],
            'gold'      => ($player ? $player->gold : 0),
            'item_costs'        => Shop::itemForSaleCosts(),
            'authenticated'     => $authenticated,
            'show_ad'           => rand(1, $this->ad_divisor) === 1,
        ];

        return $this->render($parts);
    }

    /**
     * Command for current user to purchase a quantity of a specific item
     *
     * @param quantity int The quantity of the item to purchase
     * @param item string The identity of the item to purchase
     * @return Array
     */
    public function buy(Container $p_dependencies): StreamedViewResponse
    {
        $request           = RequestWrapper::$request;
        $in_quantity       = $request->get('quantity');
        $in_item           = $request->get('item');
        $player            = $p_dependencies['current_player'];
        $authenticated     = $p_dependencies['session'] ? $p_dependencies['session']->get('authenticated') : false;
        $gold              = ($player ? $player->gold : null);
        $current_item_cost = 0;
        $no_funny_business = false;
        $no_such_item      = false;
        $item              = Item::findByIdentity($in_item);
        $quantity 		   = max(Filter::toNonNegativeInt($in_quantity), self::DEFAULT_QUANTITY);
        $item_text 	       = null;
        $valid             = false;

        if (!($item instanceof Item)) {
            $no_such_item = true;
        } else {
            $item_text = ($quantity > 1 ? $item->getPluralName() : $item->getName());
            $purchase_order = new PurchaseOrder();

            // Determine the quantity from input or as a fallback, default of 1.
            $purchase_order->quantity = $quantity;
            $purchase_order->item     = $item;
            $current_item_cost = Shop::calculatePrice($purchase_order);

            if (!$player) {
                $no_funny_business = true;
            } elseif (!$purchase_order->item || $purchase_order->quantity < 1) {
                $no_such_item = true;
            } elseif ($gold >= $current_item_cost) { // Has enough gold.
                try {
                    $inventory = new Inventory($player);
                    $inventory->add($purchase_order->item->identity(), $purchase_order->quantity);
                    $player->setGold($player->gold - $current_item_cost);
                    $player->save();
                    $valid = true;
                } catch (\InvalidArgumentException $e) {
                    $invalid_item = $e->getMessage();
                    error_log('Invalid Item attempted :'.$invalid_item);
                    $no_funny_business = true;
                }
            }
        }

        $parts = [
            'current_item_cost' => $current_item_cost,
            'quantity'          => $quantity,
            'item_text'         => $item_text,
            'no_funny_business' => $no_funny_business,
            'no_such_item'      => $no_such_item,
            'valid'             => $valid,
            'shopSections'      => ['buy'],
            'gold'              => $gold,
            'item_costs'        => Shop::itemForSaleCosts(),
            'authenticated'     => $authenticated,
            'show_ad'           => rand(1, $this->ad_divisor) === 1,
        ];

        return $this->render($parts);
    }

    /**
     * Generates the view arguments for displaying the shop template
     *
     * @param p_parts Array Name/Value pairings to pass to the view
     * @return StreamedViewResponse
     */
    private function render($p_parts)
    {
        return new StreamedViewResponse('Shop', 'shop.tpl', $p_parts, [ 'quickstat' => 'viewinv' ]);
    }
}
