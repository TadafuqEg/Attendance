<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use App\Mail\SendContactUs;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $firebaseService;
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
///////////////////////////////////////////  Login  ///////////////////////////////////////////
    public function login_view(){
        return view('dashboard.login');
    }

    public function login(Request $request)
    {   
        $validator  =   Validator::make($request->all(), [
               
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string', 'min:8'],
               
        ]);
            // dd($request->all());
        if ($validator->fails()) {
           
            return Redirect::back()->withErrors($validator)->withInput($request->all());
        }
        if (Auth::attempt(['email' => request('email'),'password' => request('password')])){

            return redirect('/admin-dashboard/home');
        }else{

            return back()->withErrors(['msg' => 'There is something wrong']);
        }
       
    }


///////////////////////////////////////////  Logout  ///////////////////////////////////////////

    public function logout(){
        Auth::logout();
       
       // auth()->guard('admin')->logout();
        return redirect('/admin-dashboard/login');
    }

    public function home(){
        $emp_count=User::whereHas('roles', function ($q) {
            $q->where('name', 'Client');
        })
        ->count();
        $attendance=Attendance::where('status','attendance')->where('date',date('Y-m-d'))->count();
        $absence=Attendance::where('status','absence')->where('date',date('Y-m-d'))->count();
        $vacation=Attendance::where('status','vacation')->where('date',date('Y-m-d'))->count();
        $sick_vacation=Attendance::where('status','sick_vacation')->where('date',date('Y-m-d'))->count();
        $ordinary_vacation=Attendance::where('status','ordinary_vacation')->where('date',date('Y-m-d'))->count();
        $emergency_vacation=Attendance::where('status','emergency_vacation')->where('date',date('Y-m-d'))->count();

        $users=User::whereHas('roles', function ($q) {
            $q->where('name', 'Client');
        })
        ->get();
        $names_array=[];
        $attendance_array=[];
        $absence_array=[];
        $vacation_array=[];
        $currentMonth = now()->month;

        // Get the current year
        $currentYear = now()->year;
        foreach($users as $user){
           $names_array[]=$user->name;
           $attendance_array[]=Attendance::where('user_id',$user->id)->whereDate('date', '<=', now())->whereMonth('created_at', $currentMonth)
           ->whereYear('created_at', $currentYear)->where('status','attendance')->count();
           $absence_array[]=Attendance::where('user_id',$user->id)->whereDate('date', '<=', now())->whereMonth('created_at', $currentMonth)
           ->whereYear('created_at', $currentYear)->where('status','absence')->count();
           $vacation_array[]=Attendance::where('user_id',$user->id)->whereDate('date', '<=', now())->whereMonth('created_at', $currentMonth)
           ->whereYear('created_at', $currentYear)->whereIn('status',['emergency_vacation','ordinary_vacation','sick_vacation','vacation'])->count();
        }
        return view('dashboard.home',compact('emp_count',
                                             'attendance',
                                             'vacation',
                                             'absence',
                                             'sick_vacation',
                                             'emergency_vacation',
                                             'ordinary_vacation',
                                             'names_array','attendance_array','absence_array','vacation_array'));
    }
    public function change_theme(Request $request){
        $user=auth()->user();
        $user->theme=$request->theme;
        $user->save();
        return $this->sendResponse(null,'success');


    }

    public function privacy_policy(){
        return view('privacy_policy');
    }

    public function remove_account(){
        return view('dashboard.remove_account');

    }

    public function contact_us(){
        
        $this->firebaseService->sendNotification('fCwn2Zb9QeGnzc7NhGY-lC:APA91bHZUOmlRA_as3b73YkfldzCnenUl3oaoJcO5Xah8Bimyp5M-LE51sByyQFT9RFsDR3BfG2qPBGCQo0xfYN3PPZ_zxB9YUVHJHRuXDybP6e3naiUpT8','hello message','hello Fathy, how are you?',[]);
        return view('contact_us');
    }

    public function post_contact_us(Request $request){
          //dd($request->all());
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);
        $name=$validated['first_name'] . ' ' . $validated['last_name'];
        Mail::to("support@tadafuq.ae")->send(new SendContactUs($name, $validated['email'], $validated['message']));
        return response()->json(['message' => 'message sent successfully!'], 200);
    }
 
}