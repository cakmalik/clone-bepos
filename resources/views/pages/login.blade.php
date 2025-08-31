<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }} - Login </title>

    <style>
        #error-message {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background-color: #f44336;
            color: white;
            text-align: center;
        }

        #error-message p {
            margin: 0;
        }

        @keyframes countdown {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        #timer {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #184eaa;
            color: white;
            text-align: center;
            line-height: 20px;
            margin-left: 5px;
            animation-name: countdown;
            animation-duration: 10s;
            animation-timing-function: linear;
            animation-fill-mode: forwards;
        }
    </style>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="border-top-wide border-primary d-flex flex-column">
    <div class="row no-gutters">
        <div class="col-md-8  d-none d-md-block">
            <div class="d-flex justify-content-center align-items-center"
                style="height: 100vh; background-color:#E5E5E5">
                <img src="{{ asset('img/illustration.svg') }}" alt="">
            </div>
        </div>
        <div class="col-md-4 col-sm-12" style="background-color:">
            <div class="d-flex align-items-center" style="height: 100vh;">
                <div class="mx-4 mx-md-5" style="width: 100%">
                    {{-- @if ($message = Session::get('error'))
                        <x-alert level="danger" message="{{ $message }}" />
                    @elseif($message = Session::get('success'))
                        <x-alert level="success" message="{{ $message }}" />
                    @endif --}}
                    <div class="col mb-2">

                    </div>
                    <div>
                        <h1 class="mb-1" style="font-size: 30px">Selamat Datang</h1>
                        <p class="fs-3">Akses sistem kasir Anda dan mulai kelola transaksi serta data bisnis dengan
                            lebih praktis.</p>
                    </div>
                    <form action="{{ route('post.login') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="col mt-4">
                            <div class="form-group mb-3">
                                <div class="label mb-1">ID Pengguna</div>
                                <input name="username" type="text" class="form-control"
                                    placeholder="Masukkan ID pengguna" style="border-radius: 12px">
                            </div>
                            <div class="form-group mb-3">
                                <div class="label mb-1">Kata Sandi</div>
                                <input name="password" type="password" class="form-control"
                                    placeholder="Masukkan kata sandi" style="border-radius: 12px">
                            </div>
                            <div class="form-group my-3">
                                <div class="d-flex justify-content-between">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember-me">
                                        <label class="form-check-label" for="remember-me">Ingat saya</label>
                                    </div>
                                    {{-- <a href="#" class="forgot-password-link" style="color: #E8603D">Forgot password?</a> --}}
                                </div>
                            </div>

                            <div class="btn-container">
                                <button type="submit" class="btn btn-primary w-100">Masuk</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
