{{-- バリデーションエラーの場合に表示 --}}
@auth
<!DOCTYPE html>
<html lang="ja">
<head>
    {{-- Meta --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        @hasSection('title')
            TAKE商品管理システム | @yield('title')
        @else
            TAKE商品管理システム
        @endif
    </title>

    {{-- Material Design Icons & Vendor CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">

    {{-- app CSS --}}
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    {{-- Bootstrap Datepicker CSS（CDN版） --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    {{-- Vue --}}
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/vue-cookies@1.7.4/vue-cookies.js"></script>
    <script src="https://unpkg.com/vue-ls"></script>
</head>
<body>
    <div class="container-scroller">

        {{-- ナビバー --}}
        <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="{{url('/')}}">
                    <img src="{{url('/assets/images/logo2.png')}}" alt="logo" />
                </a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-stretch">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="mdi mdi-menu"></span>
                </button>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                            <div class="nav-profile-img">
                                <img src="{{url('/assets/images/logo2.png')}}" alt="image">
                                <span class="availability-status online"></span>
                            </div>
                            <div class="nav-profile-text">
                                <p class="mb-1 text-black">{{ Auth::user()->name }}</p>
                            </div>
                        </a>
                        <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="mdi mdi-logout mr-2 text-primary"></i> Signout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>

        {{-- ページ本体 --}}
        <div class="container-fluid page-body-wrapper">

            {{-- サイドバー --}}
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item nav-profile">
                        <a href="#" class="nav-link">
                            <div class="nav-profile-image">
                                <img src="{{url('/assets/images/logo2.png')}}" alt="profile">
                                <span class="login-status online"></span>
                            </div>
                            <div class="nav-profile-text d-flex flex-column">
                                <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                                <span class="text-secondary text-small">Programmer</span>
                            </div>
                            <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                        </a>
                    </li>

                    {{-- メニュー --}}
                    <li class="nav-item"><a class="nav-link" href="{{url('/')}}"><span class="menu-title">Home</span><i class="mdi mdi-home menu-icon"></i></a></li>
                    <li class="nav-item"><a class="nav-link" href="{{url('/item')}}"><span class="menu-title">商品情報</span><i class="mdi mdi-crosshairs-gps menu-icon"></i></a></li>
                    <li class="nav-item"><a class="nav-link" href="{{url('/addItem')}}"><span class="menu-title">商品登録</span><i class="mdi mdi-contacts menu-icon"></i></a></li>
                    <li class="nav-item"><a class="nav-link" href="{{url('/mercariOrder')}}"><span class="menu-title">mercari注文情報</span><i class="mdi mdi-table-large menu-icon"></i></a></li>
                    <li class="nav-item"><a class="nav-link" href="{{url('/chart')}}"><span class="menu-title">商品別売上チャート</span><i class="mdi mdi-chart-bar menu-icon"></i></a></li>
                </ul>
            </nav>

            {{-- メインコンテンツ --}}
            @yield('content')

        </div>
    </div>

    {{-- jQuery, Popper.js, Bootstrap JS --}}
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    {{-- Bootstrap Datepicker JS（CDN版） --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ja.min.js"></script>

    {{-- Custom JS --}}
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>

</body>
</html>
@endauth
