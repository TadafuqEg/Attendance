<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class AttendanceExport implements FromCollection,WithHeadings
{ 
    //use Exportable;

    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    { 
            $query = DB::table('attendances')->whereNull('attendances.deleted_at')->whereDate('attendances.date', '<=', now())
                ->join('users', 'attendances.user_id', '=', 'users.id')
                ->select(
                    'users.name as name',
                    'attendances.date as date',
                    'attendances.check_in',
                    'attendances.check_out',
                    DB::raw("IF(attendances.check_in IS NULL OR attendances.check_out IS NULL, '0:00', 
           TIME_FORMAT(SEC_TO_TIME(TIMESTAMPDIFF(SECOND, attendances.check_in, attendances.check_out)), '%H:%i')) as total_work_time"),
                    'attendances.status'
                )
                ->orderBy('attendances.date', 'desc')->orderBy('attendances.id', 'desc');

            // Apply filters if present
            if ($this->request->has('status') && $this->request->status != null) {
                $query->where('attendances.status', $this->request->status);
            }

            if ($this->request->has('date_from') && $this->request->date_from != null && $this->request->has('date_to') && $this->request->date_to != null) {
                $query->whereDate('attendances.date', '>=',Carbon::parse($this->request->date_from)->format('Y-m-d'))->whereDate('attendances.date', '<=',Carbon::parse($this->request->date_to)->format('Y-m-d'));
            }elseif($this->request->has('date_from') && $this->request->date_from != null && $this->request->date_to == null){
                $query->whereDate('attendances.date', Carbon::parse($this->request->date_from)->format('Y-m-d'));
            }

            if ($this->request->has('user') && $this->request->user != null) {
                $query->where('attendances.user_id', $this->request->user);
            }

            if ($this->request->has('auto_logout') && $this->request->auto_logout != null) {
                $query->where('attendances.auto_logout', '1');
            }

            return $query->get();
       
    }
    public function headings(): array
    {
        return ['Name', 'Date', 'Check-In Time', 'Check-Out Time', 'Total Work Time', 'Status'];
    }
}
