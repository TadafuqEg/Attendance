<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\ApiController;
use App\Models\User;
use App\Models\Attendance;
use App\Models\JoinUs;
use Spatie\Permission\Models\Role;
use DateTime;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends ApiController
{     
      protected $firebaseService;
      public function __construct(FirebaseService $firebaseService)
      {
          $this->firebaseService = $firebaseService;
      }
      public function check_in_out(Request $request){
        $validator  =   Validator::make($request->all(), [
              
            'lat' => 'required',
            'lng' => 'required',
            
        ]);
        // dd($request->all());
        if ($validator->fails()) {

            return $this->sendError(null,$validator->errors(),400);
        }

        $attendance=Attendance::where('user_id',auth()->user()->id)->where('date',date('Y-m-d'))->first();
        if($attendance){
            if($attendance->check_in==null && $attendance->status=='non'){
                $attendance->check_in=date('H:i:s');
                $attendance->status='attendance';
                $attendance->lat=floatval($request->lat);
                $attendance->lng=floatval($request->lng);
                $attendance->save();
            }elseif($attendance->check_out==null && $attendance->status=='attendance'){
              $attendance->check_out=date('H:i:s');
              $attendance->lat=floatval($request->lat);
              $attendance->lng=floatval($request->lng);
              $attendance->save();
            }else{
              return $this->sendResponse(null,"you can't check in or check out",200);
            }
        }else{
          return $this->sendResponse(null,"you can't check in or check out",200);
        }
        return $this->sendResponse(null,'successfuly',200);
      }

      public function home(){
        $user=auth()->user();
        $response['user_name']=$user->name;
        $response['user_image']=getFirstMediaUrl($user,$user->avatarCollection);
        $response['work_start_time']='09:00 AM';
        $response['work_end_time']='06:00 PM';
        $response['work_location']='10 Ibn Hani Al-Andalusi Street, First District, Nasr City, Cairo Governorate,';
        $attendance=Attendance::where('user_id',auth()->user()->id)->where('date',date('Y-m-d'))->where('status','attendance')->first();
        if($attendance && $attendance->check_in!=null){
            $response['check_in']=$attendance->check_in;
        }else{
            $response['check_in']=null;
        }
        if($attendance && $attendance->check_out!=null){
          $response['check_out']=$attendance->check_out;
        }else{
          $response['check_out']=null;
        }
        if($attendance && $attendance->check_in!=null && $attendance->check_out==null){
          $response['user_status']=true;
        }else{
          $response['user_status']=false;
        }
        $currentMonth = now()->month;           

        // Get the current year
        $currentYear = now()->year;
        $response['attendance_count']=Attendance::where('user_id',auth()->user()->id)->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)->whereDate('date', '<=', now())->where('status','attendance')->count();
        $response['absence_count']=Attendance::where('user_id',auth()->user()->id)->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)->whereDate('date', '<=', now())->where('status','absence')->count();
        $response['vacation_count']=Attendance::where('user_id',auth()->user()->id)->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)->whereDate('date', '<=', now())->whereIn('status',['emergency_vacation','ordinary_vacation','sick_vacation','vacation'])->count();
        return $this->sendResponse($response,null,200);
      }

      public function all_user_attendance(Request $request){
        
            $query = DB::table('attendances')->where('user_id',auth()->user()->id)->whereNull('deleted_at')->whereDate('date', '<=', now())
                
                ->select(
                  
                    'attendances.date as date',
                    'attendances.check_in',
                    'attendances.check_out',
                    DB::raw("IF(attendances.check_in IS NULL OR attendances.check_out IS NULL, '0:00', 
           TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, attendances.check_in, attendances.check_out)), '%H:%i')) as total_work_time"),
                    'attendances.status'
                )
                ->orderBy('attendances.date', 'desc')->orderBy('attendances.id', 'desc');

            // Apply filters if present
            if ($request->has('status') && $request->status != null) {
                $query->where('attendances.status', $request->status);
            }

            if ($request->has('date') && $request->date != null) {
                $query->whereDate('attendances.date', Carbon::parse($request->date)->format('Y-m-d'));
            }
            // Get the pagination count from the request or use a default value
            $perPage = $request->count ?? 15; // Default to 15 if count is not provided
            $page = $request->page ?? 1; // Default to page 1 if page is not provided

            // Paginate the results by the requested page and records per page
            $attendances = $query->paginate($perPage, ['*'], 'page', $page);

            // Prepare the response data
            $response = [
                'all_attendance' => $attendances->items(), // Actual records for the current page
                'total_pages' => $attendances->lastPage(),  // Total number of pages
                'current_page' => $attendances->currentPage(), // Current page
                'per_page' => $attendances->perPage(), // Records per page
                'total_records' => $attendances->total(), // Total number of records
            ];

            // Return the response with the attendance data and pagination info
            return $this->sendResponse($response, null, 200);

      }

      public function create_attendance(){
        Attendance::where('status','non')->update(['status'=>'absence']);
        Attendance::where('status','attendance')->where('check_out',null)->update(['check_out'=>'18:00:00']);
        $users = User::whereHas('roles', function ($q) {
                $q->where('name', 'Client');
            })
            ->get();
        foreach($users as $user){
          $attendance=Attendance::where('user_id',$user->id)->where('date',date('Y-m-d'))->first();
          if(!$attendance){
            $attendance=Attendance::create(['user_id'=>$user->id,
                                'date'=>date('Y-m-d')
                                ]);
          }
          if (date('l') == 'Friday'||date('l') == "Saturday") {
            $attendance->status = 'vacation';
            $attendance->save();
          }
        }


        return $this->sendResponse(null,'success',200);
      }

      public function attendance(Request $request){
        
        
        $response['all_attendance']= Attendance::where('date',$request->date)->with('user:id,name,username')->orderBy('id', 'desc')->get()->map(function ($attendance) {
          $attendance->user->image=getFirstMediaUrl($attendance->user,$attendance->user->avatarCollection);
          return $attendance;
        });
        $response['attendance_count']= Attendance::where('date',$request->date)->where('status','attendance')->count();
        $response['absence_count']= Attendance::where('date',$request->date)->where('status','!=','attendance')->count();

        return $this->sendResponse($response,null,200);
      }


      public function notify_user(){
        $current_time = new DateTime();
        $start_time = DateTime::createFromFormat('H:i', '09:00');
        $end_time = DateTime::createFromFormat('H:i', '10:00');

        if ($current_time >= $start_time && $current_time <= $end_time) {
          $attendance1=Attendance::where('date',date('Y-m-d'))->where('check_in',null)->get();
          foreach($attendance1 as $attend){
               if($attend->user->device_token){
                $this->firebaseService->sendNotification($attend->user->device_token,'Check-In',"Don't forget to check in",[]);
                $data=[
                  "title"=>"Check-In",
                  "message"=>"Don't forget to check in"];
                  Notification::create(['user_id'=>$attend->user_id,'data'=>$data]);
               }
              

          }
        }

        $start_time2 = DateTime::createFromFormat('H:i', '16:00');
        $end_time2 = DateTime::createFromFormat('H:i', '18:00');
        if ($current_time >= $start_time2 && $current_time <= $end_time2) {
          $attendance2=Attendance::where('date',date('Y-m-d'))->where('check_in','!=',null)->where('check_out',null)->get();
          foreach($attendance2 as $attendance){
               if($attendance->user->device_token){
                $this->firebaseService->sendNotification($attendance->user->device_token,'CHeck-Out',"Don't forget to check out",[]);
                $data=[
                  "title"=>"Check-Out",
                  "message"=>"Don't forget to check out"];
                  Notification::create(['user_id'=>$attendance->user_id,'data'=>$data]);
               }
          }
          

        }
        return $this->sendResponse(null,'success',200);
      }

}