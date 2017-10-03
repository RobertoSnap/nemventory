<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://v4-alpha.getbootstrap.com/examples/cover/cover.css" rel="stylesheet">
</head>

<body>
<div id="public" class="site-wrapper">



    <div class="site-wrapper-inner">

        <div class="cover-container" >

            <div class="masthead clearfix">
                <div class="inner">
                    <h3 class="masthead-brand"><a href="/"><b>NEM</b>ventory</a></h3>
                    <nav class="nav nav-masthead">
                        @if (Auth::guest())
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        @else
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <b-dropdown id="ddown1" text="{{ Auth::user()->name }} " class="m-md-2">
                                <b-dropdown-item href="/web">Dashboard</b-dropdown-item>
                                <b-dropdown-item onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</b-dropdown-item>
                            </b-dropdown>
                        @endif
                    </nav>
                </div>
            </div>

            <div class="inner cover">
                @yield('content')
            </div>

            <div class="mastfoot">
                <div class="inner">
                    <p>Powered by <a href="https://nem.io"><b>NEM</b> blockchain</a>, MIT license <a href="https://github.com/RobertoSnap/nemventory"> Github</a>.</p>
                </div>
            </div>

        </div>



    </div>

</div>
<div id="particles"></div>


<!-- Scripts -->
<script src="{{ asset('js/public.js') }}"></script>


</body>
</html>


