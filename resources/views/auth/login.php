<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Warehouse Smart System</title>

  <link href="<?php echo asset('css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      /* Background yang lebih elegan dan profesional */
      background: #f1f5f9;
      background-image:
        radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%),
        radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%),
        radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: "Inter", "Segoe UI", sans-serif;
    }

    .login-card {
      max-width: 400px;
      width: 100%;
      border-radius: 16px;
      padding: 40px;
      background: #ffffff;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      border: none;
    }

    .brand-icon {
      width: 60px;
      height: 60px;
      background: #eff6ff;
      color: #2563eb;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      margin: 0 auto 20px;
    }

    .brand-title {
      font-weight: 800;
      color: #1e293b;
      text-align: center;
      font-size: 1.5rem;
      margin-bottom: 5px;
    }

    .brand-subtitle {
      text-align: center;
      color: #64748b;
      font-size: 0.9rem;
      margin-bottom: 30px;
    }

    /* Styling Input Group agar menyatu */
    .input-group-text {
      background: #f8fafc;
      border-right: none;
      color: #94a3b8;
    }

    .form-control {
      background: #f8fafc;
      border-left: none;
      padding-left: 0;
    }

    .form-control:focus {
      background: #fff;
      box-shadow: none;
      border-color: #cbd5e1;
    }

    /* Efek fokus pada parent input-group */
    .input-group:focus-within .input-group-text,
    .input-group:focus-within .form-control {
      border-color: #2563eb;
      background: #fff;
    }
    .input-group:focus-within .input-group-text {
        color: #2563eb;
    }

    .btn-primary {
      background: #2563eb;
      border: none;
      font-weight: 600;
      padding: 12px;
      border-radius: 8px;
      transition: all 0.3s;
    }

    .btn-primary:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .toggle-password {
      cursor: pointer;
      background: #f8fafc;
      border-left: none;
      color: #94a3b8;
    }
    .toggle-password:hover {
        color: #64748b;
    }

    .small-link {
      text-decoration: none;
      color: #2563eb;
      font-weight: 600;
    }
    .small-link:hover {
        text-decoration: underline;
    }
  </style>
</head>

<body>

  <div class="login-card">

    <div class="brand-icon">
        <i class="fas fa-cubes"></i>
    </div>

    <div class="brand-title">Smart Warehouse</div>
    <div class="brand-subtitle">Please sign in to continue</div>

    <form action="<?php echo url('login') ?>" method="POST" id="formLogin">

      <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token'] ?? '' ?>">

      <div class="mb-3">
        <label class="form-label small fw-bold text-muted">Username</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
            <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label small fw-bold text-muted">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
            <span class="input-group-text toggle-password" id="togglePassword">
                <i class="fas fa-eye"></i>
            </span>
        </div>
      </div>

      <div class="d-grid mb-4">
        <button type="submit" class="btn btn-primary">
            <span class="btn-text">Sign In</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        </button>
      </div>

      <div class="text-center">
        <small class="text-muted">
          Don't have an account?
          <a href="<?php echo url('/register') ?>" class="small-link">Register here</a>
        </small>
      </div>

    </form>
  </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?php echo asset('js/bootstrap.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function() {

    // 1. Fitur Toggle Password (Lihat/Sembunyikan)
    $('#togglePassword').on('click', function() {
        const passwordInput = $('#password');
        const icon = $(this).find('i');

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // 2. Handle Login Submit
    $('#formLogin').submit(function(e) {
        e.preventDefault();

        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var btnText = btn.find('.btn-text');
        var spinner = btn.find('.spinner-border');

        // State Loading: Disable tombol & munculkan spinner
        btn.prop('disabled', true);
        btnText.text('Authenticating...');
        spinner.removeClass('d-none');

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Alert Sukses Cantik
                    Swal.fire({
                        icon: 'success',
                        title: 'Login Successful!',
                        text: 'Redirecting to dashboard...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    // Alert Gagal Cantik
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: response.message,
                        confirmButtonColor: '#2563eb'
                    });
                    resetBtn();
                }
            },
            error: function(xhr) {
                var res = xhr.responseJSON;
                var msg = (res && res.message) ? res.message : 'Server connection failed.';

                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: msg,
                    confirmButtonColor: '#2563eb'
                });
                resetBtn();
            }
        });

        function resetBtn() {
            btn.prop('disabled', false);
            btnText.text('Sign In');
            spinner.addClass('d-none');
        }
    });
});
</script>
</body>
</html>