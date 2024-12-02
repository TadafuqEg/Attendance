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
use App\Models\OfficialHoliday;
use Image;
use Str;
use App\Exports\AttendanceExport;
use File;
use Maatwebsite\Excel\Facades\Excel;
class OfficialHolidayController extends Controller
{//done
    
    public function index(Request $request)
    {
        $all_holidays = OfficialHoliday::orderBy('id', 'desc');

        if ($request->has('search') && $request->search!=null ) {
            $all_holidays->where(function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->search . '%');
            });
        }
        $all_holidays = $all_holidays->paginate(12);

          
        $key_search=$request->search;
        
        return view('dashboard.official_holidays.index',compact('all_holidays','key_search'));

    }


    public function edit($id){
        $OfficialHoliday=OfficialHoliday::where('id',$id)->first();
        return view('dashboard.official_holidays.edit',compact('OfficialHoliday'));
    }

    public function update(Request $request,$id){
        $validator  =   Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
    
        
        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }
        OfficialHoliday::where('id',$id)->update(['from'=>$request->date_from,'to'=>$request->date_to]);
        
        return redirect('/admin-dashboard/official_holidays');

    }


   

   
}