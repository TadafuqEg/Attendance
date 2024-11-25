@extends('dashboard.layout.app')
@section('title', 'Dashboard - edit leave request')
@section('content')	
    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                      <div class="card-title">Edit Leave Request</div>
                      <hr>
                       <form method="post" action="{{route('update.leave_request', ['id' => $VacationRequest->id])}}" enctype="multipart/form-data">
                        @csrf
                      <div class="form-group">
                       <label>Name</label>
                        <input type="text" disabled class="form-control"  value="{{ $VacationRequest->user->name }}">
                        
                      </div>
                      
                      <div class="form-group">
                        <label>Date From</label>
                        <input type="date" disabled class="form-control"  value="{{ $VacationRequest->from }}">
                       
                      </div>
                      <div class="form-group">
                        <label>Date To</label>
                        <input type="date" disabled class="form-control"  value="{{ $VacationRequest->to }}">
                       
                      </div>
                      <div class="form-group">
                        <label>Type</label>
                        <input type="text" disabled class="form-control" @if($VacationRequest->type=='emergency_vacation') value="Casual Leave" @elseif($VacationRequest->type=='ordinary_vacation') value="Regular Leave" @elseif($VacationRequest->type=='sick_vacation') value="Sick Leave" @endif>
                       
                      </div>
                      @if($VacationRequest->type=='sick_vacation')
                      <div class="form-group">
                        <label>Report</label>
                        <p>View report from here -> <a  href="{{getFirstMediaUrl($VacationRequest,$VacationRequest->AttachmentCollection)}}">Link</a></p>
                       
                      </div>
                      @else
                      <div class="form-group">
                        <label>Message</label>
                        <textarea disabled class="form-control" rows="5">{{ $VacationRequest->message }}</textarea>
                      </div>
                      @endif
                     
                      @if($VacationRequest->type!='emergency_vacation')
                        <div class="form-group">
                            <label>HR Approval</label>
                            <select class="form-control" name="hr_approval">
                                
                                <option value="pending" @if($VacationRequest->hr_approval=='pending') selected @endif>Pending</option>
                                <option value="accepted"@if($VacationRequest->hr_approval=='accepted') selected @endif>Accepted</option>
                                <option value="rejected"@if($VacationRequest->hr_approval=='rejected') selected @endif>Rejected</option>
                              
                                
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Manager Approval</label>
                            <select class="form-control" name="Manager_approval">
                                
                                <option value="pending" @if($VacationRequest->Manager_approval=='pending') selected @endif>Pending</option>
                                <option value="accepted"@if($VacationRequest->Manager_approval=='accepted') selected @endif>Accepted</option>
                                <option value="rejected"@if($VacationRequest->Manager_approval=='rejected') selected @endif>Rejected</option>
                              
                                
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Rejection Reason</label>
                            <textarea name="rejection_reason" class="form-control" rows="5" placeholder="Reply On Message">{{ $VacationRequest->rejection_reason }}</textarea>
                        </div>
                      
                      
                      
                      
                        <div class="form-group">
                          <button type="submit" class="btn btn-light px-5"><i class="icon-lock"></i> Register</button>
                        </div>
                      @endif
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
