<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Good;
use App\viewGoods;
use App\Review;
use App\Models\User;
use App\Seller;
use App\Category;
use Auth;
use DB;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Agent;

class GoodsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = '';

        $sId = viewGoods::select('sellerID')
        ->where('ip', $ipaddress)
        ->groupBy('sellerID')
        ->orderByRaw('COUNT(*) DESC')
        ->limit(1)
        ->first();
        $cId = viewGoods::select('goodCategory')
        ->where('ip', $ipaddress)
        ->groupBy('goodCategory')
        ->orderByRaw('COUNT(*) DESC')
        ->limit(1)
        ->first();
        // $sId = 2;
        // $cId = 2;

        if($sId != null && $cId != null){
            $personalGoods = Good::orderBy('goods.rating', 'desc')
            ->where('goods.seller_id', $sId->sellerID)
            ->orWhere('goods.category', $cId->goodCategory)
            ->paginate(5);
        }elseif($sId != null){
            $personalGoods = Good::orderBy('goods.rating', 'desc')
            ->where('goods.seller_id', $sId->sellerID)
            ->paginate(5);
        }elseif($cId != null){
            $personalGoods = Good::orderBy('goods.rating', 'desc')
            ->where('goods.category', $cId->goodCategory)
            ->paginate(5);
        }else{
            $personalGoods = Good::orderBy('goods.updated_at', 'desc')
            ->paginate(5);
        }


        $newGoods = Good::orderBy('goods.updated_at', 'desc')
        ->paginate(5);

        $popGoods = Good::orderBy('goods.views', 'desc')
        ->paginate(20);

        $data = [
            'sId'=>$sId,
            'cId'=>$cId,
            'personalGoods'=>$personalGoods,
            'newGoods'=>$newGoods,
            'popGoods'=>$popGoods,
        ];

        return response()->json($data,200);
    }

    public function cat($id, Request $request)
    {
        $category = Category::find($id);
        // $catGoods = Good::orderBy('goods.updated_at', 'desc')
        // ->where('goods.category', $category->sub_category)
        // ->paginate(5);
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
                $catGoods = Good::orderBy('goods.price', 'desc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.category', '=', $category->sub_category],
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
                $catGoods = Good::orderBy('goods.price', 'asc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.category', '=', $category->sub_category],
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
                $catGoods = Good::orderBy('goods.rating', 'desc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.category', '=', $category->sub_category],
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
                $catGoods = Good::orderBy('goods.updated_at', 'desc')
                ->where([
                    ['goods.price', '>=', $minPrice],
                    ['goods.price', '<=', $maxPrice],
                    ['goods.category', '=', $category->sub_category],
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
            'catGoods'=>$catGoods,
            'sort' => $sort,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'minRating' => $minRating,
            'maxRating' => $maxRating,
        ];

        return response()->json($data,200);
    }

    public function categories()
    {

        $categories = Category::get();

        $data = [
            'categories'=>$categories,
        ];

        return response()->json($data,200);
    }

    public function homeCategories()
    {

        $categories = Category::orderBy('categories.views', 'desc')
        ->get();

        $data = [
            'categories'=>$categories,
        ];

        return response()->json($data,200);
    }

    public function store(Request $request)
    {
        $user = Seller::find(Auth::user()->id);

        $messages = [
            "attachments.max" => "file can't be more than 3."
         ];

        $this->validate($request, [
            'name' => 'required',
            'image.*' => 'mimes:jpg,jpeg,bmp,png,gif|max:20000',
            'image' => 'max:3',
        ], $messages);

        if($request->hasFile('image')){

            foreach ($request->file('image') as $sinfile){
                $filenameWithExt = $sinfile->getClientOriginalName();
                $sinfile->move(public_path().'/file/', $filenameWithExt);
                $data[] = $filenameWithExt;
                $extension = $sinfile->getClientOriginalExtension();
            }

            //create good

            $good = new Good;
            $good->name = $request->input('name');
            $good->description = $request->input('description');
            $good->originalPrice = $request->input('originalPrice') - 0.01;
            if($request->input('discount')){
                $good->discount = $request->input('discount');
                $good->price = $good->originalPrice - ($good->discount * 0.01 * $good->originalPrice);
            }else{
                $good->price = $good->originalPrice;
            }
            if($request->input('lowestPrice')){
                $good->bargain = true;
                $good->lowestPrice = $request->input('lowestPrice');
            }
            $good->category = $request->input('category');
            $good->quantity = $request->input('quantity');
            $good->seller_id = Auth::user()->id;
            $good->seller_name = $user->name;
            $good->countryName = $user->countryName;
            $good->cityName = $user->cityName;
            $good->image = json_encode($data);

            $good->save();

            return response()->json($good, 201);
        }else{
            $filenameToStore = 'NoFile';

            //create good

            $good = new Good;
            $good->name = $request->input('name');
            $good->description = $request->input('description');
            $good->originalPrice = $request->input('originalPrice');
            if($request->input('discount')){
                $good->discount = $request->input('discount');
                $good->price = $good->originalPrice - ($good->discount * 0.01 * $good->originalPrice);
            }else{
                $good->price = $good->originalPrice;
            }
            if($request->input('lowestPrice')){
                $good->bargain = true;
                $good->lowestPrice = $request->input('lowestPrice');
            }
            $good->category = $request->input('category');
            $good->quantity = $request->input('quantity');
            $good->seller_id = Auth::user()->id;
            $good->seller_name = $user->name;
            $good->countryName = $user->countryName;
            $good->cityName = $user->cityName;

            $good->save();

            return response()->json($good, 201);
        }
    }

    public function show($id, Request $request)
    {
        $good = Good::find($id);
        $user = $request->user();

        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = '';

        $location = \Location::get($ipaddress);

        $relatedGoods = Good::orderBy('goods.updated_at', 'desc')
        ->where('goods.category', $good->category)
        ->where('goods.id', '!=', $good->id)
        ->paginate(5);

        $recentViewedGoods = viewGoods::where('view_goods.ip', $location->ip)
        ->where('view_goods.goodId', '!=', $good->id)
        ->distinct()
        ->take(-5)
        ->get(['goodId', 'goodName', 'ip']);
        // ->get(['goodId', 'goodName', 'goodImage', 'goodDiscount', 'updated_at'])
        // ->paginate(5, ['goodId', 'goodName', 'ip', 'created_at']);

        $seller = Seller::find($good->seller_id);

        // if(Auth::user()->id != null){
            // $user = Auth::user() || null;
        // }

        // $goods = Good::all();

        // $reviews = Review::orderBy('reviews.updated_at', 'desc')
        // ->where('reviews.good_id', $good->id)
        // ->paginate(20);

        Good::where('id', '=', $id)
        ->update([
            // Increment the view counter field
            'views' =>
            $good->views + 1        ,
            // Prevent the updated_at column from being refreshed every time there is a new view
            'updated_at' => \DB::raw('updated_at')
        ]);



        // $location = \Location::get($ipaddress);

        $agent = new Agent();
        $device = $agent->device();
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $languages = $agent->languages();
        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);
        $ifRobot = $agent->isRobot();
        $robot = $agent->robot();

        // $viewGood = viewGoods::where('view_goods.goodId', $good->id)->first();
        // if($viewGood != null){

        // }

        $category = Category::where('sub_category', $good->category)->first();

        $viewGood = new viewGoods;
        if($user != null){
            $viewGood->userId = $user->id;
            $viewGood->userName = $user->name;
        }
        $viewGood->sellerId = $seller->id;
        $viewGood->sellerName = $seller->name;
        $viewGood->goodId = $good->id;
        $viewGood->goodName = $good->name;
        $viewGood->goodPrice = $good->price;
        $viewGood->goodImage = $good->image;
        $viewGood->goodViews = $good->views;
        $viewGood->goodCategory = $good->category;
        $viewGood->goodCategoryId = $category->id;
        $viewGood->goodDiscount = $good->discount;
        $viewGood->cityName = $location->cityName;
        $viewGood->countryCode = $location->countryCode;
        $viewGood->countryName = $location->countryName;
        $viewGood->ip = $location->ip;
        $viewGood->device = $device;
        $viewGood->browser = $browser;
        $viewGood->browserVersion = $browserVersion;
        $viewGood->languages = json_encode($languages);
        $viewGood->platform = $platform;
        $viewGood->platformVersion = $platformVersion;
        if($ifRobot){
            $viewGood->robot = $robot;
        }

        $viewGood->save();

        $good_data = [
            // 'viewGood' => $viewGood,
            // 'user' => $user,
            'good' => $good,
            'seller' => $seller,
            // 'reviews' => $reviews,
            'location' => $location,
            'device' => $device,
            'browser' => $browser,
            'browserVersion' => $browserVersion,
            'languages' => $languages,
            'platform' => $platform,
            'platformVersion' => $platformVersion,
            'ifRobot' => $ifRobot,
            'robot' => $robot,
            'relatedGoods' => $relatedGoods,
            'recentViewedGoods' => $recentViewedGoods,
        ];

        return response()->json($good_data, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Seller::find(Auth::user()->id);
        $good = Good::find($id);

        $this->validate($request, ['name' => 'required']);

        if($request->hasFile('image')){

            foreach ($request->file('image') as $sinfile){
                $filenameWithExt = $sinfile->getClientOriginalName();
                $sinfile->move(public_path().'/file/', $filenameWithExt);
                $data[] = $filenameWithExt;
                $extension = $sinfile->getClientOriginalExtension();
            }

            //update good

            $good->name = $request->input('name');
            $good->description = $request->input('description');
            $good->originalPrice = $request->input('originalPrice') - 0.01;
            if($request->input('discount')){
                $good->discount = $request->input('discount');
                $good->price = $good->originalPrice - ($good->discount * 0.01 * $good->originalPrice);
            }else{
                $good->price = $good->originalPrice;
            }
            $good->category = $request->input('category');
            $good->quantity = $request->input('quantity');
            $good->seller_id = Auth::user()->id;
            $good->seller_name = $user->name;
            $good->countryName = $user->countryName;
            $good->cityName = $user->cityName;
            $good->image = json_encode($data);

            $good->save();

            return response()->json($good, 201);
        }else{
            $filenameToStore = 'NoFile';

            //update good

            $good->name = $request->input('name');
            $good->description = $request->input('description');
            $good->originalPrice = $request->input('originalPrice');
            if($request->input('discount')){
                $good->discount = $request->input('discount');
                $good->price = $good->originalPrice - ($good->discount * 0.01 * $good->originalPrice);
            }else{
                $good->price = $good->originalPrice;
            }
            $good->category = $request->input('category');
            $good->quantity = $request->input('quantity');
            $good->seller_id = Auth::user()->id;
            $good->seller_name = $user->name;
            $good->countryName = $user->countryName;
            $good->cityName = $user->cityName;

            $good->save();

            return response()->json($good, 201);
        }

    }

    public function destroy($id)
    {
        $good = Good::find($id);

        if(Auth::user()->id === $good->seller_id){
            // Storage::delete('public/files/documents/'.$good->file);
            // Storage::delete('public/files/images/'.$good->image);
            $good->delete();

            return response()->json($good, 201);
        }
    }
}
