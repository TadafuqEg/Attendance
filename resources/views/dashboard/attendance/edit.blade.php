@extends('dashboard.layout.app')
@section('title', 'Dashboard - edit attendance')
@section('content')	
    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                      <div class="card-title">Edit Attendance</div>
                      <hr>
                       <form method="post" action="{{route('update.attendance', ['id' => $attendance->id])}}" enctype="multipart/form-data">
                        @csrf
                      <div class="form-group">
                       <label>Name</label>
                        <input type="text" disabled class="form-control"  value="{{ $attendance->user->name }}">
                        
                      </div>
                      
                      <div class="form-group">
                        <label>Date</label>
                        <input type="date" disabled class="form-control"  value="{{ $attendance->date }}">
                       
                      </div>
                      <div class="form-group">
                        <label>Check In</label>
                        <input type="time" name="check_in_time" class="form-control" @if($attendance->check_in!=null) value="{{ old('check_in_time',date('H:i',strtotime($attendance->check_in))) }}" @else value="{{ old('check_in_time') }}" @endif>
                        @if ($errors->has('check_in_time'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('check_in_time') }}</p>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Check Out</label>
                        <input type="time" name="check_out_time" class="form-control"   @if($attendance->check_out!=null) value="{{ old('check_out_time',date('H:i',strtotime($attendance->check_out))) }}" @else value="{{ old('check_out_time') }}" @endif>
                        @if ($errors->has('check_out_time'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('check_out_time') }}</p>
                        @endif
                      </div>
                     <div class="form-group">
                      <label>Status</label>
                        <select class="form-control" name="status">
                            <option value="">Select Status</option>
                            <option value="non" @if($attendance->status=='non') selected @endif>IN Road</option>
                            <option value="attendance"@if($attendance->status=='attendance') selected @endif>Attendance</option>
                            <option value="absence"@if($attendance->status=='absence') selected @endif>Absence</option>
                            <option value="vacation"@if($attendance->status=='vacation') selected @endif>Day Off</option>
                            <option value="emergency_vacation"@if($attendance->status=='emergency_vacation') selected @endif>Casual Leave</option>
                            <option value="ordinary_vacation"@if($attendance->status=='ordinary_vacation') selected @endif>Regular Leave</option>
                            <option value="sick_vacation"@if($attendance->status=='sick_vacation') selected @endif>Sick Leave</option>
                            
                            <!-- Add more options as needed -->
                        </select>
                     </div>
                      
                      
                      
                      
                      <div class="form-group">
                       <button type="submit" class="btn btn-light px-5"><i class="icon-lock"></i> Register</button>
                     </div>
                     </form>
                    </div>
                    </div>
                 </div>
            </div>
            <div class="overlay toggle-menu"></div>
        </div>
    </div>
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endpush
