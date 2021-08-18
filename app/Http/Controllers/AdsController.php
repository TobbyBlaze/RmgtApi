<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Ads;
use App\viewAds;
use App\User;
use App\Notifications\NewCart;
use App\Notifications\NewReview;
use Auth;
use DB;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Agent;

class AdsController extends Controller
{
    public function index(Request $request)
    {
        // $user = $request->user();

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

        $city = $request->input('city');
        $state = $request->input('state');
        $sort = $request->input('sort');
        // $brand = $request->input('brand');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');
        // $minRating = $request->input('minRating');
        // $maxRating = $request->input('maxRating');

        if($minPrice == null){
            $minPrice = 0;
        }
        if($maxPrice == null){
            $maxPrice = 999999;
        }
        // if($minRating == null){
        //     $minRating = 0;
        // }
        // if($maxRating == null){
        //     $maxRating = 5;
        // }

        if($state == null){
            $state = $location->cityName;
        }
        // if($city == null){
        //     $city = $location->cityName;
        // }

        switch($sort){
            case 'ph2l': {
                $good_ads = Ads::orderBy('ads.price', 'desc')
                ->where([
                    ['ads.price', '>=', $minPrice],
                    ['ads.price', '<=', $maxPrice],
                    // ['ads.rating', '>=', $minRating],
                    // ['ads.rating', '<=', $maxRating],
                    ['ads.stateName', '=', $state],
                    ['ads.cityName', '=', $city]])
                ->paginate(1);
                break;
            }
            case 'pl2h': {
                $good_ads = Ads::orderBy('ads.price', 'asc')
                ->where([
                    ['ads.price', '>=', $minPrice],
                    ['ads.price', '<=', $maxPrice],
                    // ['ads.rating', '>=', $minRating],
                    // ['ads.rating', '<=', $maxRating],
                    ['ads.stateName', '=', $state],
                    ['ads.cityName', '=', $city]])
                ->paginate(1);
                break;
            }
            case 'rating': {
                $good_ads = Ads::orderBy('ads.rating', 'desc')
                ->where([
                    ['ads.price', '>=', $minPrice],
                    ['ads.price', '<=', $maxPrice],
                    // ['ads.rating', '>=', $minRating],
                    // ['ads.rating', '<=', $maxRating],
                    ['ads.stateName', '=', $state],
                    ['ads.cityName', '=', $city]])
                ->paginate(1);
                break;
            }
            default: {
                $good_ads = Ads::orderBy('ads.updated_at', 'desc')
                ->where([
                    ['ads.price', '>=', $minPrice],
                    ['ads.price', '<=', $maxPrice],
                    // ['ads.rating', '>=', $minRating],
                    // ['ads.rating', '<=', $maxRating],
                    ['ads.stateName', '=', $state],
                    ['ads.cityName', '=', $city]])
                ->paginate(1);
            }

        }

        // $good_ads = Ads::orderBy('ads.updated_at', 'desc')
        // ->paginate(20);

        // $users = User::get();

        $data = [
            'location' => $location,
            'good_ads'=>$good_ads,
            // 'users'=>$users,
        ];

        return response()->json($data,200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $messages = [
            "attachments.max" => "file can't be more than 2."
         ];

        $this->validate($request, [
            'name' => 'required',
            'file.*' => 'mimes:jpg,jpeg,bmp,png,gif|max:20000',
            'file' => 'max:2',
        ], $messages);

        if($request->hasFile('image')){
            
            foreach ($request->file('image') as $sinfile){
                $filenameWithExt = $sinfile->getClientOriginalName();
                $sinfile->move(public_path().'/file/', $filenameWithExt);
                $data[] = $filenameWithExt;
                $extension = $sinfile->getClientOriginalExtension();
            }

            //create ads

            $ad = new Ads;
            $ad->name = $request->input('name');
            $ad->description = $request->input('description');
            $ad->price = $request->input('price');
            $ad->category = $request->input('category');
            $ad->condition = $request->input('condition');
            $ad->seller_id = $user->id;
            $ad->seller_name = $user->name;
            $ad->phone = $request->input('phone'); //$user->phone;
            $ad->address = $request->input('address'); //$user->address;
            $ad->countryName = $request->input('countryName'); //$user->countryName;
            $ad->stateName = $request->input('stateName'); //$user->stateName;
            $ad->cityName = $request->input('cityName'); //$user->cityName;
            $ad->image = json_encode($data);

            $ad->save();

            return response()->json($ad, 201);
        }else{
            $filenameToStore = 'NoFile';

            //create ads

            $ad = new Ads;
            $ad->name = $request->input('name');
            $ad->description = $request->input('description');
            $ad->price = $request->input('price');
            $ad->category = $request->input('category');
            $ad->condition = $request->input('condition');
            $ad->seller_id = $user->id;
            $ad->seller_name = $user->name;
            $ad->phone = $request->input('phone'); //$user->phone;
            $ad->address = $request->input('address'); //$user->address;
            $ad->countryName = $request->input('countryName'); //$user->countryName;
            $ad->stateName = $request->input('stateName'); //$user->stateName;
            $ad->cityName = $request->input('cityName'); //$user->cityName;
            
            $ad->save();

            return response()->json($ad, 201);
        }

    }

    public function show($id, Request $request)
    {
        $ad = Ads::find($id);

        $user = $request->user();

        // $ads = Ads::all();

        // $reviews = Review::orderBy('reviews.updated_at', 'desc')
        // ->paginate(20);

        $relatedAds = Ads::orderBy('ads.updated_at', 'desc')
        ->where('ads.category', $ad->category)
        ->where('ads.id', '!=', $ad->id)
        ->paginate(5);

        $recentViewedAds = viewAds::where('view_ads.ip', $location->ip)
        ->where('view_ads.goodId', '!=', $ad->id)
        ->distinct()
        ->take(-5)
        ->get(['adId', 'adName', 'ip']);

        Ads::where('id', '=', $id)
        ->update([
            // Increment the view counter field
            'views' => 
            $ad->views + 1        ,
            // Prevent the updated_at column from being refreshed every time there is a new view
            'updated_at' => \DB::raw('updated_at')   
        ]);

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
            $ipaddress = request()->ip();

        $location = \Location::get($ipaddress);

        $agent = new Agent();
        $device = $agent->device();
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $languages = $agent->languages();
        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);
        $ifRobot = $agent->isRobot();
        $robot = $agent->robot();

        $viewAd = new viewAds;
        if($user != null){
            $viewAd->userId = $user->id;
            $viewAd->userName = $user->name;
        }
        // $viewAd->userId = $user->id;
        // $viewAd->userName = $user->name;
        $viewAd->sellerId = $ad->seller_id;
        $viewAd->sellerName = $ad->name;
        $viewAd->adId = $ad->id;
        $viewAd->adName = $ad->name;
        $viewAd->adViews = $ad->views;
        $viewAd->cityName = $location->cityName;
        $viewAd->countryCode = $location->countryCode;
        $viewAd->countryName = $location->countryName;
        $viewAd->ip = $location->ip;
        $viewAd->device = $device;
        $viewAd->browser = $browser;
        $viewAd->browserVersion = $browserVersion;
        $viewAd->languages = json_encode($languages);
        $viewAd->platform = $platform;
        $viewAd->platformVersion = $platformVersion;
        if($ifRobot){
            $viewAd->robot = $robot;
        }

        $viewAd->save();

        $ads_data = [
            'ad' => $ad,
            'ads' => $ads,
            'user' => '$user',
            // 'users' => $users,
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
            'relatedAds' => $relatedAds,
            'recentViewedAds' => $recentViewedAds,
        ];

        return response()->json($ads_data);
    }

