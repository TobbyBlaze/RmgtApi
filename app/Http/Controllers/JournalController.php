<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Models\Journal;
use App\Models\User;
use App\Models\Admin;
use Auth;
use DB;

// use Illuminate\Support\Facades\Crypt;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $user = User::find(Auth::user()->id);
        // if($user->email == 'muritala.mt@gmail.com'){
        //     $user = Admin::find(Auth::user()->id);
        // }
        // $user = $request->user();
        // $user_id = Auth::user()->id;
        // $user_name = Auth::user()->name;

        $journals = Journal::orderBy('journals.updated_at', 'desc')
        ->where('journals.user_id', $user->id)
        ->paginate(10);

        $data = [
            'user'=>$user,
            // 'user1'=>$user1,
            // 'user_id'=>$user_id,
            // 'user_name'=>$user_name,
            'journals'=>$journals,
        ];

        return response()->json($data,200);
    }

    public function store(Request $request)
    {
        $user = User::find(Auth::user()->id);

        $this->validate($request, [
            'journal' => 'required',
        ]);

            $filenameToStore = 'NoFile';

            //create journal

            // $encrypted = Crypt::encryptString('Hello world.');
            return response()->json([$request->input('journal'), $user], 201);

            $journal = new Journal;
            $journal->journal = $request->input('journal');
            $journal->user_id = $user->id;
            $journal->user_name = $user->name;

            $journal->save();

            return response()->json($journal, 201);
    }

    public function show($id, Request $request)
    {
        $journal = Journal::find($id);
        $user = $request->user();

        Journal::where('id', '=', $id)
        ->update([
            // Increment the view counter field
            'views' =>
            $journal->views + 1        ,
            // Prevent the updated_at column from being refreshed every time there is a new view
            'updated_at' => \DB::raw('updated_at')
        ]);

        $journal_data = [

            'user' => $user,
            'journal' => $journal,

        ];

        return response()->json($journal_data, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find(Auth::user()->id);
        $journal = Journal::find($id);

        $this->validate($request, ['journal' => 'required']);

            //update journal

            $journal->journal = $request->input('journal');
            $journal->user_id = Auth::user()->id;
            $journal->user_name = $user->name;

            $journal->save();

            return response()->json($journal, 201);

    }

    public function destroy($id)
    {
        $journal = Journal::find($id);

        if(Auth::user()->id === $journal->user_id){
            // Storage::delete('public/files/documents/'.$journal->file);
            // Storage::delete('public/files/images/'.$journal->image);
            $journal->delete();

            return response()->json($journal, 201);
        }
    }
}
