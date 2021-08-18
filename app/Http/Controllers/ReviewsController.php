<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Good;
use App\Review;
use App\User;
use App\Sale;
use Auth;
use DB;


class ReviewsController extends Controller
{
    public function index($id)
    {
        // $user = $request->user();
        $good = Good::find($id);
        
        $reviews = Review::orderBy('reviews.updated_at', 'desc')
        ->where('reviews.good_id', $good->id)
        ->paginate(20);

        $data = [
            // 'good' => $good,
            'reviews'=>$reviews,
        ];

        return response()->json($data,200);
    }

    public function store(Request $request, $id)
    {

        // $good = Good::find($id);
        // $user = $request->user();
        $user = User::find(Auth::user()->id);

        // $good = Good::find($review->good_id);
        $good = Good::find($id);

        // $reviews = Review::orderBy('reviews.updated_at', 'desc')
        // ->where('reviews.good_id', $good->id)
        // ->get();

        $goodNum = Review::where([
            ['good_id', '=', $good->id],
            ['rating', '>', 0]])
        ->count();

        $eligible = Sale::where([
        ['sales.user_id', '=', $user->id],
        ['sales.good_id', '=', $good->id]])
        ->count();

        if($eligible > 0){
            $review = new Review;
            $review->rating = $request->input('rating');
            $review->body = $request->input('body');
            $review->user_id = $user->id;
            $review->user_name = $user->name;
            $review->good_id = $request->input('good_id');
            // $review->good_id = $good->id;
            
            $review->save();

            if($review->rating){
                Good::where('id', '=', $review->good_id)
                ->update([
                    'rating' => 
                    (($good->rating * $goodNum) + $review->rating)/($goodNum + 1)     ,
                    // Prevent the updated_at column from being refreshed every time there is a new view
                    'updated_at' => \DB::raw('updated_at')   
                ]);
            }

            return response()->json($review, 201);
        }else{
            $ineligible = 'You are not eligible';
            $review = null;
            $data = [
                'eligible' => $eligible,
                'ineligible' => $ineligible,
                'review'=>$review,
            ];
            return response()->json($data, 201);
        }
        
    }

    public function show($id, Request $request)
    {
        $review = Review::find($id);

        $user = $request->user();

        $reviews = Review::all();

        $review_data = [
            'review' => $review,
            'reviews' => $reviews,
            'user' => $user,
        ];

        return response()->json($review_data);
    }

    public function update(Request $request, $id)
    {
        $review = Review::find($id);
        $review->rating = $request->input('rating');
        $review->body = $request->input('body');
        $review->user_id = auth()->user()->id;
        $review->good_id = $good->id;
        
        $review->save();

        return response()->json($review, 201);
    }

    public function destroy($id)
    {
        $review = Review::find($id);
        
        if(auth()->user()->id === $review->user_id){
            $review->delete();
            return response()->json($review, 201);
        }
    }
}
