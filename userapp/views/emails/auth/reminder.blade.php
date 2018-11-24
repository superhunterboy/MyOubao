<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<p>请点击以下链接完成密码重置：
        <br /><a href="{{ route('reset', $token) }}" target="_blank">{{ route('reset', $token) }}</a>
    </p>
	</body>
</html>
