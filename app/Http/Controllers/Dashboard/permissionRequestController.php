<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use DateTime;
use App\Models\Attendance;
use App\Models\LeavePermission;
use Image;
use Str;
use App\Exports\AttendanceExport;
use File;
use App\Models\Notification;
use App\Services\FirebaseService;
use Maatwebsite\Excel\Facades\Excel;
class permissionRequestController extends Controller
{
    protected $firebaseService;
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    public function index(Request $request)
    {
       
            
        
        $all_requests = LeavePermission::orderBy('id', 'desc');

        if ($request->has('status')&& $request->status!=null) {
            $all_requests->where('status', $request->status);
        }
        
        if($request->has('date_from')&& $request->date_from!=null && $request->has('date_to')&& $request->date_to!=null) {
            $all_requests->where('date','>=', date('Y-m-d',strtotime($request->date_from)))->where('date','<=', date('Y-m-d',strtotime($request->date_to)));
        }elseif($request->has('date_from')&& $request->date_from!=null && $request->date_to==null){
            $all_requests->where('date', date('Y-m-d',strtotime($request->date_from)));
        }
    
        
        if($request->has('user')&& $request->user!=null) {
            $all_requests->where('user_id', $request->user);
        }
        
        $all_requests = $all_requests->paginate(12);

        
        $users=User::whereHas('roles', function ($query) {
            $query->where('roles.name', 'Client');
        })->get();
      
        $status_search=$request->status;
        $date_from_search=$request->date_from;
        $date_to_search=$request->date_to;
        $user_search=$request->user;
        return view('dashboard.permissions.index',compact('all_requests','users','status_search','date_to_search','date_from_search','user_search'));

    }


    public function edit($id){
        $PermissionRequest=LeavePermission::where('id',$id)->first();
        return view('dashboard.permissions.edit',compact('PermissionRequest'));
    }

    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'hr_approval' => ['required'],
            'Manager_approval' => ['required'],
            'rejection_reason' => ['nullable']
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        
        LeavePermission::where('id',$id)->update([  'hr_approval' => $request->hr_approval,
                                            'Manager_approval' => $request->Manager_approval,
                                            'rejection_reason'=> $request->rejection_reason
                                           ]);
       
       $Leave_Permission=LeavePermission::find($id);
      
            if($request->hr_approval=='rejected' || $request->Manager_approval=='rejected'){
                $Leave_Permission->status='rejected';
                $Leave_Permission->save();
                $this->firebaseService->sendNotification($Leave_Permission->user->device_token,'Regular Request',"Unfortunately, your permission request registered with code “" . $Leave_Permission->code . "” has been rejected",[ "url" => url('/api/get_one_permission?permission_id=' . $Leave_Permission->id)]);
                $data=[
                  "title"=>"Regular Request",
                  "message"=>"Unfortunately, your permission request registered with code “" . $Leave_Permission->code . "” has been rejected",
                  "url" => url('/api/get_one_permission?permission_id=' . $Leave_Permission->id)
                ];
                  Notification::create(['user_id'=>$Leave_Permission->user_id,'data'=>json_encode($data)]);
               
            }elseif($request->hr_approval=='accepted' && $request->Manager_approval=='accepted'){
                $Leave_Permission->status='accepted';
                $Leave_Permission->save();
                $this->firebaseService->sendNotification($Leave_Permission->user->device_token,'Regular Request',"Your permission request registered with code “" . $Leave_Permission->code . "” has been accepted",[ "url" => url('/api/get_one_permission?permission_id=' . $Leave_Permission->id)]);
                $data=[
                  "title"=>"Regular Request",
                  "message"=>"Your permission request registered with code “" . $Leave_Permission->code . "” has been accepted",
                  "url" => url('/api/get_one_permission?permission_id=' . $Leave_Permission->id)
                ];
                  Notification::create(['user_id'=>$Leave_Permission->user_id,'data'=>json_encode($data)]);
               
            }
        
     
        return redirect('/admin-dashboard/permission_requests');

    }


   

    public function delete($id)
    {
        LeavePermission::where('id', $id)->delete();
        return redirect('/admin-dashboard/permission_requests');
    }
}