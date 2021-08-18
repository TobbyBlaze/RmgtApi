<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Good;
use App\Cart;
use App\Review;
use App\User;
use App\Order;
use App\OrderProduct;
use Auth;
use DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('cors');
    }
    
    public function index()
    {
        $user = Auth::user();

        $orders = Order::orderBy('orders.updated_at', 'desc')
        ->where('orders.user_id', $user->id)
        ->paginate(20);

        $orderNum = Order::where('orders.user_id', $user->id)
        ->get()->count();

        $data = [

            // 'user' => $user,
            'orders'=>$orders,
            'orderNum' => $orderNum,

        ];

        return response()->json($data,200);
    }

    public function store(Request $request)
    {
        // $cart = Cart::find($id);
        $user = User::find(auth::user()->id);
       
        $order = new Order;
        
        $order->user_id = $user->id;
        $order->first_name = $request->input('first_name');
        $order->last_name = $request->input('last_name');
        $order->country = $request->input('country');
        $order->address1 = $request->input('address1');
        $order->address2 = $request->input('address2');
        $order->city = $request->input('city');
        $order->state = $request->input('state');
        $order->zip = $request->input('zip');
        $order->phone = $request->input('phone');
        $order->email = $request->input('email');

        $order->save();
   
        $cart = Cart::orderBy('carts.updated_at', 'desc')
        // ->where('carts.user_id', $user->id)
        ->get();
        // $cart = Cart::all();

        // $cart->delete();

        // return redirect('/')->with('success', 'cart created successfully');
        return response()->json($order, 201);
    }

    public function viorder($id, Request $request)
    {
        $orderGoods = Order::find($id);
        
        $data = [
            'orderGoods'=>$orderGoods,
        ];

        return response()->json($data,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
