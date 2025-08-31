<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' | ' : '' }}{{ config('app.name') }}</title>
    {{-- icon --}}
    <link rel="icon" href="{{ asset('images/logo-kashir-bg.png') }}" type="image/x-icon">
    <!-- CSS files -->
    <link href="{{ asset('dist/css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-flags.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-payments.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/tabler-vendors.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/demo.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/demo.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('dist/css/custom.css') }}" rel="stylesheet" />
    <link href="{{ asset('/dist/css/datatables.min.css') }}" rel="stylesheet" />
    <!-- Include daterangepicker.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.6/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://kit.fontawesome.com/9d5759d939.js" crossorigin="anonymous"></script>
    @stack('styles')
    @livewireStyles

    <style>
        /* Add this CSS for the loader styling */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

</head>

<body>
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        <div class="page-wrapper">
            @yield('page')
            @include('partials.footer')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk menampilkan page loader
            function showLoader() {
                $(".page-loader").fadeIn();
            }

            // Fungsi untuk menyembunyikan page loader
            function hideLoader() {
                $(".page-loader").fadeOut();
            }

            // Tampilkan page loader saat dokumen siap
            showLoader();

            // Menggunakan event window.onload untuk menghilangkan page loader setelah seluruh halaman selesai dimuat
            window.onload = function() {
                hideLoader();
            };
        });

        // const Toast = Swal.mixin({
        //     toast: true,
        //     position: 'top-end',
        //     showConfirmButton: false,
        //     timer: 3000,
        //     timerProgressBar: true,
        //     didOpen: (toast) => {
        //         toast.addEventListener('mouseenter', Swal.stopTimer)
        //         toast.addEventListener('mouseleave', Swal.resumeTimer)
        //     }
        // })
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <script src="{{ asset('/dist/libs/nouislider/dist/nouislider.min.js') }}"></script>
    <script src="{{ asset('/dist/libs/litepicker/dist/litepicker.js') }}"></script>
    <script src="{{ asset('/dist/libs/tom-select/dist/js/tom-select.base.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tom-select/2.1.0/esm/tom-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.19/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('/dist/js/custom.js') }}"></script>
    <!-- Libs JS -->
    <script src="{{ asset('dist/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <!-- Tabler Core -->
    <script src="{{ asset('dist/js/tabler.min.js') }}"></script>
    <script src="{{ asset('dist/js/demo.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/dist/js/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Include moment.js -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- Include daterangepicker.js -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- countUp --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.6.2/countUp.umd.js"></script>

    <script>
        $(document).ready(function() {
            $('.table:not(.no-datatable)').DataTable()
            $(".alert").fadeTo(5000, 0).slideUp(500, function() {
                $(this).remove();
            });

        });

        function iframeLoaded() {
            $('#loadingProgress').html(proses(0));
        }


        function proses(action) {
            let htmlData = '';
            if (action == 1) {
                htmlData =
                    '<div class="progress progress-sm"><div class="progress-bar progress-bar-indeterminate"></div></div>';
            }
            return htmlData;
        }

        function deleteData(url) {
            console.log(url);
            Swal.fire({
                text: 'Apakah kamu yakin menghapus data ini ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {
                            '_method': 'DELETE',
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            Swal.fire('Saved!', data.message, 'success').then(function() {
                                $('#dataTable').DataTable().ajax.reload();
                            });
                        },
                        error: function(data) {
                            Swal.fire('Upss!', data.responseJSON.message, 'error');
                        }
                    })
                } else {
                    Swal.fire('Data tidak jadi hapus', '', 'info')
                }
            });
        };

        function number(input) {
            return input.startsWith('34') || input.startsWith('37') ?
                '999999999999999' : '9999999999999999'
        }

        function formatNumber(num) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2
            }).format(num);
        }

        function formatNumberRound(num) {
            console.log(Math.round(num).toLocaleString('id-ID'), 'cek cek');
            return Math.round(num).toLocaleString('id-ID')
        }
    </script>


    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    </script>

    @if (session('success'))
        <script>
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        </script>
    @endif

    @stack('scripts')
    @livewireScripts
    @stack('js')
    
    @include('partials.confirm-leave-modal')

</body>

</html>
