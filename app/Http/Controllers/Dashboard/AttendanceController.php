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
use App\Models\Attendance;
use App\Models\DriverLicense;
use Image;
use Str;
use App\Exports\AttendanceExport;
use File;
use Maatwebsite\Excel\Facades\Excel;
class AttendanceController extends Controller
{//done
    public function index(Request $request)
    {   
        $validator  =   Validator::make($request->all(), [
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
            
        ]);
        if ($validator->fails()) {

           // return Redirect::back()->withErrors(['error'=>'trdfssfghtrh']);
            return Redirect::back()->with('error', 'The end date must be after the start date.');
        }
        
        if($request->has('export') && $request->export=='1'){
            $invitation_code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_'), 0, 12);
            return Excel::download(new AttendanceExport($request), auth()->user()->id . $invitation_code . 'attencance.xlsx');
            //$contact_us=ContactUs::all();
        }else{
            
        
            $all_attendance = Attendance::orderBy('date', 'desc')->orderBy('id', 'desc')->whereDate('date', '<=', now());

            if ($request->has('status')&& $request->status!=null) {
                $all_attendance->where('status', $request->status);
            }
            
        
        
            if($request->has('date_from')&& $request->date_from!=null && $request->has('date_to')&& $request->date_to!=null) {
                $all_attendance->where('date','>=', date('Y-m-d',strtotime($request->date_from)))->where('date','<=', date('Y-m-d',strtotime($request->date_to)));
            }elseif($request->has('date_from')&& $request->date_from!=null && $request->date_to==null){
                $all_attendance->where('date', date('Y-m-d',strtotime($request->date_from)));
            }
            if($request->has('user')&& $request->user!=null) {
                $all_attendance->where('user_id', $request->user);
            }
            if($request->has('auto_logout')&& $request->auto_logout!=null) {
                $all_attendance->where('auto_logout', '1');
            }
            $all_attendance = $all_attendance->paginate(12);

            // $all_attendance->getCollection()->transform(function ($attendance) {
            //     // Add the 'image' key based on some condition
            //     $attendance->user->image = getFirstMediaUrl($attendance->user,$attendance->user->avatarCollection);
            //     return $attendance;
            // });
            $users=User::whereHas('roles', function ($query) {
                $query->where('roles.name', 'Client');
            })->get();
        }
        $status_search=$request->status;
        $date_from_search=$request->date_from;
        $date_to_search=$request->date_to;
        $user_search=$request->user;
        return view('dashboard.attendance.index',compact('all_attendance','users','status_search','date_to_search','date_from_search','user_search'));

    }


    public function edit($id){
        $attendance=Attendance::where('id',$id)->first();
        // $attendance->user->image = getFirstMediaUrl($attendance->user,$attendance->user->avatarCollection);
        return view('dashboard.attendance.edit',compact('attendance'));
    }

    public function update(Request $request,$id){
        $validator = Validator::make($request->all(), [
            'check_in_time' => ['nullable'],
            'check_out_time' => ['nullable'],
            'status' => ['required']
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        
        Attendance::where('id',$id)->update([  'check_in' => $request->check_in_time,
                                            'check_out' => $request->check_out_time,
                                            'status'=> $request->status
                                           ]);
     
        return redirect('/admin-dashboard/attendance');

    }


   

    public function delete($id)
    {
        Attendance::where('id', $id)->delete();
        return redirect('/admin-dashboard/attendance');
    }
}