<?php $CI = &get_instance();
$CI->load->helper('text'); ?>
<html>
<head>
	<title><?php if (isset($title)) {
    echo $title;
} else {
    echo 'BakarMedia';
}?></title>
	<base href='<?php echo base_url(); ?>'>
	<link rel='stylesheet' type='text/css' href='assets/css/default.css' />
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="assets/js/nprogress.js"></script>
	<link rel='stylesheet' href='assets/css/nprogress.css' />
	<link rel="icon" type="image/png" href="assets/img/favicon.ico" />
</head>
<body>
	<noscript>JavaScript pada browser anda mati. Tolong nyalakan, atau beberapa fungsi di situs ini tidak akan berjalan sebagaimana mestinya.</noscript>
	<div class='header'>
		<div class='wrapper'>
			<div class='left'>
				<a href='.'><img class='sitelogo' src='assets/img/header_logo.png'/></a>
			</div>
			<div class='right hlogin'>
				<?php
                    if (($CI->session->userdata('user')) == false) {
                        ?>
				<a href='javascript:void(0)' onClick='show_login()'><button>Masuk</button></a>
				<?php

                    } else {
                        ?>
				<a href='.'><span class='btn'><?= ellipsize($CI->session->userdata('name'), 32, 1) ?></span></a>
				<a href='pengguna/pengaturan'><span class='btn'><img src='assets/img/settings.png' width='24px'/></span></a>
				<a href='pengguna/bantuan'><span class='btn'><img src='assets/img/tulung.png' width='24px'/></span></a>
				<a href='pengguna/keluar'><span class='btn'><img src='assets/img/off.png' width='24px'/></span></a>
				&nbsp;
				<?php

                    }
                ?>
			</div>
		</div>
	</div>
	<div class='dragblacker' style='display:none;'>&nbsp;</div>
	<?php if ($CI->session->userdata('user') === false) {
    ?>
	<div class='login_pop' style='display:none;'>
		<script>
			var disable = false;
			
			function show_login() {
				$('div.dragblacker').fadeIn();
				$('div.login_pop').fadeIn();
			}
			function hide_login() {
				$('div.dragblacker').fadeOut();
				$('div.login_pop').fadeOut();
			}
			function validation_warn(what) {
				if(what === false) { $('div.carek').html('').slideUp(10); }
				else { $('div.carek').append(what + '<br/>').slideDown(); }
			}
			$(document).ready(function() {
				$('#f_login').submit(function(e) {
					NProgress.start();
					validation_warn(false);
					e.preventDefault();
					$.ajax({
						type: "POST",
						url: 'pengguna/masuk',
						data: $(this).serialize(),
						success: function(data) {
							NProgress.done();
							if (data === 'berhasil') {
								location.reload();
							}
							else {
								validation_warn(data);
							}
						}
					});
				});
				
				$('#f_register').submit(function(e) {
					if(disable == true) { return; }
					var clear = true;
					
					NProgress.start();
					e.preventDefault();
					validation_warn(false);
					if($('#f_register input[name=fname]').val().length < 1 || $('#f_register input[name=lname]').val().length < 1) {
						validation_warn("Tolong isi nama pada kedua isian yang disediakan.");
						NProgress.done(); clear = false;
					}
					var check = $('#f_register input[name=email]').val().split("@");
					if(check.length != 2 || check[check.length - 1].split(".").length < 2 || check[check.length - 1].split(".")[1].length < 1) {
						validation_warn("Alamat surel tidak valid.");
						NProgress.done(); clear = false;
					}
					if($('#f_register input[name=pass]').val().length < 6) {
						validation_warn("Kata sandi minimal 6 karakter.");
						NProgress.done(); clear = false;
					}
					if($('#f_register input#tos').is(':checked') == false) {
						validation_warn("Anda harus menyetujui Ketentuan Layanan jika ingin mendaftar.");
						NProgress.done(); clear = false;
					}
					
					if(clear == true) {
						disable = true;
						$.ajax({
							type: "POST",
							url: 'pengguna/daftar',
							data: $(this).serialize(),
							success: function(data) {
								NProgress.done();
								if (data === 'berhasil') {
									location.href = '.';
								}
								else {
									validation_warn(data);
									disable = false;
								}
							}
						});
					}
				});
			});
		</script>
		<div class='top'>
			<center><h4>Silakan masuk, atau daftar jika anda belum memiliki akun.</h4></center>
			<div class='left'>
				<a href='javascript:void(0)' onClick='hide_login()'><img class='close' src='assets/img/close.png'/></a>
				<div class='wrappad'>
					<form id='f_login' method='POST'>
						<input type='text' name='email' placeholder='Alamat surel' />
						<input type='password' name='pass' placeholder='Kata sandi' />
						<input type='checkbox' name='remember' id='remember' /><label for='remember'>Biarkan saya tetap masuk</label>
						<input type='submit' class='rbutton' value='Masuk'/>
					</form>
				</div>
			</div>
			<div class='right'>
				<div class='wrappad'>
					<form id='f_register' method='POST'>
						<input type='text' class='hanbun' name='fname' placeholder='Nama depan' />
						<input type='text' class='hanbun' name='lname' placeholder='Nama belakang' />
						<input type='text' name='email' placeholder='Alamat surel' />
						<input type='password' name='pass' placeholder='Kata sandi' />
						<input type='checkbox' name='agree' id='tos' value='1'/><label for='tos'>Setuju dengan <a href='kebijakan/ketentuan'>Ketentuan Layanan</a></label>
						<input type='submit' class='rbutton' value='Daftar'/>
					</form>
				</div>
			</div>
		</div>
		<div class='carek'></div>
	</div>
	<?php 
} ?>
	<?php
        if (isset($components)) {
            foreach ($components as $component) {
                echo $component;
            }
        }
    ?>
	<div class='footer'>
		<div class='wrapper'>
			<p>&copy;2014 BakarMedia</p>
			<p>
				<a href='kebijakan/ketentuan'>Ketentuan Layanan</a> |
				<a href='kebijakan/privasi'>Kebijakan Privasi</a>
			</p>
		</div>
	</div>
</body>
</html>