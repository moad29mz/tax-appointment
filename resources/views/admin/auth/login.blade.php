<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - المديرية العامة للضرائب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h3 {
            color: #2c3e50;
            font-weight: bold;
        }
        .login-header p {
            color: #6c757d;
        }
        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            border-color: #4a6491;
            box-shadow: 0 0 0 0.25rem rgba(74, 100, 145, 0.25);
        }
        .btn-login {
            background: linear-gradient(to right, #2c3e50, #4a6491);
            color: white;
            padding: 12px;
            border-radius: 8px;
            border: none;
            width: 100%;
            font-size: 16px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: linear-gradient(to right, #4a6491, #2c3e50);
            transform: translateY(-2px);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo i {
            font-size: 60px;
            color: #4a6491;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-landmark"></i>
            <h4>المديرية العامة للضرائب</h4>
        </div>
        
        <div class="login-header">
            <h3>تسجيل الدخول</h3>
            <p>الرجاء إدخال بيانات الدخول الخاصة بك</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label">البريد الإلكتروني</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="{{ old('email') }}" required placeholder="admin@tax.gov">
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">كلمة المرور</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" 
                           required placeholder="أدخل كلمة المرور">
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">تذكرني</label>
            </div>

            <button type="submit" class="btn btn-login mb-3">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
            </button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>