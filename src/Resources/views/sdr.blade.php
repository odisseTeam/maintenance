<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page_title')</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <meta name="csrf_token" content="{{ csrf_token() }}">
    <script> window.Laravel = {csrfToken: '{{csrf_token()}}'}</script>
    <link rel="icon" href="{{ URL::asset("img/favicon/".session("favicon")) }}" alt="logo icon">


    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('resources/bootstrap/css/bootstrap.min.css') }}">
{{--    <link rel="stylesheet" href="{{ asset('resources/jquery-ui/jquery-ui.css') }}">--}}
    <!-- Font Awesome -->
{{--    <link rel="stylesheet" href="{{ asset('resources/font-awesome/css/font-awesome.min.css') }}">--}}
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('resources/Ionicons/css/ionicons.min.css') }}">

    <link rel="stylesheet" href="{{ asset('resources/odisse-icons/odisse-icons.css') }}">

    <link href="{{ asset('resources/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('resources/odisse/odisse.css') }}">

    <script>
        window._date_format = '{{\App\SLP\Formatter\SystemDateFormats::getDateFormatJavascript()}}'
        window._date_time_format = '{{\App\SLP\Formatter\SystemDateFormats::getDateTimeFormatJavascript()}}'

        window._date_format_moment = '{{\App\SLP\Formatter\SystemDateFormats::getDateFormatMoment()}}'
        window._date_time_format_moment = '{{\App\SLP\Formatter\SystemDateFormats::getDateTimeFormatMoment()}}'

        window._date_format_vue = '{{\App\SLP\Formatter\SystemDateFormats::getDateFormatVue()}}'
        window._date_time_format_vue = '{{\App\SLP\Formatter\SystemDateFormats::getDateTimeFormatVue()}}'
    </script>

    <link href="{{asset('resources/fa/css/all.css')}}" rel="stylesheet" />
    <script src="{{asset('resources/fa/js/all.js')}}" data-auto-replace-svg="nest"></script>

@yield('css')

<!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('resources/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet"
          href="{{ asset( config('app.skin_version', 'resources/dist/css/skins/_all-skins.min.css')) }}">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="{{ asset('resources/dist/js/html5shiv.min.js') }}"></script>
  <script src="{{ asset('resources/dist/js/respond.min.js') }}"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<!-- ADD THE CLASS sidebar-collapse TO HIDE THE SIDEBAR PRIOR TO LOADING THE SITE -->
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="/" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><img style="width:100%" src="{{ URL::asset("img/logos/".session("small_logo")) }}"/></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><img style="width:100%"
                                       src="{{ URL::asset("img/logos/".session("large_logo")) }}"/></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top dark-background">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">{{__('layout_sdr.toggle_navigation')}}</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ asset('resources/dist/img/avatar.jpeg') }}" class="user-image"
                                 alt="User Image">
                            <span
                                class="hidden-xs">{{JWTAuth::user()->first_name}} {{JWTAuth::user()->last_name}}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ asset('resources/dist/img/avatar.jpeg') }}" class="img-circle"
                                     alt="User Image">

                                <p>
                                    {{JWTAuth::user()->first_name}} {{JWTAuth::user()->last_name}}
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="/profile" class="btn btn-default btn-flat">{{__('layout_sdr.profile')}}</a>
                                </div>
                                <div class="pull-right">
                                    <a class="btn btn-default btn-flat" href="{{ '/logout' }}"
                                       onclick="event.preventDefault();
                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ '/logout' }}" method="POST"
                                          style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>

                    @if( isset($wiki_link))
                    <li>
                        <a id="wiki_link" href="{{$wiki_link}}" target=”_blank” class="" style="padding-left:1%;margin-top:-5%"><i class="fa-solid fa-info-circle fa-2x" style="display: inline-block;border-radius: 50%;font-size:1.3em;color:#0275d8;fill:white;outline: 1px solid white;background:white">
                            </i></a>
                    </li>
                    @endif


                </ul>
            </div>
        </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar dark-background">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="{{ asset('resources/dist/img/avatar.jpeg') }}" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p>{{JWTAuth::user()->first_name}} {{JWTAuth::user()->last_name}}</p>
                    <a href="#"><i class="fa-solid fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="treeview">
                    <a href="#">
                        <i class="fa-solid fa-gauge"></i> <span>{{__('layout_sdr.dashboard')}}</span>
                        <span class="pull-right-container">
              <i class="fa-solid fa-angle-left pull-right"></i>
            </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="/"><i class="fa-solid fa-house-building"></i>{{__('layout_sdr.room_view')}}</a></li>
                    </ul>
                </li>
                @if(Sentinel::check())
                    @php $menu = config('sdr.Menu'); $menuIcons = config('sdr.MenuIcons');  $OneLevelMenu = config('sdr.OneLevelMenu'); @endphp
                    @foreach( $menu as $groupName => $menugroup )
                        @php $showMenu = false; @endphp

                        @foreach( $menugroup as $menuitem => $menu )
                            @if(in_array($menuitem, $UserMenu))
                                @php $showMenu = true; @endphp
                            @endif
                        @endforeach

                        @if($showMenu)
                            <li class="treeview">
                                <a href="#">

                                    <i class="{{$menuIcons[$groupName]}}"></i>

                                    <span>{{$groupName}}</span>
                                    <span class="pull-right-container">
                                      <i class="fa-solid fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">

                                    @foreach( $menugroup as $menuitem => $menu )
                                        @if(in_array($menuitem, $UserMenu))
                                            <li><a href="{{$menu['link']}}"><i
                                                        class="{{$menu['icon']}}"></i> {{$menu['text']}}</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach



                    @foreach( $OneLevelMenu as $link => $menu )
                        @if(in_array($link, $UserMenu))
                            <li>
                                <a href="{{$menu['link']}}">
                                    <i class="{{$menu['icon']}}"></i>
                                    <span>{{$menu['text']}}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper white-background">
        <!-- Content Header (Page header) -->

    @yield('pageheader')

    <!-- Main content -->
    @yield('content')
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="{{ asset('resources/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('resources/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/odisse.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('resources/bootstrap/js/bootstrap.min.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('resources/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>


<script type="text/javascript" src="{{ asset('resources/moment/min/moment-with-locales.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('resources/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>


<!-- FastClick -->
<script src="{{ asset('resources/fastclick/lib/fastclick.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('resources/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('resources/dist/js/demo.js') }}"></script>

<script src="{{ asset('/js/utility.js') }}"></script>

<script src="{{ asset('/js/moment.min.js') }}"></script>

<script src="https://js.pusher.com/5.0/pusher.min.js"></script>
@if(!empty(env('BROADCAST_DRIVER')) and env('BROADCAST_DRIVER') == 'pusher')
    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('{{env('PUSHER_APP_KEY')}}', {
            cluster: '{{env('PUSHER_APP_CLUSTER')}}',
            forceTLS: true
        });
    </script>
@endif
@yield('script')

{{--<script src="{{ asset('js/app.js') }}"></script>--}}
<script>
    setTimeout(
        function () {
            $('.alert').hide('blind', {}, 500);
        }
        , 6000
    )
</script>

{{--
<script>
    $("#wiki_link").attr("href", "http://www.google.com/");
</script> --}}

</body>
</html>
