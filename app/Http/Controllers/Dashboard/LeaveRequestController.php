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
use App\Models\Notification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\FirebaseService;
use DateTime;
use App\Models\Attendance;
use App\Models\VacationRequest;
use Image;
use Str;
use App\Exports\AttendanceExport;
use File;
use Maatwebsite\Excel\Facades\Excel;
class LeaveRequestController extends Controller
{//done
    protected $firebaseService;
      public function __construct(FirebaseService $firebaseService)
      {
          $this->firebaseService = $firebaseService;
      }
    public function index(Request $request)
    {
        // if($request->has('export') && $request->export=='1'){
        //     $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_'), 0, 12);
        //     return Excel::download(new AttendanceExport($request), auth()->user()->id . $invitation_code . 'attencance.xlsx');
        //     //$contact_us=ContactUs::all();
        // }else{
            
        
            $all_requests = VacationRequest::orderBy('id', 'desc');

            if ($request->has('status')&& $request->status!=null) {
                $all_requests->where('status', $request->status);
            }
            if ($request->has('type')&& $request->type!=null) {
                $all_requests->where('type', $request->type);
            }
        
        
         
            if($request->has('user')&& $request->user!=null) {
                $all_requests->where('user_id', $request->user);
            }
            
            $all_requests = $all_requests->paginate(12);

            // $all_attendance->getCollection()->transform(function ($attendance) {
            //     // Add the 'image' key based on some condition
            //     $attendance->user->image = getFirstMediaUrl($attendance->user,$attendance->user->avatarCollection);
            //     return $attendance;
            // });
            $users=User::whereHas('roles', function ($query) {
                $query->where('roles.name', 'Client');
            })->get();
        // }
        $status_search=$request->status;
        $type_search=$request->type;
        $user_search=$request->user;
        return view('dashboard.requests.index',compact('all_requests','users','status_search','type_search','user_search'));

    }


    public function edit($id){
        $VacationRequest=VacationRequest::where('id',$id)->first();
        return view('dashboard.requests.edit',compact('VacationRequest'));
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
        
        VacationRequest::where('id',$id)->update([  'hr_approval' => $request->hr_approval,
                                            'Manager_approval' => $request->Manager_approval,
                                            'rejection_reason'=> $request->rejection_reason
                                           ]);
        $Vacation_Request=VacationRequest::find($id);
        if($Vacation_Request->type!='emergency_vacation' && $Vacation_Request->type!='sick_vacation'){
            if($request->hr_approval=='rejected' || $request->Manager_approval=='rejected'){
                $Vacation_Request->status='rejected';
                $Vacation_Request->save();
                $this->firebaseService->sendNotification($Vacation_Request->user->device_token,'Regular Request',"Unfortunately, your request registered with code “" . $Vacation_Request->code . "” has been rejected",[ "url" => url('/api/get_one_request?request_id=' . $Vacation_Request->id),"screen"=>"Request"]);
                $data=[
                  "title"=>"Regular Request",
                  "message"=>"Unfortunately, your request registered with code “" . $Vacation_Request->code . "” has been rejected",
                  "url" => url('/api/get_one_request?request_id=' . $Vacation_Request->id)
                ];
                  Notification::create(['user_id'=>$Vacation_Request->user_id,'data'=>json_encode($data)]);
               
                $start = new DateTime($Vacation_Request->from);
                $end = new DateTime($Vacation_Request->to);
                $end->modify('+1 day');
                while ($start < $end) {
                    $attendance=Attendance::where('user_id',$Vacation_Request->user_id)->where('date',$start->format('Y-m-d'))->where('status',$Vacation_Request->type)->first();
                    if($attendance){
                        $attendance->delete();
                    }
                    
                    $start->modify('+1 day');
                }
            }elseif($request->hr_approval=='accepted' && $request->Manager_approval=='accepted'){
                $Vacation_Request->status='accepted';
                $Vacation_Request->save();
                $this->firebaseService->sendNotification($Vacation_Request->user->device_token,'Regular Request',"Your request registered with code “" . $Vacation_Request->code . "” has been accepted",[ "url" => url('/api/get_one_request?request_id=' . $Vacation_Request->id),"screen"=>"Request"]);
                $data=[
                  "title"=>"Regular Request",
                  "message"=>"Your request registered with code “" . $Vacation_Request->code . "” has been accepted",
                  "url" => url('/api/get_one_request?request_id=' . $Vacation_Request->id)
                ];
                  Notification::create(['user_id'=>$Vacation_Request->user_id,'data'=>json_encode($data)]);
                $start = new DateTime($Vacation_Request->from);
                $end = new DateTime($Vacation_Request->to);
                $end->modify('+1 day');
                while ($start < $end) {
                    $attendance=Attendance::where('user_id',$Vacation_Request->user_id)->where('date',$start->format('Y-m-d'))->first();
                    if(!$attendance){
                        $attendance=Attendance::create(['user_id'=>$Vacation_Request->user_id,
                                            'date'=>$start->format('Y-m-d'),
                                            'status'=>$Vacation_Request->type
                                            ]);
                    }
                    if ($start->format('l') == 'Friday' || $start->format('l') == 'Saturday') {
                        $attendance->status = 'vacation';
                        $attendance->save();
                    }
                   
                    
                    $start->modify('+1 day');
                }
            }
        }
     
        return redirect('/admin-dashboard/leave_requests');

    }


   

    public function delete($id)
    {
        VacationRequest::where('id', $id)->delete();
        return redirect('/admin-dashboard/leave_requests');
    }
}