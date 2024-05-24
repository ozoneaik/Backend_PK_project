<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <title>Document</title>
</head>
<body>
<div class='hold-transition login-page'>
<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">


            @if ($message = Session::get('error'))

                <div class="alert alert-danger alert-block">

                    <button type="button" class="close" data-dismiss="alert">×</button>

                    <strong>{{ $message }}</strong>

                </div>
            @endif



            <div class='bg-dark'>
                <img src='{{asset('images/Login.webp')}}' alt="" width="100%"/>
            </div>
            <p class="login-box-msg text-dark text-bold mt-2" style="font-size: 30px">เข้าสู่ระบบ</p>

            <form method="POST" class="mt-8 space-y-6" action={{route('signup')}}>
                @csrf
                @method('POST')
                <div class="form-group mb-3">
                    <input type="text" class="form-control" placeholder="รหัสพนักงาน" name="emp_no"/>
                </div>
                <div class="form-group mb-3">
                    <input type="text" class="form-control" placeholder="ชื่อพนักงาน" name="emp_name"/>
                </div>
                <div class="form-group mb-3">
                    <select name="emp_role" id="emp_role" class="form-control">
                        <option value="" selected disabled>สิทธิ์พนักงาน</option>
                        <option value="admin">admin</option>
                        <option value="key_staff">key_staff</option>
                        <option value="staff">staff</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" placeholder="ชื่อในการเข้าสู่ระบบ" class="form-control" name="name">
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="รหัสผ่าน" name="password"/>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="ยืนยันรหัสผ่าน" name="password_confirmation"/>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <select name="emp_status" id="emp_status" class="form-control">
                        <option selected value="" disabled>สถานะการเข้าใช้งานระบบ</option>
                        <option value="activated">activated</option>
                        <option value="deactivated">deactivated</option>
                    </select>
                </div>
                <div class="row">
                    <div class="w-100">
                        <button type="submit" class="btn btn-primary btn-block">ลงทะเบียน</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
</div>
</body>
</html>
