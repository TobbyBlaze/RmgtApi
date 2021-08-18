<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\Trade;
use App\Models\User;
use App\Models\Admin;
use Auth;
use DB;

class tradeController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(Auth::user()->id);
        // $user1 = $request->user();
        // $user_id = Auth::user()->id;
        // $user_name = Auth::user()->name;

        $trades = Trade::orderBy('trades.updated_at', 'desc')
        // ->where('trades.user_id', $user->id)
        ->paginate(10);

        $data = [
            'user'=>$user,
            // 'user1'=>$user1,
            // 'user_id'=>$user_id,
            // 'user_name'=>$user_name,
            'jrades'=>$trades,
        ];

        return response()->json($data,200);
    }

    public function store(Request $request)
    {
        $user = Admin::find(Auth::user()->id);

        $this->validate($request, [
            'trade' => 'required',
        ]);

            $filenameToStore = 'NoFile';

            //create trade

            $trade = new Trade;
            $trade->trade = $request->input('trade');
            $trade->user_id = $user->id;
            $trade->user_name = $user->name;

            $trade->save();

            return response()->json($trade, 201);
    }

    public function show($id, Request $request)
    {
        $trade = Trade::find($id);
        $user = $request->user();

        Trade::where('id', '=', $id)
        ->update([
            // Increment the view counter field
            'views' =>
            $trade->views + 1        ,
            // Prevent the updated_at column from being refreshed every time there is a new view
            'updated_at' => \DB::raw('updated_at')
        ]);

        $trade_data = [

            'user' => $user,
            'trade' => $trade,

        ];

        return response()->json($trade_data, 201);
    }

    public function update(Request $request, $id)
    {
        $user = Admin::find(Auth::user()->id);
        $trade = Trade::find($id);

        $this->validate($request, ['trade' => 'required']);

            //update trade

            $trade->trade = $request->input('trade');
            $trade->user_id = Auth::user()->id;
            $trade->user_name = $user->name;

            $trade->save();

            return response()->json($trade, 201);

    }

    public function destroy($id)
    {
        $trade = Trade::find($id);

        if(Auth::user()->id === $trade->user_id){
            // Storage::delete('public/files/documents/'.$trade->file);
            // Storage::delete('public/files/images/'.$trade->image);
            $trade->delete();

            return response()->json($trade, 201);
        }
    }
}
