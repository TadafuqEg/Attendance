@extends('dashboard.layout.app')
@section('title', 'Dashboard - users')
@section('content')	
<style>
    .pagination{
        display: inline-flex;
    }
    .user-status {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-left: -4%;
        margin-bottom: 4.65%;
    }

    .online {
        background-color: green;
    }

    .offline {
        background-color: gray;
    }
</style>
    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <div>
                            
                            <form id="searchForm" class="search-bar" style="margin-bottom:1%;margin-right:20px;margin-left:0px;"method="post" action="{{ route('users') }}" enctype="multipart/form-data">
                                @csrf
                                <div style="display:flex;">
                                  <h5 class="card-title" style="width: 60%;">Employees</h5>
                                  <div style="display:flex;margin-bottom:1%;margin-left:0px;">
                                    <a  class="btn btn-light px-5" style="margin:0% 1% 1% 1%; " href="{{route('add.user')}}">create</a>
                                    {{-- <button class="btn btn-light px-5" type="button" onclick="toggleFilters()"style="margin:0% 1% 1% 1%; ">Filter</button> --}}
                                    <input type="text" class="form-control" placeholder="Enter keywords" name="search">
                                    <a href="javascript:void(0);" id="submitForm"><i class="icon-magnifier"></i></a>
                                  </div>
                                  
                                </div>
                                
                                
                                {{-- <div id="filterOptions" style="display: none; text-align:center;">
                                  <div style="display: flex; justify-content: center; align-items: center;">

                                    <select class="form-control"style="width: 33%;" name="role">
                                        <option value="">Select Role</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Client">Client</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                    
                                    
                                  </div>
                                    
                                    
                                    <button class="btn btn-light px-5" style="margin-top:10px" type="submit">Apply Filters</button>
                                </div> --}}
                            </form>
                            {{-- <a  class="btn btn-light px-5" style="margin-bottom:1%; " href="{{route('add.user')}}">create</a> --}}
                        </div>
                       
                        <div class="table-responsive">
                        <table class="table table-hover">
                          <thead>
                            <tr>
                              
                              <th scope="col">Name</th>
                              <th scope="col">Email</th>
                              <th scope="col">Phone Number</th>
                              <th scope="col">Job Title</th>
                              
                              <th scope="col">Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($all_users) && $all_users->count())
                            @foreach($all_users as $user)
                              <tr>
                                <td><span class="user-profile"><img @if(getFirstMediaUrl($user,$user->avatarCollection)!=null) src="{{getFirstMediaUrl($user,$user->avatarCollection)}}" @else src="{{asset('dashboard/user_avatar.png')}}" @endif class="img-circle" alt="user avatar"></span> {{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                
                                <td>{{$user->phone}}</td>
                                <td>{{$user->username}}</td>
                                
                                {{-- <td>{{$user->roles->first()->name}}</td> --}}
                                
                                <td>
                                  
                                  
                                  <a style="margin-right: 1rem; cursor: pointer;"onclick='toggleModel({{$user->id}},"{{$user->name}}","{{ getFirstMediaUrl($user, $user->avatarCollection) ?? asset('dashboard/user_avatar.png') }}");'>
                                    <span  class="bi bi-graph-up" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a>
                                  <a href="{{url('/admin-dashboard/user/edit/'.$user->id)}}" style="margin-right: 1rem;">
                                    <span  class="bi bi-pen" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a>
                                 
                                  <a onclick="showConfirmationPopup('{{ url('/admin-dashboard/user/delete/'.$user->id) }}')">
                                    <span class="bi bi-trash" style="font-size: 1rem; color: rgb(255,255,255);"></span>
                                  </a>
                                 
                                  
                                </td>
                              </tr>
                            @endforeach
                          @else
                              <tr>
                                <td>There are no Employees.</td>
                              </tr>
                          @endif
                          </tbody>
                        </table>
                        <div style="text-align: center;">
                          {!! $all_users->appends(['search' => request('search')])->links("pagination::bootstrap-4") !!}
                        </div>
                      </div>
                      </div>
                    </div>
                  </div>
            </div>
            <div class="overlay toggle-menu"></div>
        </div>
    </div>
   
    
    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle" style="color:black;">Evaluation Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="evaluationForm">
            <input type="hidden" id="userId" name="user_id" value="">
            <div class="modal-body" style="color:black;">
              <div class="form-group">
                <div style="width: 100%;  display: flex;justify-content: center;">
                  <img id="userAvatar" src="{{asset('dashboard/logo.png')}}" class="logo-icon" alt="logo icon" style="width:100px;height:100px; border-radius: 50%;">
                </div>
                <div style="width: 100%;  display: flex;justify-content: center;">
                  <h5 class="logo-text"style="color:black;font-weight: bold;" id="nameInput"></h5>

                </div>
              </div>
              <!-- Evaluation input -->
              <div class="form-group">
                <label for="evaluationInput"style="color:black;">Evaluation</label>
                <input type="number" class="form-control" id="evaluationInput" name="evaluation" min="1" max="100" required style="color:black !important;border: 1px solid #a9a9af;">
              </div>
              <!-- Note textarea -->
              <div class="form-group">
                <label for="noteTextarea"style="color:black;">Note</label>
                <textarea class="form-control" id="noteTextarea" name="note" rows="5" style="color:black !important;border: 1px solid #a9a9af;"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary"id="submitEvaluation">Save Evaluation</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle" style="color:black;">Evaluation Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="color:black;">
          <h5 style="color:rgb(17, 133, 13)">Evaluation saved successfully!</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="confirmationPopup" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle" style="color:black;">Are you sure you want to delete this user?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="color:black;">
            <button onclick="deleteUser()">Yes</button>
            <button onclick="hideConfirmationPopup()">No</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle" style="color:black;">Evaluation Form</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" style="color:black;">
          <h5 style="color:rgb(201, 21, 21)">Sorry, You Can't Evaluate Now!</h5>
          <h5 style="color:rgb(201, 21, 21)">Evaluation is from day 27 to day 3 of next month</h5>
          </div>
        </div>
      </div>
    </div>
@endsection
@push('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  function showConfirmationPopup(deleteUrl) {
   
        
        const myModal = new bootstrap.Modal(document.getElementById('confirmationPopup'), {});
              myModal.show();
        // Set the delete URL in a data attribute to access it in the deleteUser function
        document.getElementById('confirmationPopup').setAttribute('data-delete-url', deleteUrl);
    }

    function hideConfirmationPopup() {
       
        var modal = document.getElementById('confirmationPopup');
                  modal.classList.remove('show');
                  modal.style.display = 'none';
                  document.body.classList.remove('modal-open');
                  document.getElementsByClassName('modal-backdrop')[0].remove();
    }

    function deleteUser() {
        const deleteUrl = document.getElementById('confirmationPopup').getAttribute('data-delete-url');
        window.location.href = deleteUrl;
    }
</script>
<script>
    $(document).ready(function() {
        $('#submitForm').on('click', function() {
            $('#searchForm').submit();
        });
    });
    </script>
    
    <script>
      function toggleModel(id,name,imgSrc) {

        const array = [1, 2, 3, 31, 30, 29, 28, 27];
        const date = new Date().getDate(); // Get the current day of the month

        if (array.includes(date)) {
            document.getElementById('nameInput').textContent = name;
            document.getElementById('evaluationInput').value = ''; // Clear previous value
            document.getElementById('noteTextarea').value = '';
            document.getElementById('userAvatar').src = imgSrc;
            $('#userId').val(id);
            const myModal = new bootstrap.Modal(document.getElementById('exampleModalCenter'), {});
            myModal.show();
        } else {
          const myModal = new bootstrap.Modal(document.getElementById('exampleModalCenter3'), {});
              myModal.show();
              setTimeout(function() {
                  var modal = document.getElementById('exampleModalCenter3');
                  modal.classList.remove('show');
                  modal.style.display = 'none';
                  document.body.classList.remove('modal-open');
                  document.getElementsByClassName('modal-backdrop')[0].remove();
              }, 5000); 
        }

        
      }
  
      $(document).ready(function () {
    $('#submitEvaluation').on('click', function (e) {
        e.preventDefault();

        // Collect form data
        const formData = {
            user_id: $('#userId').val(),
            evaluation: $('#evaluationInput').val(),
            note: $('#noteTextarea').val(),
            _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
        };

        // AJAX request
        $.ajax({
            url: '/admin-dashboard/save-evaluation', // Update with your route URL
            type: 'POST',
            data: formData,
            success: function (response) {
              document.getElementById('exampleModalCenter').classList.remove('show');
              document.getElementById('exampleModalCenter').style.display = 'none';
              document.body.classList.remove('modal-open');
              document.getElementsByClassName('modal-backdrop')[0].remove();
              const myModal = new bootstrap.Modal(document.getElementById('exampleModalCenter2'), {});
              myModal.show();
              setTimeout(function() {
                  var modal = document.getElementById('exampleModalCenter2');
                  modal.classList.remove('show');
                  modal.style.display = 'none';
                  document.body.classList.remove('modal-open');
                  document.getElementsByClassName('modal-backdrop')[0].remove();
              }, 3000); 

            },
            error: function (xhr) {
                // Handle error response
                alert('Error: ' + xhr.responseJSON.message);
                console.error(xhr.responseJSON.errors);
            }
        });
    });
});
  </script>
@endpush
