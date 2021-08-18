<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;

use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;

use App\Models\User;
use App\Order;
use App\Cart;
use App\Good;
use App\Sale;
use App\Seller;
use App\Admin;
use Auth;
use DB;

class CheckoutController extends Controller
{
    public function charge(Request $request)
    {
        // dd($request->all());
        $user = $request->user();

        try {
            // Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            Stripe::setApiKey('sk_test_51FyXZOIfMsn8pOBN2CZG6KzUcUgWlySQBGLW8rP9x7ZL5P32nIyuoPfSG3VPDDqylbhmFYGIF0tuV5whXP7nGeUS00lHF870xd');

            $customer = Customer::create(array(
                // 'email' => $request->stripeEmail,
                'email' => $user->email,
                'source' => $request->id
            ));

            $carts = Cart::where('carts.user_id', $user->id)
            ->get();

            // $goodPrice = 0;
            $goodName = [];
            $goodQuantity = [];
            $goodColor = [];
            $goodImage = [];
            $goodPrice = [];
            $goodSubTotPrice = 0;
            $goodTotPrice = 0;

            foreach ($carts as $cart){
                $goodName[] = $cart->name;
                $goodQuantity[] = $cart->quantity;
                $goodColor[] = $cart->color;
                $goodImage[] = $cart->image;
                $goodPrice[] = $cart->price;
                $goodSubTotPrice = $goodSubTotPrice + ($cart->price * $cart->quantity);
                $goodTotPrice = $goodTotPrice + ($cart->price * $cart->quantity);

                $good = Good::find($cart->good_id);
                $seller = Seller::find($cart->seller_id);
                $admin = Admin::find(1);
                // $good = Good::find(1);

                Good::where('id', '=', $cart->good_id)
                ->update([
                    'quantity' =>
                    $good->quantity - $cart->quantity        ,
                    'purchases' =>
                    $good->purchases + 1        ,
                    // Prevent the updated_at column from being refreshed every time there is a new view
                    'updated_at' => \DB::raw('updated_at')
                ]);

                $sellerCash = 0.9 * $cart->price;
                $adminCash = $cart->price - $sellerCash;

                Seller::where('id', '=', $cart->seller_id)
                ->update([
                    'account_balance' =>
                    $seller->account_balance + $sellerCash      ,
                    'sales' =>
                    $seller->sales + 1      ,
                    // Prevent the updated_at column from being refreshed every time there is a new view
                    'updated_at' => \DB::raw('updated_at')
                ]);

                Admin::where('id', '=', '1')
                ->update([
                    'account_balance' =>
                    $admin->account_balance + $adminCash      ,
                    'sales' =>
                    $admin->sales + 1      ,
                    // Prevent the updated_at column from being refreshed every time there is a new view
                    'updated_at' => \DB::raw('updated_at')
                ]);

                $sale = new Sale;

                $sale->user_id = $user->id;
                $sale->user_name = $request->input('first_name');
                $sale->seller_id = $seller->id;
                $sale->seller_name = $seller->name;
                $sale->country = $request->input('country');
                $sale->address1 = $request->input('address1');
                $sale->city = $request->input('city');
                $sale->zip = $request->input('zip');
                $sale->phone = $request->input('phone');
                $sale->email = $user->email;
                $sale->good_id = $cart->id;
                $sale->good_name = $cart->name;
                $sale->good_quantity = $cart->quantity;
                $sale->good_color = $cart->color;
                $sale->good_image = $cart->image;
                $sale->good_price = $cart->price;

                $sale->save();
            }

            $charge = Charge::create(array(
                // 'customer' => $request->card->id,
                'customer' => $customer->id,
                'amount' => $goodTotPrice * 100,
                'currency' => 'usd'
            ));

            $order = new Order;

            $order->user_id = $user->id;
            $order->first_name = $request->input('first_name');
            $order->last_name = $request->input('last_name');
            $order->country = $request->input('country');
            $order->address1 = $request->input('address1');
            $order->city = $request->input('city');
            $order->zip = $request->input('zip');
            $order->phone = $request->input('phone');
            $order->email = $user->email;
            $order->goodsName = json_encode($goodName);
            $order->goodsQuantity = json_encode($goodQuantity);
            $order->goodsColor = json_encode($goodColor);
            $order->goodsImage = json_encode($goodImage);
            $order->goodsPrice = json_encode($goodPrice);
            $order->subtotal = $goodSubTotPrice;
            $order->total = $goodTotPrice;

            $order->save();
            $carts->each->delete();

            $data = [
                'carts' => $carts,
                'user' => $user,
                'customer'=>$customer,
                'charge'=>$charge,
                'order' => $order,
            ];


            return response()->json($data,200);
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }
}
