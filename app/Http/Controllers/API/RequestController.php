<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\LeavePermission;
use App\Models\Attendance;
use App\Models\VacationRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Validator;

class RequestController extends ApiController
{
    public function emergency_vacation(){
         $response['taken']= Attendance::where('user_id',auth()->user()->id)->where('status','emergency_vacation')->whereYear('date', date('Y'))->count();
         $response['total']=7;
         $response['Credit']=$response['total']-$response['taken'];
         return $this->sendResponse($response,null,200);
    }
    public function ordinary_vacation  (){
        $response['taken']= Attendance::where('user_id',auth()->user()->id)->where('status','ordinary_vacation')->whereYear('date', date('Y'))->count();
        $response['total']=14;
        $response['Credit']=$response['total']-$response['taken'];
        return $this->sendResponse($response,null,200);
    }

    public function sick_vacation_request(Request $request){
        $validator  =   Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'file' => 'required|file|mimes:pdf,doc,docx,jpeg,jpg,png,gif|max:3072'
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }
        $request_vacation = VacationRequest::orderBy('id', 'desc')->first();

        if ($request_vacation) {
            $lastCode = $request_vacation->code;
            $code = 'REQ-' . str_pad((int) substr($lastCode, 4) + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $code = 'REQ-000001';
        }
        $request_vacation=VacationRequest::create(['user_id'=>auth()->user()->id,
                                                    'code'=>$code,
                                                    'from'=>date('Y-m-d',strtotime($request->date_from)),
                                                    'to'=>date('Y-m-d',strtotime($request->date_to)),
                                                    'type'=>'sick_vacation',
                                                    'status'=>'accepted',
                                                    'hr_approval'=>'accepted',
                                                    'Manager_approval'=>'accepted']);
        if($request->file('file')){
            
                uploadMedia($request->file,$request_vacation->AttachmentCollection,$request_vacation);
            
        }
        $start = new DateTime($request->date_from);
        $end = new DateTime($request->date_to);
        $end->modify('+1 day');
        while ($start < $end) {
            $attendance=Attendance::where('user_id',auth()->user()->id)->where('date',$start->format('Y-m-d'))->first();
            if(!$attendance){
                $attendance=Attendance::create(['user_id'=>auth()->user()->id,
                                    'date'=>$start->format('Y-m-d'),
                                    'status'=>'sick_vacation'
                                    ]);
            }
            if ($start->format('l') == 'Friday' || $start->format('l') == 'Saturday') {
                $attendance->status = 'vacation';
                $attendance->save();
            }elseif(!in_array($attendance->status,['ordinary_vacation','emergency_vacation','vacation'])){
                $attendance->status = 'sick_vacation';
                $attendance->save();
            }
           
            $start->modify('+1 day');
        }
        return $this->sendResponse(null,'Vacation Request Created Successfully',200);

    }

    public function emergency_vacation_request(Request $request){
       
        $validator  =   Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'message' => 'nullable'
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }
        $taken= Attendance::where('user_id',auth()->user()->id)->where('status','emergency_vacation')->whereYear('created_at', date('Y'))->count();
        $total=7;
        $Credit=$total-$taken;
        $start = new DateTime($request->date_from);
        $end = new DateTime($request->date_to);
        $end->modify('+1 day'); // include end date in the range

        $totalDays = 0;

        // Loop through each day in the range
        while ($start < $end) {
            // Check if the day is not Friday (5) or Saturday (6)
            if ($start->format('l') != 'Friday' && $start->format('l') != 'Saturday') {
                $totalDays++;
            }
            $start->modify('+1 day');
        }
         if($totalDays > $Credit){
            if($Credit==0)
                return $this->sendError(null,'Sorry, You do not have any casual leave balance',400);
            return $this->sendError(null,'Sorry, You only have ' . $Credit . ' days of casual leave',400);
            
         }
        $request_vacation = VacationRequest::orderBy('id', 'desc')->first();

        if ($request_vacation) {
            $lastCode = $request_vacation->code;
            $code = 'REQ-' . str_pad((int) substr($lastCode, 4) + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $code = 'REQ-000001';
        }
        $request_vacation=VacationRequest::create(['user_id'=>auth()->user()->id,
                                                    'code'=>$code,
                                                    'from'=>date('Y-m-d',strtotime($request->date_from)),
                                                    'to'=>date('Y-m-d',strtotime($request->date_to)),
                                                    'message'=>$request->message,
                                                    'type'=>'emergency_vacation',
                                                    'status'=>'accepted',
                                                    'hr_approval'=>'accepted',
                                                    'Manager_approval'=>'accepted']);
        $start = new DateTime($request->date_from);
        $end = new DateTime($request->date_to);
        $end->modify('+1 day');
        while ($start < $end) {
            $attendance=Attendance::where('user_id',auth()->user()->id)->where('date',$start->format('Y-m-d'))->first();
            if(!$attendance){
                $attendance=Attendance::create(['user_id'=>auth()->user()->id,
                                    'date'=>$start->format('Y-m-d'),
                                    'status'=>'emergency_vacation'
                                    ]);
            }
            if ($start->format('l') == 'Friday' || $start->format('l') == 'Saturday') {
                $attendance->status = 'vacation';
                $attendance->save();
            }elseif(!in_array($attendance->status,['ordinary_vacation','sick_vacation','vacation'])){
                $attendance->status = 'emergency_vacation';
                $attendance->save();
            }
           
            $start->modify('+1 day');
        }
        return $this->sendResponse(null,'Vacation Request Created Successfully',200);

    }

