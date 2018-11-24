<!DOCTYPE html>
<html>
  <head>
    <title>
    	@section('title')
    	@show
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')" />
    <!--[if lt IE 9]>
        <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.min.js"></script>
        <script src="http://cdn.bootcss.com/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    @section ('styles')
      {{ style('bootstrap-3.0.3')}}
      {{ style('main' )}}
      {{ style('ui' )}}

    @show


  </head>
  <body class="body">

  		@yield('body')



    @section('javascripts')
      {{ script('jquery-1.10.2') }}
      {{ script('bootstrap-3.0.3') }}
      {{ script('md5') }}
    @show

    @section('js-code')

    @show

    @section('end')

    @show
  </body>
</html>