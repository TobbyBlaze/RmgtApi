<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Good;
use App\Sale;
use App\Seller;
use Auth;
use DB;

class SellerDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function goods(Request $request)
    {
        $seller = Auth::user();
        $Goods = Good::orderBy('goods.updated_at', 'desc')
        ->where('goods.seller_id', $seller->id)
        ->get();

        $data = [
            'Goods'=>$Goods,
        ];

        return response()->json($data,200);
    }

    public function sales(Request $request)
    {
        $seller = Auth::user();
        $Sales = Sale::orderBy('sales.updated_at', 'desc')
        ->where('sales.seller_id', $seller->id)
        ->get();

        $data = [
            'Sales'=>$Sales,
        ];

        return response()->json($data,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
