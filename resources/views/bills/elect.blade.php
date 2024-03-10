@extends('layouts.sidebar')
@section('tittle', 'Electricity')
@section('content')
    <div class="row">
        <div class="loading-overlay" id="loadingSpinner" style="display: none;">
            <div class="loading-spinner"></div>
        </div>
        <div class="row">
            <div class="loading-overlay" id="loadingSpinner1" style="display: none;">
                <div class="loading-spinner"></div>
            </div>
        <div style="padding:90px 15px 20px 15px">
            <form id="dataForm">
                @csrf
                <div class="row">
                    <div class="col-sm-8">
                        <br>
                        <br>
                        <div id="AirtimePanel">
                            <div class="subscribe">
                                <p>Electricity</p>
                              <br/>
                                <div id="div_id_network" class="form-group">
                                    <label for="network" class=" requiredField">
                                     Select Your Tv<span class="asteriskField">*</span>
                                    </label>
                                    <div class="">
                                        <select name="id" id="firstSelect" class="text-success form-control" required="">

                                            @foreach($tv as $tv1)
                                                <option value="{{$tv1['id']}}">{{$tv1['name']}}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                                <br/>
                                <div id="div_id_network" class="form-group">
                                    <label for="network" class=" requiredField">
                                        Enter Meter Number<span class="asteriskField">*</span>
                                    </label>
                                    <div class="">
                                        <input type="number" id="number" name="number" minlength="11" class="text-success form-control" required>
                                    </div>
                                </div>
                                <br/>
                                <div id="div_id_network" class="form-group">
                                    <label for="network" class=" requiredField">
                                        Meter Name<span class="asteriskField">*</span>
                                    </label>
                                    <div class="" >
                                        <input type="text" id="name" name="name" class="text-success form-control" required readonly>

                                    </div>
                                </div>
                                <div id="div_id_network" >
                                    <label for="network" class=" requiredField">
                                        Enter Amount<span class="asteriskField">*</span>
                                    </label>
                                    <div class="">
                                        <input type="number" id="amount" name="amount" min="50" max="4000" class="text-success form-control" required>
                                    </div>
                                </div>
                                <input type="hidden" name="refid" value="<?php echo rand(10000000, 999999999); ?>">
                                <button type="submit" class="submit-btn" >PURCHASE</button>
                            </div>

                        </div>


                    </div>
                    <br/>
                    <br/>
                    <div class="col-sm-4 ">
                        <br/>
                        <br/>
                        <div class="card bg-primary">
                            <div class="card-header border-0">
                                <h4 class="heading mb-0 text-white">Wallet & Bonus 😎</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div class="sales-bx">
                                        <i class="fa fa-wallet text-yellow" style="font-size: 40px;"></i>
                                        <h4>₦{{number_format(intval(Auth::user()->parentData->balance *1),2)}}</h4>
                                        <span>Balance</span>
                                    </div>
                                    <div class="sales-bx">
                                        <i class="fa fa-wallet text-yellow" style="font-size: 40px"></i>
                                        <h4>₦{{number_format(intval(Auth::user()->parentData->bonus *1),2)}}</h4>
                                        <span>Bonus</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </form>


        </div>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#number').on('input', function() {
                var inputElement = document.getElementById("number");
                var inputValue = inputElement.value;
                var secondS = $('#firstSelect');
                var third = $('#name');

                if (inputValue.length === 11) {
                    $('#loadingSpinner1').show();

                    $.ajax({
                        url: '{{ url('verifyelec') }}/' + inputValue + '/' + secondS.val(),
                        type: 'GET',
                        data: {
                            value1: inputValue,
                            value2: secondS.val()
                        },
                        success: function(response) {
                            $('#loadingSpinner1').hide();
                            $('#name').val(response);
                        },
                        error: function(xhr) {
                            $('#loadingSpinner1').hide();
                            Swal.fire({
                                icon: 'error',
                                title: 'fail',
                                text: xhr.responseText
                            });
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#dataForm').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting traditionally
                // Get the form data
                var formData = $(this).serialize();
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to buy ' + document.getElementById("firstSelect").options[document.getElementById("firstSelect").selectedIndex].text + ' of ' + document.getElementById("amount").value + ' on ' + document.getElementById("number").value + ' (' + document.getElementById("name").value + ')?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#loadingSpinner').show();
                        $.ajax({
                            url: "{{ route('buyelect') }}",
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


                // Send the AJAX request
            });
        });

    </script>


@endsection
