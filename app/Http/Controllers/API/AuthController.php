<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\User;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
      
        $validator  =   Validator::make($request->all(), [
            
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }
        $success_login = false;
        
        $user = User::where('email', $request->email)->first();
        // ->whereHas('roles', function ($q) {
        //     $q->where('name', 'Client');
        // })
        if ($user && Hash::check($request->password, $user->password)) {
            $success_login = true;
            if($request->device_token){
                $user->device_token=$request->device_token;
                $user->save();
            }
        }
        if($user && $user->first_login=='0'){
            $first_login=$user->first_login;
            $user->first_login='1';
            $user->save();
            $user->first_login=$first_login;
        }

        if($success_login){

            $user->token=$user->createToken('api')->plainTextToken;
            $user->picture=getFirstMediaUrl($user,$user->avatarCollection);
           

        }else{
            return $this->sendError(null,"Please verify that your information is correct",400);
           
        }
        
        return $this->sendResponse($user,null,200);
    }

    public function logout(Request $request){
        $user = $request->user();
        $currentToken = $user->currentAccessToken();
        // Revoke the token of the current device
       
      
        $currentToken->delete();
       
        return $this->sendResponse(null,'logout successfuly',200);
        
    }
    public function device_tocken(Request $request){
        $validator  =   Validator::make($request->all(), [
            'device_token'=>'required'
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }

        $user=auth()->user();
        $user->device_token=$request->device_token;
        $user->save();
        return $this->sendResponse(null,'FCM-Tocken saved successfully.',200);

    }
    public function update_password(Request $request){
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);
    
        if ($validator->fails()) {
            
            return $this->sendError(null,$validator->errors(),401);

        }
    
        // Check if the old password matches the user's current password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return $this->sendError(null,'The old password is incorrect.',400);

        }
    
        // Update the user's password
        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();
    
        return $this->sendResponse(null,'Password updated successfully.',200);

    }

    public function profile(){
        $user=auth()->user();
        $user->image=getFirstMediaUrl($user,$user->avatarCollection);
        return $this->sendResponse($user,null,200);
    }

    public function update_profile(Request $request){
        $user=auth()->user();
        if($request->file('image')){
            $image=getFirstMediaUrl($user,$user->avatarCollection);
            if($image!= null){
                deleteMedia($user,$user->avatarCollection);
                uploadMedia($request->image,$user->avatarCollection,$user);
            }else{
                uploadMedia($request->image,$user->avatarCollection,$user);
            }
        }
       
        $user=auth()->user();
        $user->image=getFirstMediaUrl($user,$user->avatarCollection);
        return $this->sendResponse($user,'profile updated successfully.',200);
    }

    public function user_notification(){
        $notifications=Notification::where('user_id',auth()->user()->id)->orderBy('id', 'desc')->get()->map(function($notification){
              $notification->data=json_decode($notification->data);
              return $notification;
        });
        return $this->sendResponse($notifications,null,200);
    }

    public function seen_notification(Request $request){
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
        ]);
    
        if ($validator->fails()) {
            
            return $this->sendError(null,$validator->errors(),401);

        }
        $notification=Notification::findOrFail($request->notification_id);
        $notification->seen='1';
        $notification->save();
        return $this->sendResponse(null,'Notification seen successfully',200);
    }
}