    public function update(Request $request, $id)
    {
        $user = User::find(auth::user()->id);
        $ad = Ads::find($id);

        // $good->update($request->all());
        // return response()->json($good, 200);

        $this->validate($request, ['name' => 'required']);
        //return 123;

        if($request->hasFile('image')){
            
            foreach ($request->file('image') as $sinfile){
                $filenameWithExt = $sinfile->getClientOriginalName();
                $sinfile->move(public_path().'/file/', $filenameWithExt);
                $data[] = $filenameWithExt;
                $extension = $sinfile->getClientOriginalExtension();
            }

            //update ads

            $ad->name = $request->input('name');
            $ad->description = $request->input('description');
            $ad->price = $request->input('price');
            $ad->category = $request->input('category');
            $ad->quantity = $request->input('quantity');
            $ad->seller_id = Auth::user()->id;
            $ad->image = json_encode($data);

            $ad->save();

            return response()->json($ad, 201);
        }else{
            $filenameToStore = 'NoFile';

            //update ads

            $ad->name = $request->input('name');
            $ad->description = $request->input('description');
            $ad->price = $request->input('price');
            $ad->category = $request->input('category');
            $ad->quantity = $request->input('quantity');
            $ad->seller_id = Auth::user()->id;
            
            $ad->save();

            return response()->json($ad, 201);
        }

    }

    public function destroy($id)
    {
        $ad = Ads::find($id);
        
        if(Auth::user()->id === $ad->seller_id){
            // Storage::delete('public/files/documents/'.$ad->file);
            // Storage::delete('public/files/images/'.$ad->image);
            $ad->delete();

            return response()->json($ad, 201);
        }
    }
}
