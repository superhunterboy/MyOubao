<html>
<head>
    <title>
        @section('title')
        {{ $title or '' }}
        @show{{-- 页面标题 --}}
    </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description')">{{-- 页面描述 --}}
    <meta name="keywords" content="@yield('keywords')" />    {{-- 页面关键词 --}}
</head>
	<frameset rows="40,*" border="0" name="frameset"  frameborder="0" framespacing="0" >
	<frame src="{{route('admin.header')}}" style="border:0;" scrolling="NO" noresize>
	<frameset id="attachucp" cols="250, *">
		<frame name="left" src="{{ route('admin.sidemenu') }}" style="border:0;" noresize>
		<frame name="main" src="{{ route('admin.dashboard') }}" style="border:0;" >
	</frameset>
	</frameset>
    <noframes>
    <body>
        您的浏览器不支持Frameset框架
    </body>
    </noframes>
</html>