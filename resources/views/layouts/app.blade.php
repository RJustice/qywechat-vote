<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title> @if(isset($vote)) {{ $vote->$title }} @else 民意调研 @endif </title>

    <!-- Fonts -->
    <!-- <link href="http://apps.bdimg.com/libs/fontawesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'> -->
    <!-- <link href="http://fonts.useso.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'> -->

    <!-- Styles -->
    <link href="http://apps.bdimg.com/libs/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
    <div style='margin:0 auto;width:0px;height:0px;overflow:hidden;'><img src="{{ asset('style/img/design_and_hand_made_notebook_128px_1199814_easyicon.net.png') }}" width='300'></div>
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                {{--<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>--}}

                <!-- Branding Image -->
                <a class="navbar-brand" href="@if (Auth::guest()) {{ route('vlist') }} @else {{ url('') }} @endif">
                    月度评测调研
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- JavaScripts -->
    <script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="http://apps.bdimg.com/libs/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    @yield('js')
</body>
</html>
