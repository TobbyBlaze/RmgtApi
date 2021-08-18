<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Good;
use App\Cart;
// use App\Order;
use App\User;
use App\Seller;
use App\Notifications\NewReview;
use App\Notifications\NewCart;
use Auth;
use DB;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if($user){
            $profile_data = [
                'user' => $user
            ];
            
            return response()->json($profile_data, 201);
        }
    }

    public function updateUser(Request $request)
    {

        // $user = $request->user();
        $user = Auth::user();
        // $password = $user->password;

        $user->name = $request->input('name');
        $user->last_name = $request->input('last_name');
        // $user->email = $request->input('email');
        $user->city = $request->input('city');
        $user->country = $request->input('country');
        $user->street = $request->input('street');
        $user->zip = $request->input('zip');
        $user->phone1 = $request->input('phone1');
        $user->phone2 = $request->input('phone2');
        $user->address1 = $request->input('address1');
        $user->address2 = $request->input('address2');
        // $user->password = bcrypt($request->input('password'));

        $user->save();
        return response()->json($user, 201);
        
        
    }

    public function updateUserPassword(Request $request)
    {

        // $user = $request->user();
        $user = Auth::user();
        $password = $user->password;

        $old_password = $request->input('old_password');
        $user->password = bcrypt($request->input('password'));

        if(Hash::check($old_password, $password)){
            $user->save();
            return response()->json($user, 201);
        }else{
            $error = 'User password could not be updated';
            return response()->json($error, 201);
        }
      
    }

    public function updateSeller(Request $request)
    {

        // $user = Seller::find(Auth::user()->id);
        $user = $request->user();

        $this->validate($request, [
            'file.*' => 'mimes:jpg,jpeg,png,gif',
            'file' => 'max:1',
        ]);

        if($request->hasFile('file')){
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->getClientOriginalExtension();
            $filenameToStore = $filename.'_'.time().'.'.$extension;
            //$path = $request->file('file')->storeAs('public/files/documents', $filenameToStore);
            
            if($extension == "jpg" || $extension == "jpeg" || $extension == "png" || $extension == "gif"){
                $path = $request->file('file')->storeAs('public/users-avatar', $filenameToStore);
            }else{
                // dd($extension);
            }

            //update user

            $user->name = $request->input('name');
            // $user->title = $request->input('title');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            // $user->status = $request->input('status');
            $user->bio = $request->input('bio');
            // $user->email = $request->input('email');
            $user->phone_number_1 = $request->input('phone_number_1');
            $user->phone_number_2 = $request->input('phone_number_2');
            $user->date_of_birth = $request->input('date_of_birth');
            $user->department = $request->input('department');
            $user->school = $request->input('school');
            $user->college = $request->input('college');

            //$post->document = $filenameToStore;

            //$extension = $request->file('file')->getClientOriginalExtension();
            
            if($extension == "jpg" || $extension == "jpeg" || $extension == "png" || $extension == "gif"){
                $user->avatar = $filenameToStore;
            }else{

            }
            
            $user->save();

            return response()->json($user, 201);
            
            
        }else{
            $filenameToStore = 'NoFile';

            //update user

            $user->name = $request->input('name');
            // $user->title = $request->input('title');
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            // $user->status = $request->input('status');
            $user->bio = $request->input('bio');
            // $user->email = $request->input('email');
            $user->phone_number_1 = $request->input('phone_number_1');
            $user->phone_number_2 = $request->input('phone_number_2');
            $user->date_of_birth = $request->input('date_of_birth');
            $user->department = $request->input('department');
            $user->school = $request->input('school');
            $user->college = $request->input('college');
            
            $user->save();

            return response()->json($user, 201);
        }

    }
}
