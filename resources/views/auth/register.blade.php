<x-guest-layout>
    <style>
        .subscribe {
            position: relative;
            padding: 20px;
            background-color: #FFF;
            border-radius: 4px;
            color: #333;
            box-shadow: 0px 0px 60px 5px rgba(0, 0, 0, 0.4);
        }

        .subscribe:after {
            position: absolute;
            content: "";
            right: -10px;
            bottom: 18px;
            width: 0;
            height: 0;
            border-left: 0px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #0e8006;
        }

        .subscribe p {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 4px;
            line-height: 28px;
        }



        .subscribe .submit-btn {
            position: absolute;
            border-radius: 30px;
            border-bottom-right-radius: 0;
            border-top-right-radius: 0;
            background-color: #0e8006;
            color: #FFF;
            padding: 12px 25px;
            display: inline-block;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 5px;
            right: -10px;
            bottom: -20px;
            cursor: pointer;
            transition: all .25s ease;
            box-shadow: -5px 6px 20px 0px rgba(26, 26, 26, 0.4);
        }

        .subscribe .submit-btn:hover {
            background-color: #0e8006;
            box-shadow: -5px 6px 20px 0px rgba(88, 88, 88, 0.569);
        }
        button {
            padding: 20px 30px;
            font-size: 1.5em;
            /*width:200px;*/
            cursor: pointer;
            border: 0px;
            position: relative;
            /*margin: 20px;*/
            transition: all .25s ease;
            /*background: rgba(116, 23, 231, 1);*/
            color: #fff;
            overflow: hidden;
            /*border-radius: 10px*/
        }

        .load {
            position: absolute;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            background: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: inherit
        }

        .load::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            border: 3px solid #fff;
            width: 30px;
            height: 30px;
            border-left: 3px solid transparent;
            border-bottom: 3px solid transparent;
            animation: loading1 1s ease infinite;
            z-index: 10
        }

        .load::before {
            content: '';
            position: absolute;
            border-radius: 50%;
            border: 3px dashed #fff;
            width: 30px;
            height: 30px;
            border-left: 3px solid transparent;
            border-bottom: 3px solid transparent;
            animation: loading1 2s linear infinite;
            z-index: 5
        }

        @keyframes loading1 {
            0% {
                transform: rotate(0deg)
            }

            100% {
                transform: rotate(360deg)
            }
        }

        button.active {
            transform: scale(.85)
        }

        button.activeLoading .loading {
            visibility: visible;
            opacity: 1
        }

        button .loading {
            opacity: 0;
            visibility: hidden
        }
    </style>

    <div class="login-aside text-center  d-flex flex-column flex-row-auto">
        <div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
            <div class="text-center mb-lg-4 mb-2 pt-5 logo">
                <img width="100" src="{{asset('ruky.jpg')}}" alt="">
            </div>
            <h3 class="mb-2 text-white">Welcome back!</h3>
            <p class="mb-4">Sell online, process payments whether online or not. <br> Simply complete payment on the go using your banking app or ussd.</p>
        </div>
        <div class="aside-image position-relative" style="background-image:url({{asset('images/background/pic-2.png')}});">
            <img class="img1 move-1" src="{{asset('images/background/pic3.png')}}" alt="">
            <img class="img2 move-2" src="{{asset('images/background/pic4.png')}}" alt="">
            <img class="img3 move-3" src="{{asset('images/background/pic5.png')}}" alt="">

        </div>
    </div>
    <div class="container flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
        <div class="d-flex justify-content-center h-100 align-items-center">
            <div class="authincation-content style-2">
                <div class="row no-gutters">
                    <div class="col-xl-12 tab-content">
                        <div id="sign-up" class="auth-form tab-pane fade show active  form-validation">
                            <x-validation-errors class="alert alert-danger" />

                            @if (session('status'))
                                <div class="mb-4 font-medium text-sm text-green-600">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
            @csrf

                                <div class="text-center mb-4">
                                    <h3 class="text-center mb-2 text-black">Sign up</h3>
                                    <span>Your Social Campaigns</span>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-xl-6 col-6">
                                        <a href="https://www.google.com/" class="btn btn-outline-light d-block social-bx">
                                            <svg width="16" height="16" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M27.9851 14.2618C27.9851 13.1146 27.8899 12.2775 27.6837 11.4094H14.2788V16.5871H22.1472C21.9886 17.8738 21.132 19.8116 19.2283 21.1137L19.2016 21.287L23.44 24.4956L23.7336 24.5242C26.4304 22.0904 27.9851 18.5093 27.9851 14.2618Z" fill="#4285F4"/>
                                                <path d="M14.279 27.904C18.1338 27.904 21.37 26.6637 23.7338 24.5245L19.2285 21.114C18.0228 21.9356 16.4047 22.5092 14.279 22.5092C10.5034 22.5092 7.29894 20.0754 6.15663 16.7114L5.9892 16.7253L1.58205 20.0583L1.52441 20.2149C3.87224 24.7725 8.69486 27.904 14.279 27.904Z" fill="#34A853"/>
                                                <path d="M6.15656 16.7113C5.85516 15.8432 5.68072 14.913 5.68072 13.9519C5.68072 12.9907 5.85516 12.0606 6.14071 11.1925L6.13272 11.0076L1.67035 7.62109L1.52435 7.68896C0.556704 9.58024 0.00146484 11.7041 0.00146484 13.9519C0.00146484 16.1997 0.556704 18.3234 1.52435 20.2147L6.15656 16.7113Z" fill="#FBBC05"/>
                                                <path d="M14.279 5.3947C16.9599 5.3947 18.7683 6.52635 19.7995 7.47204L23.8289 3.6275C21.3542 1.37969 18.1338 0 14.279 0C8.69485 0 3.87223 3.1314 1.52441 7.68899L6.14077 11.1925C7.29893 7.82856 10.5034 5.3947 14.279 5.3947Z" fill="#EB4335"/>
                                            </svg>

                                            <span class="ms-1 font-w600 label-color">Sign in with Google</span></a>
                                    </div>
                                    <div class="col-xl-6 col-6">
                                        <a href="https://www.apple.com/" class="btn btn-outline-light d-block social-bx">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 456.008 560.035"><path d="M380.844 297.529c.787 84.752 74.349 112.955 75.164 113.314-.622 1.988-11.754 40.191-38.756 79.652-23.343 34.117-47.568 68.107-85.731 68.811-37.499.691-49.557-22.236-92.429-22.236-42.859 0-56.256 21.533-91.753 22.928-36.837 1.395-64.889-36.891-88.424-70.883-48.093-69.53-84.846-196.475-35.496-282.165 24.516-42.554 68.328-69.501 115.882-70.192 36.173-.69 70.315 24.336 92.429 24.336 22.1 0 63.59-30.096 107.208-25.676 18.26.76 69.517 7.376 102.429 55.552-2.652 1.644-61.159 35.704-60.523 106.559M310.369 89.418C329.926 65.745 343.089 32.79 339.498 0 311.308 1.133 277.22 18.785 257 42.445c-18.121 20.952-33.991 54.487-29.709 86.628 31.421 2.431 63.52-15.967 83.078-39.655"/></svg>
                                            <span class="ms-1 font-w600 label-color">Sign in with Apple</span></a>
                                    </div>
                                </div>
                                <div class="row subscribe">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="full-name" class="form-label">Full Name</label>
                                            <input type="text" name="name" class="form-control" id="full-name" placeholder="John">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="last-name" class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" id="username" placeholder="Doe">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="xyz@example.com">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="email" name="address" placeholder="address">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="phone" class="form-label">Phone No.</label>
                                            <input type="number" class="form-control" id="phone" name="phone" placeholder="123456789">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="phone" class="form-label">Date Of Birth.</label>
                                            <input type="date" class="form-control" name="dob" >
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Gender</label>
                                            <select name="gender" class="form-control" id="email" >
                                                <option>select gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" name="password" class="form-control" id="password" placeholder=" ">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="confirm-password" class="form-label">Confirm Password</label>
                                            <input type="text" class="form-control" name="password_confirmation" id="confirm-password" placeholder=" ">
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <button type="submit" class="submit-btn btn-primary">Sign Up <span class="load loading"></span></button>

                                </div>
                                <br/>
                                <script>
                                    const btns = document.querySelectorAll('button');
                                    btns.forEach((items)=>{
                                        items.addEventListener('click',(evt)=>{
                                            evt.target.classList.add('activeLoading');
                                        })
                                    })

                                </script>
                                <p class="text-center my-3">or sign in with other accounts?</p>
                                <p class="mt-3 text-center">
                                    Already have an Account <a href="{{route('login')}}" class="text-underline">Sign In</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
