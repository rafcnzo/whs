<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Smart Warehouse</title>

  <link href="<?php echo asset('css/bootstrap.min.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    body {
      background: #f1f5f9;
      background-image:
        radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%),
        radial-gradient(at 50% 0%, hsla(225,39%,30%,1) 0, transparent 50%),
        radial-gradient(at 100% 0%, hsla(339,49%,30%,1) 0, transparent 50%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: "Inter", system-ui, -apple-system, sans-serif;
      padding: 20px;
    }

    .register-card {
      max-width: 900px;
      width: 100%;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      background: #ffffff;
      display: flex;
    }

    .left-panel {
      background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
      padding: 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      color: white;
      width: 40%;
    }

    .right-panel {
      padding: 40px;
      width: 60%;
    }

    .brand-title {
      font-size: 2rem;
      font-weight: 800;
      margin-bottom: 10px;
    }

    .form-control {
      background: #f8fafc;
      border: 1px solid #cbd5e1;
      padding: 10px 15px;
    }

    .form-control:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
      background: #fff;
    }

    .btn-primary {
      background: #2563eb;
      border: none;
      padding: 12px;
      font-weight: 600;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .btn-primary:hover {
      background: #1d4ed8;
      transform: translateY(-1px);
    }

    /* Responsif untuk Mobile */
    @media (max-width: 768px) {
      .register-card { flex-direction: column; }
      .left-panel { display: none; } /* Sembunyikan panel kiri di HP */
      .right-panel { width: 100%; padding: 30px; }
    }
  </style>
</head>
<body>

  <div class="register-card">

    <div class="left-panel">
      <div class="mb-4">
        <i class="fas fa-cubes fa-3x text-primary"></i>
      </div>
      <h2 class="brand-title">Smart WHS</h2>
      <p class="text-gray-300">Bergabunglah bersama kami untuk manajemen gudang yang lebih efisien, akurat, dan terintegrasi secara realtime.</p>
      <div class="mt-4">
        <small class="d-block mb-2"><i class="fas fa-check-circle text-success me-2"></i> Realtime Stock</small>
        <small class="d-block mb-2"><i class="fas fa-check-circle text-success me-2"></i> Easy Tracking</small>
        <small class="d-block"><i class="fas fa-check-circle text-success me-2"></i> Comprehensive Reports</small>
      </div>
    </div>

    <div class="right-panel">
      <h3 class="fw-bold mb-1 text-dark">Buat Akun Baru</h3>
      <p class="text-muted mb-4 small">Lengkapi data di bawah ini untuk mendaftar.</p>

      <form id="formRegister">
        <?php // Ganti dengan CSRF manual jika PHP Native: <input type="hidden" name="_token" ...> ?>

        <div class="mb-3">
          <label class="form-label small fw-bold">Username</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
            <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="Pilih username unik" required autocomplete="off">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" name="password" id="pass" class="form-control border-start-0 ps-0" placeholder="Min. 6 karakter" required minlength="6">
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label small fw-bold">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-check-double text-muted"></i></span>
                <input type="password" name="password_confirm" id="passConf" class="form-control border-start-0 ps-0" placeholder="Ulangi password" required>
            </div>
          </div>
        </div>

        <div id="passError" class="text-danger small mb-3 d-none">
            <i class="fas fa-exclamation-circle"></i> Password tidak sama!
        </div>

        <div class="d-grid mt-4">
          <button type="submit" class="btn btn-primary">
            <span class="btn-text">Daftar Sekarang</span>
            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
          </button>
        </div>

        <div class="text-center mt-4">
          <small class="text-muted">
            Sudah punya akun?
            <a href="<?php echo url('login') ?>" class="text-decoration-none fw-bold text-primary">Login di sini</a>
          </small>
        </div>
      </form>
    </div>

  </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?php echo asset('js/bootstrap.min.js') ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // Validasi Password Match Realtime
    $('#pass, #passConf').on('keyup', function() {
        const p1 = $('#pass').val();
        const p2 = $('#passConf').val();

        if(p2.length > 0 && p1 !== p2) {
            $('#passError').removeClass('d-none');
            $('#passConf').addClass('is-invalid');
        } else {
            $('#passError').addClass('d-none');
            $('#passConf').removeClass('is-invalid');
        }
    });

    $('#formRegister').on('submit', function(e) {
        e.preventDefault();

        // Validasi Akhir sebelum kirim
        const p1 = $('#pass').val();
        const p2 = $('#passConf').val();
        if(p1 !== p2){
            Swal.fire('Error', 'Konfirmasi password tidak sesuai!', 'error');
            return;
        }

        const btn = $(this).find('button[type="submit"]');
        const btnText = btn.find('.btn-text');
        const spinner = btn.find('.spinner-border');

        // Loading State
        btn.prop('disabled', true);
        btnText.text('Mendaftarkan...');
        spinner.removeClass('d-none');

        $.ajax({
            url: '<?php echo url('register') ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        text: 'Anda akan diarahkan ke halaman login.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                    resetBtn();
                }
            },
            error: function(xhr) {
                let msg = 'Terjadi kesalahan sistem!';
                try {
                    const err = JSON.parse(xhr.responseText);
                    msg = err.message || msg;
                } catch(e) {}

                Swal.fire('Oops...', msg, 'error');
                resetBtn();
            }
        });

        function resetBtn() {
            btn.prop('disabled', false);
            btnText.text('Daftar Sekarang');
            spinner.addClass('d-none');
        }
    });
});
</script>

</body>
</html>