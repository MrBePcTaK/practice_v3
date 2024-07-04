<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FoodOrdersController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->query('id');
        Log::debug($userId);

        if(is_null($userId)) {
            abort(403);
        }
//
//        $user = new User();
//        $orders = $user->orders()->where('date', date('Y-m-d'));
//        Log::debug('orders', $orders);

        $product = new Product();
        $menu = $product->getMenuToday();
        if($menu->isEmpty()) {
            $menu = null;
        }

        return view('menu', [
            'menu' => $menu,
            'order' => null
        ]);
    }
}
