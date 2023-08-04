<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            /*width: 21cm;*/
            font-family: 'dejavu serif', sans-serif;
            font-size: 12px
        }

        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .float-left {
            float: left;
        }

        .float-right {
            float: right;
        }

        .text-center {
            text-align: center;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        .mt-40 {
            margin-top: 40px;
        }

        .w-100 {
            width: 100%;
        }
    </style>
    <title>Document</title>
</head>
<body>
<header class="header">
    <div class="float-left">
        <p class="text-center">UBND THÀNH PHỐ HỒ CHÍ MINH</p>
        <p class="text-center font-weight-bold">TRƯỜNG ĐẠI HỌC SÀI GÒN</p>
    </div>
    <div class="float-right">
        <p class="text-center font-weight-bold">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
        <p class="text-center font-weight-bold">Độc lập - Tự do - Hạnh phúc</p>
    </div>
</header>
<div class="clear">
    <p class="text-center font-weight-bold">BẢNG ĐIỂM TỔNG KẾT MÔN HỌC</p>
    <div>
        <p class="">Năm học: </p>
        <p class="">Học kỳ: </p>
        <p class="">Môn học: {{$classroom->term->termName}}</p>
        <p class="">Lớp: {{$classroom->id}}</p>
        <p class="">Giáo viên: {{$lecturer->fullname}}</p>
        <p class="">Số lượng sinh viên: {{$studentQty}}</p>
    </div>
    <div>
        <table class="table w-100">
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Mã sinh viên</th>
                <th rowspan="2">Họ và lót</th>
                <th rowspan="2">Tên</th>
                <th rowspan="2">Điểm CC</th>
                <th colspan="3">Điểm hệ số 1</th>
                <th colspan="2">Điểm hệ số 2</th>
                <th rowspan="2">Điểm thi</th>
                <th rowspan="2">Điểm TB</th>
            </tr>
            <tr>
                <th>Điểm 1</th>
                <th>Điểm 2</th>
                <th>Điểm 3</th>
                <th>Điểm 1</th>
                <th>Điểm 2</th>
            </tr>
            @foreach($gradeList as $student)
            <tr>
                <td>{{$loop->index + 1}}</td>
                <td>{{$student['code']}}</td>
                <td>{{$student['famMidName']}}</td>
                <td>{{$student['name']}}</td>
                <td class="text-center">{{$student['attendance']}}</td>
                <td class="text-center">{{$student['coefficient1Exam1']}}</td>
                <td class="text-center">{{$student['coefficient1Exam2']}}</td>
                <td class="text-center">{{$student['coefficient1Exam3']}}</td>
                <td class="text-center">{{$student['coefficient2Exam1']}}</td>
                <td class="text-center">{{$student['coefficient2Exam2']}}</td>
                <td class="text-center">{{$student['exam']}}</td>
                <td class="text-center">{{$student['final']}}</td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="mt-40">
        <table class="w-100">
            <tr>
                <th>Điểm TB</th>
                <th>A (8.5-10)</th>
                <th>B (7.0-8.4)</th>
                <th>C (5.5-6.9)</th>
                <th>D (4.0-5.4)</th>
                <th>F (Dưới 4.0)</th>
            </tr>
            <tr class="text-center">
                <th>Số lượng</th>
                <td>
                    @isset($statistical['a'])
                    {{$statistical['a']['quantity']}} ({{$statistical['a']['percent']}}%)
                    @endisset
                </td>
                <td>
                    @isset($statistical['b'])
                    {{$statistical['b']['quantity']}} ({{$statistical['a']['percent']}}%)
                    @endisset
                </td>
                <td>
                    @isset($statistical['c'])
                    {{$statistical['c']['quantity']}} ({{$statistical['a']['percent']}}%)
                    @endisset
                </td>
                <td>
                    @isset($statistical['d'])
                    {{$statistical['d']['quantity']}} ({{$statistical['a']['percent']}}%)
                    @endisset
                </td>
                <td>
                    @isset($statistical['f'])
                    {{$statistical['f']['quantity']}} ({{$statistical['a']['percent']}}%)
                    @endisset
                </td>

            </tr>
        </table>
    </div>
    <footer class="mt-40">
        <div class="float-right">
            <div class="text-center">
                <p>TP.Hồ Chí Minh, ngày {{$date['date']}} tháng {{$date['month']}} năm {{$date['year']}}</p>
                <p class="font-weight-bold">Giảng viên</p>
            </div>
        </div>
        <div class="clear"></div>
    </footer>
</div>
</body>
</html>