    public function ordinary_vacation_request(Request $request){
        $validator  =   Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'message' => 'required'
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }
        $taken= Attendance::where('user_id',auth()->user()->id)->where('status','ordinary_vacation')->whereYear('created_at', date('Y'))->count();
        $total=14;
        $Credit=$total-$taken;
        $start = new DateTime($request->date_from);
        $end = new DateTime($request->date_to);
        $end->modify('+1 day'); // include end date in the range

        $totalDays = 0;

        // Loop through each day in the range
        while ($start < $end) {
            // Check if the day is not Friday (5) or Saturday (6)
            if ($start->format('l') != 'Friday' && $start->format('l') != 'Saturday') {
                $totalDays++;
            }
            $start->modify('+1 day');
        }
         if($totalDays > $Credit){
            if($Credit==0)return $this->sendError(null,'Sorry, You do not have regular leave balance',400);
            return $this->sendError(null,'Sorry, You only have ' . $Credit . ' days of regular leave',400);
            
         }
         $request_vacation = VacationRequest::orderBy('id', 'desc')->first();

        if ($request_vacation) {
            $lastCode = $request_vacation->code;
            $code = 'REQ-' . str_pad((int) substr($lastCode, 4) + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $code = 'REQ-000001';
        }
        $request_vacation=VacationRequest::create(['user_id'=>auth()->user()->id,
                                                    'code'=>$code,
                                                    'from'=>date('Y-m-d',strtotime($request->date_from)),
                                                    'to'=>date('Y-m-d',strtotime($request->date_to)),
                                                    'message'=>$request->message,
                                                    'type'=>'ordinary_vacation'
                                                   ]);
        return $this->sendResponse(null,'Vacation Request Created Successfully',200);

    }

    public function leave_permission_request(Request $request){
        // $current_time = Carbon::now();
        // $cutoff_time = Carbon::createFromTime(18, 0, 0); // 6:00 PM

        // if ($current_time->greaterThanOrEqualTo($cutoff_time)) {
        //     return $this->sendError(null, 'Requests cannot be made after 6:00 pm', 400);
        // }
        $validator  =   Validator::make($request->all(), [
              
            'time_from' => ['required', 'date_format:h:i a','after_or_equal:09:00 am'],
            'time_to' => ['required', 'date_format:h:i a', 'after:time_from','before_or_equal:06:00 pm'],
            'message' => 'nullable|string',
            'date' => 'required'
            
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }
        
        // $attendance=Attendance::where('user_id',auth()->user()->id)->where('date',date('Y-m-d'))->where('status','attendance')->where('check_out',null)->first();
        // if(!$attendance){
        //     return $this->sendError(null,"You can't do Leave permission request now",400);
        // }
        $request_leave = LeavePermission::orderBy('id', 'desc')->first();

        if ($request_leave) {
            $lastCode = $request_leave->code;
            $code = 'PER-' . str_pad((int) substr($lastCode, 4) + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $code = 'PER-000001';
        }
        $leave_permission=LeavePermission::create(['user_id'=>auth()->user()->id,
                                                    'code'=>$code,
                                                    'from'=> date("H:i", strtotime($request->time_from)),
                                                    'to'=>date("H:i", strtotime($request->time_to)),
                                                    'message'=>$request->message,
                                                    'date'=>date('Y-m-d',strtotime($request->date))
                                                  ]);
        return $this->sendResponse(null,'Leave Permission Created Successfully',200);
    }


    public function all_requests(){
        $all_requests=VacationRequest::where('user_id',auth()->user()->id)->orderBy('id', 'desc')->get();
        return $this->sendResponse($all_requests,null,200);

    }

    public function all_permissions(){
        $all_permissions=LeavePermission::where('user_id',auth()->user()->id)->orderBy('id', 'desc')->get();
        return $this->sendResponse($all_permissions,null,200);
    }

    public function get_one_request(Request $request){
        $validator  =   Validator::make($request->all(), [
            'request_id' => 'required|integer|exists:requests,id'
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }
        $VacationRequest=VacationRequest::where('id',$request->request_id)->first();
        return $this->sendResponse($VacationRequest,null,200);

    }
    public function get_one_permission (Request $request){
        $validator  =   Validator::make($request->all(), [
            'permission_id' => 'required|integer|exists:leave_permissions,id'
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }
        $leave_permission=LeavePermission::where('id',$request->permission_id)->first();
        return $this->sendResponse($leave_permission,null,200);
    }
};