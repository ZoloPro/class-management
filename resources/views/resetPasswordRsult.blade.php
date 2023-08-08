<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
</head>
<body style="background-color: #f2f2f2">
<div class="container-fluid d-flex d-flex align-items-center justify-content-center" style="height: 100vh;">
    <!-- Bạn có thể điều chỉnh độ rộng và chiều cao cho card theo ý muốn -->
    <div class="card p-4" style="width: 300px;">
        <div class="card-body">
            <!-- Dòng chữ thông báo của bạn -->
            <div class="text-center p-2">
                @if($success)
                <img src="{{URL::asset('/image/success.png')}}" style="height: 7rem"/>
                @else
                <img src="{{URL::asset('/image/error.png')}}" style="height: 7rem"/>
                @endif
            </div>
            <h2 class="card-title text-center mt-2">Thông báo</h2>
            <p class="text-center mt-1">{{$message}}</p>
        </div>
    </div>
</div>
</body>
</html>
