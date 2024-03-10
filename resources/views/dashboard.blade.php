@extends('layouts.sidebar')
@section('tittle', 'Dashboard')
@section('page', 'Dashboard')
@section('content')

    <div class="row">
        <div class="loading-overlay" id="loadingSpinner" style="display: none;">
            <div class="loading-spinner"></div>
        </div>
        <div class="panel-header   py-3 bubble-shadow" style="background: linear-gradient(to right, #132563,   #132563)!important">
            <div class="page-inner py-5">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row py-4">
                    <div>
                        <h2 class="text-white pb-2 fw-bold">Welcome to Ruky</h2>
                        <div class="card-title text-white" ><span id="greet"><b>{{$greet}} {{Auth::user()->username}}</b></span> </div> <hr>

                    </div>

                    {{--                    <div class="ml-md-auto py-2 py-md-0">--}}
                    {{--                        <button type="button" class="btn btn-warning btn-round mr-2" data-toggle="modal" data-target="#fundWalletModal">--}}
                    {{--                            Fund Wallet--}}
                    {{--                        </button>--}}


                    {{--                        <a href="/404/page-not-found-error/page/" class="btn btn-info btn-round text-white" style="visibility:hidden">.</a>--}}

                    {{--                    </div>--}}
                </div>


            </div>
        </div>

    </div>
    <br>
    <br>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body pb-xl-4 pb-sm-3 pb-0">
                    <div class="row">
                        <div class="col-xl-3 col-6">
                            <div class="">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
									<span class="me-3 bgl-primary text-primary">
										<i class="ti-wallet"></i>
{{--										<svg id="icon-wallet" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">--}}
{{--											<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>--}}
{{--											<circle cx="12" cy="7" r="4"></circle>--}}
{{--										</svg>--}}
									</span>
                                            <div class="media-bod  y">
                                                <p class="mb-1">Ruky Wallet</p>
                                                <h4 class="mb-0">₦{{number_format(intval($wallet['balance'] *1),2)}}</h4>
{{--                                                <span class="badge badge-primary">+3.5%</span>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-6">
                            <div class="">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
									<span class="me-3 bgl-primary text-primary">
										<i class="ti-wallet"></i>
									</span>
                                            <div class="media-bod  y">
                                                <p class="mb-1">Deposits</p>
                                                <h4 class="mb-0">₦{{number_format(intval($tdepo *1))}}</h4>
                                                {{--                                                <span class="badge badge-primary">+3.5%</span>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xl-3 col-6">
                            <div class="">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
									<span class="me-3 bgl-secondary text-primary">
										<i class="ti-wallet"></i>
									</span>
                                            <div class="media-bod  y">
                                                <p class="mb-1">Bills</p>
                                                <h4 class="mb-0">₦{{number_format(intval($tbill *1),2)}}</h4>
                                             </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-xl-3 col-6">
                            <div class="">
                                <div class="widget-stat card">
                                    <div class="card-body p-4">
                                        <div class="media ai-icon">
									<span class="me-3 bgl-secondary text-primary">
										<i class="ti-wallet"></i>
									</span>
                                            <div class="media-bod  y">
                                                <p class="mb-1">Bonus</p>
                                                <h4 class="mb-0">₦{{number_format(intval($wallet['bonus'] *1),2)}}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4 col-sm-3 col-lg-3">
            <a href="{{route('pick')}}">
                <div class="card">
                    <div class="card-body p-3 text-center">
                             <span style="font-size: 30px;">
                                 <img width="50" src="https://play-lh.googleusercontent.com/Vse_HvYw4_KZsvVf0NXXWBNnwEq0GVsihLw5z9yzc14MY8vuBet4Vl_shjP0EGg0WuU">
                             </span>
                        {{--                            <div class="h6  text-dark">Data</div>--}}
                        <small>Data</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-4 col-sm-3 col-lg-3">
            <a data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
                <div class="card">
                    <div class="card-body p-3 text-center">
                             <span style="font-size: 30px;">
                                 <img width="50" src="https://cloud.bekonta.com/public/user_dashboard/icons/airtime.svg">
                             </span>
                        <small>Airtime</small>
                        {{--                            <div class="h6  text-dark">Airtime</div>--}}
                    </div>
                </div>
            </a>
        </div>
        <div class="modal fade" id="exampleModalCenter">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Buy Airtime</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal">
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="dataForm" >
                            @csrf
                        <div id="AirtimePanel">
                            <div class="subscribe">
                                <br/>
                                <div id="div_id_network" class="form-group">
                                    <label for="network" class=" requiredField">
                                        Network<span class="asteriskField">*</span>
                                    </label>
                                    <div class="">
                                        <select name="id" class="text-success form-control" required="">

                                            <option value="m">MTN</option>
                                            <option value="g">GLO</option>
                                            <option value="a">AIRTEL</option>
                                            <option value="9">9MOBILE</option>

                                        </select>
                                    </div>
                                </div>
                                <br/>
                                <div id="div_id_network" >
                                    <label for="network" class=" requiredField">
                                        Enter Amount<span class="asteriskField">*</span>
                                    </label>
                                    <div class="">
                                        <input type="number" id="amount" name="amount" min="100" max="4000" oninput="calc()" class="text-success form-control" required>
                                    </div>
                                </div>
                                <br/>
                                <div id="div_id_network" class="form-group">
                                    <label for="network" class=" requiredField">
                                        Enter Phone Number<span class="asteriskField">*</span>
                                    </label>
                                    <div class="">
                                        <input type="number" id="anyme" name="number" minlength="11" class="text-success form-control" required>
                                    </div>
{{--                                    <i class="fa fa-user" onclick="web2app.selectContact(contactCallback);"></i>--}}
                                </div>
                                <input type="hidden" name="refid" value="<?php echo rand(10000000, 999999999); ?>">
                                <br>
                                <button type="submit" class="btn btn-success">PURCHASE</button>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="small mb-1" for="amount" style="color: #000000"><b>Amount to Pay (<span>₦</span>)</b></label>
                                        <br>
                                        <span class="text-danger">2% Discount:</span> <b class="text-success">₦<span id="shownow1"></span></b>
                                    </div>
                                </div>
                                <script>
                                    function calc(){
                                        var value = document.getElementById("amount").value;
                                        var percent = 2/100 * value;
                                        var reducedvalue = value - percent;
                                        document.getElementById("shownow1").innerHTML = reducedvalue;

                                    }
                                </script>
                            </div>
                        </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
{{--                        <button type="button" class="btn btn-primary">Save changes</button>--}}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-4 col-sm-3 col-lg-3">
            <a href="{{url('tv')}}">
                <div class="card">
                    <div class="card-body p-3 text-center">
                             <span style="font-size: 30px;">
                                 <img width="50" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSw-WmPyjVF6CMyYO61o15KbQdyMRR5b9X18w&usqp=CAU">
                             </span>
                        <h6>Tv</h6>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-4 col-sm-3 col-lg-3">
            <a href="{{url('electricity')}}">
                <div class="card">
                    <div class="card-body p-3 text-center">
                             <span style="font-size: 30px;">
                                 <img width="50" src="data:image/png;base64,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">
                             </span>
                        <small>Electricity</small>
                        {{--                            <div class="h6 m-2 text-dark">Electricity</div>--}}
                    </div>
                </div>
            </a>
        </div>
        <div class="col-4 col-sm-3 col-lg-3">
            <a href="#">
                <div class="card">
                    <div class="card-body p-3 text-center">
                             <span style="font-size: 30px;">
                                 <img width="50" src="https://cdn.pixabay.com/photo/2018/08/25/21/08/money-3630935_1280.png">
                             </span>
                        <small>Transfer</small>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="modal fade" id="exampleModalCenter">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {


            // Send the AJAX request
            $('#dataForm').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting traditionally

                // Get the form data
                var formData = $(this).serialize();
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to buy airtime of ₦' + document.getElementById("amount").value + ' on ' + document.getElementById("anyme").value +' ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // The user clicked "Yes", proceed with the action
                        // Add your jQuery code here
                        // For example, perform an AJAX request or update the page content
                        $('#loadingSpinner').show();

                        $.ajax({
                            url: "{{ route('buyairtime') }}",
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                // Handle the success response here
                                $('#loadingSpinner').hide();

                                console.log(response);
                                // Update the page or perform any other actions based on the response

                                if (response.status == 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.message
                                    }).then(() => {
                                        location.reload(); // Reload the page
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Pending',
                                        text: response.message
                                    });
                                    // Handle any other response status
                                }

                            },
                            error: function(xhr) {
                                $('#loadingSpinner').hide();
                                Swal.fire({
                                    icon: 'error',
                                    title: 'fail',
                                    text: xhr.responseText
                                });
                                // Handle any errors
                                console.log(xhr.responseText);

                            }
                        });

                    }
                });
            });
        });

    </script>

@endsection
