<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Good;
// use App\Cart;
use App\Review;
use App\User;
use App\Seller;
use Auth;
use DB;

class StoreController extends Controller
{
    public function index()
    {
        $sellers = Seller::orderBy('sellers.updated_at', 'desc')
        ->paginate(2);

        $data = [
            'sellers' => $sellers,
        ];

        return response()->json($data,200);
    }

    public function show($id, Request $request)
    {
        $seller = Seller::find($id);
        $sort = $request->input('sort');
        $brand = $request->input('brand');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');
        $minRating = $request->input('minRating');
        $maxRating = $request->input('maxRating');

        if($minPrice == null){
            $minPrice = 0;
        }
        if($maxPrice == null){
            $maxPrice = 999999;
        }
        if($minRating == null){
            $minRating = 0;
        }
        if($maxRating == null){
            $maxRating = 5;
        }

        switch($sort){
            case 'ph2l': {
                $storeGoods = Good::orderBy('goods.price', 'desc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.seller_id', '=', $seller->id],
                    ['goods.rating', '>=', $minRating],
                    ['goods.rating', '<=', $maxRating]])
                // ->where('goods.price', '>=', $minPrice)
                // ->where('goods.price', '<=', $maxPrice)
                // ->where('goods.rating', '>=', $minRating)
                // ->where('goods.rating', '<=', $minRating)
                // ->where('goods.brand', '>=', $brand)
                // ->where('goods.seller_id', $seller->id)
                ->paginate(1);
                break;
            }
            case 'pl2h': {
                $storeGoods = Good::orderBy('goods.price', 'asc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.seller_id', '=', $seller->id],
                    ['goods.rating', '>=', $minRating],
                    ['goods.rating', '<=', $maxRating]])
                // ->where('goods.price', '>=', $minPrice)
                // ->where('goods.price', '<=', $maxPrice)
                // ->where('goods.rating', '>=', $minRating)
                // ->where('goods.rating', '<=', $minRating)
                // ->where('goods.brand', '>=', $brand)
                // ->where('goods.seller_id', $seller->id)
                ->paginate(1);
                break;
            }
            case 'rating': {
                $storeGoods = Good::orderBy('goods.rating', 'desc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.seller_id', '=', $seller->id],
                    ['goods.rating', '>=', $minRating],
                    ['goods.rating', '<=', $maxRating]])
                // ->where('goods.price', '>=', $minPrice)
                // ->where('goods.price', '<=', $maxPrice)
                // ->where('goods.rating', '>=', $minRating)
                // ->where('goods.rating', '<=', $minRating)
                // ->where('goods.brand', '>=', $brand)
                // ->where('goods.seller_id', $seller->id)
                ->paginate(1);
                break;
            }
            default: {
                $storeGoods = Good::orderBy('goods.updated_at', 'desc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.seller_id', '=', $seller->id],
                    ['goods.rating', '>=', $minRating],
                    ['goods.rating', '<=', $maxRating]])
                // ->where('goods.price', '>=', $minPrice)
                // ->where('goods.price', '<=', $maxPrice)
                // ->where('goods.rating', '>=', $minRating)
                // ->where('goods.rating', '<=', $minRating)
                // ->where('goods.brand', '>=', $brand)
                // ->where('goods.seller_id', $seller->id)
                ->paginate(1);
            }

        }

        // $storeGoods = Good::orderBy('goods.updated_at', 'desc')
        // // ->where('goods.price', '>', $minPrice)
        // // ->where('goods.price', '<', $maxPrice)
        // // ->where('goods.rating', '>', $minRating)
        // // ->where('goods.rating', '<', $minRating)
        // // ->where('goods.brand', '>=', $brand)
        // ->where('goods.seller_id', $seller->id)
        // ->paginate(20);

        $data = [
            'seller' => $seller,
            'storeGoods'=>$storeGoods,
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minRating' => $minRating,
            'maxRating' => $maxRating,
        ];

        return response()->json($data,200);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
