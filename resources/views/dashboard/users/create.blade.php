@extends('dashboard.layout.app')
@section('title', 'Dashboard - create user')
@section('content')	
    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="row mt-3">
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                      <div class="card-title">Create New Employee</div>
                      <hr>
                       <form method="post" action="{{route('create.user')}}" enctype="multipart/form-data">
                        @csrf
                      <div class="form-group">
                       <label>Name</label>
                        <input type="text" name="name" class="form-control"  placeholder="Enter Name"value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('name') }}</p>
                        @endif
                      </div>
                      
                      <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"  placeholder="Enter Email"value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('email') }}</p>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Job Title</label>
                        <input type="text" name="username" class="form-control"  placeholder="Enter Username"value="{{ old('username') }}">
                        @if ($errors->has('username'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('username') }}</p>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Phone Number</label>
                        <input type="number" name="phone_number" class="form-control"  placeholder="Enter Phone Number"value="{{ old('phone_number') }}">
                        @if ($errors->has('phone_number'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('phone_number') }}</p>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control"  placeholder="Enter Address"value="{{ old('address') }}">
                       
                      </div>
                      {{-- <div class="form-group">
                        <label>Role</label>
                        
                        <select class="form-control" name="role">
                          <option value="">Select Role</option>
                          @foreach($roles as $role)
                          <option value="{{$role->id}}" @if(old('role')==$role->id) selected @endif>{{$role->name}}</option>
                          @endforeach
                        </select>
                        @if ($errors->has('role'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('role') }}</p>
                        @endif
                      </div> --}}
                      <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" id="imageInput" placeholder="Choose Image" onchange="showImage(event)">
                        <img id="imagePreview" class="img-thumbnail" src="#" alt="Selected Image" style="display: none; height:100px;">
                        @if ($errors->has('image'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('image') }}</p>
                        @endif
                      </div>
                      <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control"  placeholder="Enter Password">
                        @if ($errors->has('password'))
                            <p class="text-error more-info-err" style="color: red;">
                                {{ $errors->first('password') }}</p>
                        @endif
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
<script>
  function showImage(event) {
      var imageInput = event.target;
      var imagePreview = document.getElementById('imagePreview');
      
  
      if (imageInput.files && imageInput.files[0]) {
          var reader = new FileReader();
  
          reader.onload = function(e) {
              imagePreview.src = e.target.result;
              imagePreview.style.display = 'block';
          }
  
          reader.readAsDataURL(imageInput.files[0]);
      }
  }
  </script>
@endpush
