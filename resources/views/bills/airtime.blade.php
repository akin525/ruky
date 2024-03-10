@extends('layouts.sidebar')
@section('tittle', 'Airtime')
@section('page', 'Buy Airtime')
@section('content')
    <div class="loading-overlay" id="loadingSpinner" style="display: none;">
        <div class="loading-spinner"></div>
    </div>
    <form id="dataForm" class="card card-body">
        @csrf
        <div class="row">
            <div class="col-sm-8">
                <br>
                <br>
                <div id="AirtimePanel">
                    <div class="subscribe">
                        <p>AIRTIME PURCHASE</p>
                        {{--                       <input placeholder="Your e-mail" class="subscribe-input" name="email" type="email">--}}
                        <br/>
                        <div id="div_id_network" class="form-group">
                            <label for="network" class=" requiredField">
                                Network<span class="asteriskField">*</span>
                            </label>
                            <div class="">
                                <select name="id" class="text-success form-control" required="">
                                    <option>Select your network</option>
                                    @if($server->server == "mcd")
                                        <option value="m">MTN</option>
                                        <option value="g">GLO</option>
                                        <option value="a">AIRTEL</option>
                                        <option value="9">9MOBILE</option>
                                    @elseif($server->server =="easyaccess")
                                        <option value="01">MTN</option>
                                        <option value="02">GLO</option>
                                        <option value="03">AIRTEL</option>
                                        <option value="04">9MOBILE</option>
                                    @endif

                                </select>
                            </div>
                        </div>
                        <br/>
                        <div id="div_id_network" >
                            <label for="network" class=" requiredField">
                                Enter Amount<span class="asteriskField">*</span>
                            </label>
                            <div class="">
                                <input type="number" id="amount" name="amount" min="50" max="4000" oninput="calc()" class="text-success form-control" required>
                            </div>
                        </div>
                        <br/>
                        <div id="div_id_network" class="form-group">
                            <label for="network" class=" requiredField">
                                Enter Phone Number<span class="asteriskField">*</span>
                            </label>
                            <div class="">
                                <input type="number" id="number" name="number" minlength="11" class="text-success form-control" required>
                            </div>
                        </div>
                        <input type="hidden" name="refid" value="<?php echo rand(10000000, 999999999); ?>">
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
                        <button type="submit" class="submit-btn" >PURCHASE</button>
                    </div>


                    {{--                    <button type="submit" class=" btn btn-success" style="color: white;background-color: #28a745" id="warning"> Purchase Now<span class="load loading"></span></button>--}}
                    <script>
                        const btns = document.querySelectorAll('button');
                        btns.forEach((items)=>{
                            items.addEventListener('click',(evt)=>{
                                evt.target.classList.add('activeLoading');
                            })
                        })
                    </script>
                </div>


            </div>
            <br/>
            <br/>
            <div class="col-sm-4">
                <br/>
                <br/>
                <div class="card bg-primary">
                    <center>

                        <div class="">
                            <h4 class="heading mb-0 text-white">Advertisement 😎</h4>
                        </div>
                        <div class="card card-body shadow-hover">
                            <style>
                                .bo {
                                    max-width: 100%;
                                    height: auto;
                                }
                            </style>
                            @if($ads=="")
                                <a href="{{route('advert')}}">
                                    <img  class="bo" src="{{asset('ad.jpg')}}" alt="ads" />
                                </a>
                            @else
                                <a href="{{route('ads-detail', $ads->id)}}">
                                    <img  class="bo" src="{{url('/', $ads->cover_image)}}" alt="ads" />
                                    <h3 class="text-primary" ><b>{{$ads->advert_name}}</b></h3>
                                </a>
                        @endif
                    </center>



                    {{--                                            <div class="d-flex justify-content-between">--}}
                    {{--                                                <div class="sales-bx">--}}
                    {{--                                                    <i class="fa fa-wallet text-yellow" style="font-size: 40px;"></i>--}}
                    {{--                                                    <h4>₦{{number_format(intval(Auth::user()->parentData->balance *1),2)}}</h4>--}}
                    {{--                                                    <span>Balance</span>--}}
                    {{--                                                </div>--}}
                    {{--                                                <div class="sales-bx">--}}
                    {{--                                                    <i class="fa fa-wallet text-yellow" style="font-size: 40px"></i>--}}
                    {{--                                                    <h4>₦{{number_format(intval(Auth::user()->parentData->bonus *1),2)}}</h4>--}}
                    {{--                                                    <span>Bonus</span>--}}
                    {{--                                                </div>--}}
                    {{--                                            </div>--}}
                    {{--                                            <ul class="list-group ">--}}
                    {{--                                                <b><li class="list-group-item list-group-item-primary text-white"> MTN *310#</li></b>--}}
                    {{--                                                <b><li class="list-group-item list-group-item-success text-white">MTN [CG] *131*4# or *460*260#</li></b>--}}
                    {{--                                                <b><li class="list-group-item list-group-item-action text-white">9mobile  *223#</li></b>--}}
                    {{--                                                <b><li class="list-group-item list-group-item-info text-white">Airtel *123#</li></b>--}}
                    {{--                                                <b><li class="list-group-item list-group-item-secondary text-white">Glo *124*0#</li></b>--}}
                    {{--                                            </ul>--}}
                </div>
            </div>
        </div>


    </form>
@endsection
@section('script')
    <script>
        $(document).ready(function() {


            // Send the AJAX request
            $('#dataForm').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting traditionally

                // Get the form data
                var formData = $(this).serialize();
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to buy airtime of ₦' + document.getElementById("amount").value + ' on ' + document.getElementById("number").value +' ?',
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
