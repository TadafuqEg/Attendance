@extends('dashboard.layout.app')
@section('title', 'Dashboard - edit permission request')
@section('content')	
    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                      <div class="card-title">Edit Permission Request</div>
                      <hr>
                       <form method="post" action="{{route('update.permission_request', ['id' => $PermissionRequest->id])}}" enctype="multipart/form-data">
                        @csrf
                      <div class="form-group">
                       <label>Name</label>
                        <input type="text" disabled class="form-control"  value="{{ $PermissionRequest->user->name }}">
                        
                      </div>
                      
                      <div class="form-group">
                        <label>Date</label>
                        <input type="date" disabled class="form-control"  value="{{ $PermissionRequest->date }}">
                       
                      </div>
                      <div class="form-group">
                        <label>Time From</label>
                        <input type="time" disabled class="form-control"  value="{{ $PermissionRequest->from }}">
                       
                      </div>
                      <div class="form-group">
                        <label>Time To</label>
                        <input type="time" disabled class="form-control"  value="{{ $PermissionRequest->to }}">
                       
                      </div>
                     
                      
                      <div class="form-group">
                        <label>Message</label>
                        <textarea disabled class="form-control" rows="5">{{ $PermissionRequest->message }}</textarea>
                      </div>
                     
                     
                      
                     <div class="form-group">
                        <label>HR Approval</label>
                        <select class="form-control" name="hr_approval">
                            
                            <option value="pending" @if($PermissionRequest->hr_approval=='pending') selected @endif>Pending</option>
                            <option value="accepted"@if($PermissionRequest->hr_approval=='accepted') selected @endif>Accepted</option>
                            <option value="rejected"@if($PermissionRequest->hr_approval=='rejected') selected @endif>Rejected</option>
                           
                            
                            <!-- Add more options as needed -->
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Manager Approval</label>
                        <select class="form-control" name="Manager_approval">
                            
                            <option value="pending" @if($PermissionRequest->Manager_approval=='pending') selected @endif>Pending</option>
                            <option value="accepted"@if($PermissionRequest->Manager_approval=='accepted') selected @endif>Accepted</option>
                            <option value="rejected"@if($PermissionRequest->Manager_approval=='rejected') selected @endif>Rejected</option>
                           
                            
                            <!-- Add more options as needed -->
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="5" placeholder="Reply On Message">{{ $PermissionRequest->rejection_reason }}</textarea>
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
