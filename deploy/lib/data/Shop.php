<?php

namespace NinjaWars\core\data;

use NinjaWars\core\data\DatabaseConnection;
use \PDO;

/**
 *
 * Operations for Shops, their inventory/items list, etc
 */
class Shop
{
    /**
     * Pulls the shop items costs and all
     */
    public static function itemForSaleCosts($administrative = false): array
    {
        $sel = 'select item_display_name, item_internal_name, item_cost, image, usage from item where for_sale = TRUE order by image is not null desc, item_cost asc';

        $items_data = query($sel);
        // Rearrange the array to use the internal identity as indexes.
        $item_costs = array();

        foreach ($items_data as $item_data) {
            $item_costs[$item_data['item_internal_name']] = $item_data;
        }

        return $item_costs;
    }

    /**
     * For admin view of shop items for balancing
     */
    public static function fullItems($administrative = false): array
    {
        if ((defined('DEBUG') && DEBUG) || $administrative) {
            $sel = 'select item_display_name, item_internal_name, item_cost, image, usage from item order by for_sale DESC, image is not null desc, item_cost asc';
        } else {
            return [];
        }

        $items_data = query($sel);
        // Rearrange the array to use the internal identity as indexes.
        $item_costs = array();

        foreach ($items_data as $item_data) {
            $item_costs[$item_data['item_internal_name']] = $item_data;
        }

        return $item_costs;
    }

    /**
     * Calculate price of items with markup.
     */
    public static function calculatePrice(PurchaseOrder $purchase_order): int
    {
        return (int) ceil($purchase_order->item->item_cost * $purchase_order->quantity);
    }
}
