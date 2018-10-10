<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List
     * 
     * List orders from database.
     *
     * @return Response
     */
    public function list(Request $request) {

        $_type = 'error_list_orders';
        $_message = 'Bad Request';

        $list = [];

        $orders = DB::table('order')->get();
        foreach($orders as $order) {
            
            $item = [
                'id' => $order->id,
                'total_order' => $order->total_order,
                'total_discount' => $order->total_discount,
                'discounts' => json_decode($order->discounts, true),
                'total' => $order->total,
                'date' => $order->created_at
            ];

            # Customer
            $customer = DB::table('customer')
                ->where([ 'id' => $order->{'customer-id'}])
                ->first();

            $item['customer'] = collect($customer);

            # Order Items
            $items = DB::table('order-item')
                ->where([ 'order-id' => $order->id])
                ->get();

            $item['items'] = collect($items);

            $list[] = $item;
        }

        return response()->json([
            'status' => true,
            'type' => 'success_list_orders',
            'message' => 'List orders with success.',
            'data' => $list
        ]);
    }

    /**
     * New
     * 
     * Add new order request into database.
     *
     * @return Response
     */
    public function new(Request $request) {

        $order = $request->all();

        $_type = 'error_new_order';
        $_message = 'Bad Request';

        if($this->isValid($order)) {

            DB::beginTransaction();

            try {
                $order_discounts = $this->applyDiscounts($order);
                $order_id = DB::table('order')->insertGetId([ 
                    'customer-id' => $order['customer-id'], 
                    'total_order' => $order_discounts['total_order'],
                    'total_discount' => $order_discounts['total_discount'],
                    'discounts' => json_encode($order_discounts['messages']),
                    'total' => $order_discounts['total']
                ]);

                foreach($order['items'] as $item) {

                    $product = DB::table('product')
                        ->where('id', $item['product-id'])
                        ->first();

                    if(is_null($product))
                        continue;

                    DB::table('order-item')->insert([
                        'order-id' => $order_id,
                        'product-id' => $product->id,
                        'quantity' => $item['quantity'],
                        'unit-price' => floatval($product->price),
                        'total' => intval($item['quantity']) * floatval($product->price)
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'type' => 'success_new_order',
                    'message' => '',
                    'data' => [
                        'orderId' => $order_id 
                    ]
                ]);

            } catch (Exception $e) {
                DB::rollBack();

                $_type = 'error_save_order';
                $_message = 'Error: ' . json_encode($ex);
            }

        } else {

            $_type = 'error_invalid_order';
            $_message = 'Error: This order is invalid. Required fields (`customer-id`, `items`, `total`).';
        }

        return response()->json([
            'status' => false,
            'type' => $_type,
            'message' => $_message
        ]);
    }

    /**
     * Discounts
     * 
     * Check discounts for an order request.
     *
     * @return Response
     */
    public function discounts(Request $request) {

        $order = $request->all();

        $_type = 'error_order_discounts';
        $_message = 'Bad Request';

        if($this->isValid($order)) {

            $order_discounts = $this->applyDiscounts($order);

            return response()->json([
                'status' => true,
                'type' => 'success_discount_order',
                'message' => 'Order discounts retrieved with success.',
                'data' => [
                    'order' => $order_discounts['total_order'],
                    'discounts' => $order_discounts['messages'],
                    'total_discount' => $order_discounts['total_discount'],
                    'total' => $order_discounts['total']
                ]
            ]);

        } else {
            $_type = 'error_invalid_order';
            $_message = 'Error: This order is invalid. Required fields (`customer-id`, `items`, `total`).';
        }

        return response()->json([
            'status' => false,
            'type' => $_type,
            'message' => $_message
        ]);
    }

    /**
     * Is Valid
     * 
     * Checks if order information is valid.
     *
     * @return bolean
     */
    private function isValid($order) {

        $customer_id = $order['customer-id'] ?? null;
        $items = $order['items'] ?? null;
        $total = $order['total'] ?? null;

        if(!is_null($customer_id) && !empty($items) && !is_null($total)) {

            $customer = DB::table('customer')->where('id', $customer_id)->exists();

            if(!empty($customer)) {

                foreach($items as $item) {

                    $_product_id = $item['product-id'] ?? null;
                    $_quantity = $item['quantity'] ?? null;
                    $_unit_price = $item['unit-price'] ?? null;
                    $_total = $item['total'] ?? null;
                    
                    $product = DB::table('product')->where('id', $_product_id)->exists();

                    if(!empty($product) && !is_null($_quantity) && !is_null($_unit_price) && !is_null($_total)) 
                        return true;
                }
            }
        }

        return false;
    }

    /**
     * Get Total
     * 
     * Get order total value from all items. (For easy debug, avoid json errors)
     */
    private function getTotal($order) {

        $total = 0;

        if($this->isValid($order)) {

            foreach($order['items'] as $item) {
            
                $product = DB::table('product')
                    ->where('id', $item['product-id'])
                    ->first();

                if(!is_null($product)) {
                    $total += intval($item['quantity']) * floatval($product->price);
                }
            }

            return $total;
        }
        
        return false;
    }

    /**
     * Get Category List
     * 
     * Get categories
     */
    private function getCategoryList($category_id = null) {

        $list = [
            1 => 'Tools',
            2 => 'Switches'
        ];

        return !is_null($category_id) && isset($list[$category_id]) ? $list[$category_id] : $list;
    }

    /**
     * Get Discounts
     * 
     * Retrieve current active discount rules.
     *
     * @return
     */
    private function applyDiscounts($order) {
        
        $active_discounts = [ 
            'customer-revenue-1000' => $this->customerRevenueDiscount($order, 1000, 0.1),
            'switches-5-pack' => $this->categoryPackDiscount($order, 2, 5),
            'tools-20' => $this->categoryPercentageDiscount($order, 1, 0.2)
        ];

        $apply_messages = [];
        $apply_percentage = 0;
        $apply_value = 0;
        foreach($active_discounts as $discount) {
            
            if($discount['apply']) {

                $type = $discount['type'];
                $value = $discount['discount'];

                switch($type) {
                    case 'percentage':
                        $apply_percentage += floatval($value);
                        break;
                    case 'value':
                        $apply_value += floatval($value);
                        break;
                    default: 
                        continue; 
                }

                $apply_messages[] = $discount['message'];
            }
        }

        $total_order = $this->getTotal($order);

        # Discount Amount
        $total = $total_order - $apply_value;

        # Discount Percentage
        $total = (1 - $apply_percentage) * $total;
        $total_discount = $total_order - $total;

        return [
            'total_order' => sprintf('%0.2f', $total_order),
            'total_discount' => sprintf('%0.2f', $total_discount),
            'total' => sprintf('%0.2f', $total),
            'messages' => $apply_messages
        ];
    }

    /**
     * Costumer Revenue Discount
     * 
     * Apply discount for:
     * A customer who has already bought for over € 1000, gets a discount of 10% on the whole order.
     * 
     * @return array
     */
    private function customerRevenueDiscount($order, $revenue, $discount) {

        $data = [
            'apply' => false,
            'discount' => $discount,
            'type' => 'percentage',
            'message' => ''
        ];

        $customer = DB::table('customer')
            ->where('id', $order['customer-id'])
            ->first();

        $_message = "The customer `{$customer->name}` (id: {$customer->id}) with `{$customer->revenue}€` revenue";

        if(floatval($customer->revenue) > $revenue) {

            $discount_percentage = $discount * 100;

            return array_merge($data, [
                'apply' => true,
                'message' => $_message . " (> {$revenue}€), apply {$discount_percentage}% discount on whole order."
            ]);
        }

        return array_merge($data, [
            'message' => $_message . " (> {$revenue}€), not valid discount."
        ]);
    }

    /**
     * Category Pack Discount 
     * 
     * Apply discount for:
     * For every product of category "Switches" (id 2), when you buy five, you get a sixth for free.
     *
     * @return array
     */
    private function categoryPackDiscount($order, $category, $pack) {

        $categories = $this->getCategoryList();

        $data = [
            'apply' => false,
            'discount' => 0,
            'type' => 'value',
            'message' => ''
        ];

        $packs = 0;
        $offer = false;

        $products = 0;
        $products_total = 0;
        $products_discount = [];
        foreach($order['items'] as $item) {
            
            $product = DB::table('product')
                ->where('id', $item['product-id'])
                ->first();

            if(is_null($product) || $product->category !== $category)
                continue;

            $quantity = intval($item['quantity']);
            $products_total += $quantity;
            
            for ($i=1; $i <= $quantity; $i++) {

                $products++;
                $is_pack = ($products % $pack) == 0 ? true : false;

                if($offer) {
                    $products_discount[] = $product;
                    $offer = false;

                    continue;
                }

                if($is_pack && $products > 0) {
                    $packs++;
                    $offer = true;
                }
            }
        }

        if($packs > 0) {
            
            $offers = count($products_discount);
            $offers_total = 0;
            foreach($products_discount as $product) {
                $offers_total += floatval($product->price);
            }

            return array_merge($data, [
                'apply' => true,
                'discount' => $offers_total,
                'message' => "The order contains `{$products_total}` products of category '{$categories[$category]}' (id: {$category}), so will apply `{$packs}` packs, offer `{$offers}` products, a discount of `{$offers_total}€`."
            ]);
        }

        return array_merge($data, [
            'message' => "The order contains `{$products_total}` products of category '{$categories[$category]}' (id: {$category}), discount not valid."
        ]);
    }

    /**
     * Category Percentage Discount
     * 
     * Apply discount for:
     * If you buy two or more products of category "Tools" (id 1), you get a 20% discount on the cheapest product.
     * 
     * @return array
     */
    private function categoryPercentageDiscount($order, $category, $discount) {

        $categories = $this->getCategoryList();

        $data = [
            'apply' => false,
            'discount' => 0,
            'type' => 'value',
            'message' => ''
        ];

        $products = 0;
        $product_discount = [ 'product-id' => null, 'price' => null, 'discount' => null ];
        foreach($order['items'] as $item) {
            
            $product = DB::table('product')
                ->where('id', $item['product-id'])
                ->first();

            if(is_null($product) || $product->category !== $category)
                continue;
            
            if(is_null($product_discount['price']) || $product->price < $product_discount['price'] ) {
                $product_discount = [
                    'id' => $product->id,
                    'price' => $product->price,
                    'discount' => $discount * $product->price
                ];
            }

            $products++;
        }

        if($products >= 2) {

            $discount_percentage = $discount * 100;

            return array_merge($data, [
                'apply' => true,
                'discount' => $product_discount['discount'],
                'message' => "The order contains `{$products}` products of category '{$categories[$category]}' (id: {$category}), so will apply `{$discount_percentage}%` on cheapest product, (id: {$product_discount['id']}) `{$product_discount['price']}€`, a discount of `{$product_discount['discount']}€`."
            ]);
        }

        return array_merge($data, [
            'message' => "The order contains `{$products}` of category '{$categories[$category]}' (id: {$category}), discount not valid."
        ]);
    }
}
