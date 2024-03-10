@extends('layouts.sidebar')
@section('tittle', 'Data-Pin')
@section('content')

    <div style="padding:90px 15px 20px 15px">
        <div class="loading-overlay" id="loadingSpinner" style="display: none;">
            <div class="loading-spinner"></div>
        </div>
        <form id="dataForm">
            @csrf
            <div class="row">
                <div class="col-sm-8">
                    <br>
                    <br>
                    <div class="subscribe">
                        <div id="AirtimePanel">
                            <div id="div_id_network" class="form-group">
                                <label for="network" class=" requiredField">
                                    Network<span class="asteriskField">*</span>
                                </label>
                                <div class="">
                                    <select name="id" class="text-success form-control" required="">

                                        <option value="m">MTN</option>

                                    </select>
                                </div>
                            </div>

                            <select  name="productid" class="text-success form-control" onChange="myNewFunction(this);" required="">

                                <option value="data_pin" >[mtn] 1.5GB (DATAPIN) ₦{{$product->tamount}}
                                </option>

                            </select>
                            <input type="hidden" name="refid" value="<?php echo rand(10000000, 999999999); ?>">
                            <button type="submit" class="submit-btn">PURCHASE</button>


                        </div>
                    </div>
                </div>
                <div class="col-sm-4 ">
                    <br>
                    <ul class="list-group">
                        <li class="list-group-item list-group-item-primary"><b>Please follow these steps:</b></li>
                        <li class="list-group-item list-group-item-success"> <b>Dial *460*6*1#</b></li>
                        <li class="list-group-item list-group-item-action"> <b>Enter the Pin given to you in the box</b></li>
                        <li class="list-group-item list-group-item-info">Click on Send </li>
                    </ul>
                    <br>

                    <br>

                </div>
            </div>

        </form>
    </div>

@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('#dataForm').submit(function(e) {
                e.preventDefault(); // Prevent the form from submitting traditionally

                // Get the form data
                var formData = $(this).serialize();
                $('#loadingSpinner').show();

                // Send the AJAX request
                $.ajax({
                    url: "{{ route('buypin') }}",
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
                                icon: 'error',
                                title: 'fail',
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
            });
        });

    </script>
@endsection
