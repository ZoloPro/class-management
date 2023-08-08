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
<div class="container-fluid d-flex align-items-center justify-content-center" style="height: 100vh;">
    <!-- Bạn có thể điều chỉnh độ rộng và chiều cao cho card theo ý muốn -->
    <div class="card" style="width: 350px">
        <div class="card-body">
            <h4 class="text-center p-2">Khôi phục mật khẩu</h4>
            <form id="resetPasswordForm" method="post" action="{{ url('/forgot-password') }}">
                @csrf
                <input type="hidden" name="token" value="{{$token}}">
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" name="password" id="password">
                    <div class="invalid-feedback">
                        Mật khẩu phải có tối thiểu 6 kí tự, trong đó có ít nhất 1 chữ hoa, 1 chữ thường, 1 chữ số và 1
                        kí tự đặc biệt.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation">
                    <div class="invalid-feedback">
                        Xác nhận mật khẩu không khớp.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary float-end" id="submitButton">Lưu</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('resetPasswordForm');
        const passwordInput = form.querySelector('#password');
        const confirmPasswordInput = form.querySelector('#password_confirmation');
        const submitButton = form.querySelector('#submitButton');

        passwordInput.addEventListener('input', function () {
            validatePassword(passwordInput.value)
                ? passwordInput.classList.remove('is-invalid')
                : passwordInput.classList.add('is-invalid');
        });

        confirmPasswordInput.addEventListener('input', function () {
            confirmPasswordInput.value === passwordInput.value
                ? confirmPasswordInput.classList.remove('is-invalid')
                : confirmPasswordInput.classList.add('is-invalid');
        });

        form.addEventListener('submit', function (event) {
            if (!validatePassword(passwordInput.value)) {
                event.preventDefault();
                passwordInput.classList.add('is-invalid');
                return false;
            }

            if (passwordInput.value !== confirmPasswordInput.value) {
                event.preventDefault();
                confirmPasswordInput.classList.add('is-invalid');
                return false;
            }
        });

        function validatePassword(password) {
            const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;
            return passwordRegex.test(password);
        }
    });
</script>
</body>
</html>
