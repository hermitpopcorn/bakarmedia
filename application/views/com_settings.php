<script>
	function map_link() {
		if($('#rootmapselect').val() == 2) {
			$('#rootlink').css('visibility', 'visible');
		} else {
			$('#rootlink').css('visibility', 'hidden');
		}
	}
	
	function formval() {
		clear = true;
		if($('input[name=pass]').val().length < 1) {
			formred('a');
			clear = false;
		}
		if($('input[name=newpass1]').val() != $('input[name=newpass2]').val()) {
			formred('b');
			clear = false;
		}
		if($('input[name=fname]').val().length < 1 || $('input[name=lname]').val().length < 1) {
			formred('c');
			clear = false;
		}
		
		return clear;
	}
	
	function formred(list) {
		for (var i = 0, len = list.length; i < len; i++) {
			$('div.minipadder#' + list.charAt(i)).css('background', '#FDD');
			if(list.charAt(i) == 'a') { $('div.minipadder#a help').html("Kata sandi salah."); }
			if(list.charAt(i) == 'b') { $('div.minipadder#b help').html("Pastikan kedua kata sandi di atas sama."); }
			if(list.charAt(i) == 'c') { $('div.minipadder#c help').html("Nama depan maupun nama belakang tidak boleh kosong."); }
		}
	}
	
	function formreset() {
		for (var i = 0, len = list.length; i < len; i++) {
			$('div.minipadder#' + list.charAt(i)).css('background', '#FFF');
		}
		if(list.charAt(i) == 'a') { $('div.minipadder#a help').html("Untuk mengubah pengaturan akun, anda perlu memasukkan kata sandi anda yang sekarang."); }
		if(list.charAt(i) == 'b') { $('div.minipadder#b help').html("Biarkan kosong jika anda tidak ingin mengubahnya."); }
		if(list.charAt(i) == 'c') { $('div.minipadder#c help').html("<a href='pengguna/bantuan#akun'>Di mana nama ini akan muncul?</a>"); }
	}
	
	$(document).ready(function() {
		$('#rootmapselect').change(function() {
			map_link();
		});
		
		$('#patform').submit(function(e) {
			e.preventDefault();
			
			if(formval() == false) { return; }
			
			NProgress.start();
			$('#ss').fadeOut();
			$('#spin').fadeIn();
			$.ajax({
				url: 'pengguna/perbarui_pengaturan',
				data: $(this).serialize(),
				type: "POST",
				success: function(data) {
					NProgress.done();
					$('#spin').css('display', 'none');
					if(data == 'berhasil') {
						$('#success').fadeIn(0);
						setTimeout(function() {$('#success').fadeOut();}, 2000);
						formreset();
					} else {
						formred(data);
					}
				}
			});
		});
	});
</script>
<div class='wrapper padder'>
	<h1>Pengaturan</h1>
	<div class='pengaturankanan'>
		<h2><?= $email ?></h2>
		<b>Total Ukuran Berkas: <?= $TotalSize ?></b><br/>
		<b>Sisa: <?= $remaining ?></b>
	</div>
	<div class='pengaturankiri'>
		<form action='pengguna/perbarui_aturan' method='POST' id='patform'>
			<div class='minipadder' id='a'>
				<input type='password' name='pass' placeholder='Kata sandi sekarang'/><br/>
				<help>Untuk mengubah pengaturan akun, anda perlu memasukkan kata sandi anda yang sekarang.</help>
			</div>
			<hr/>
			<div class='minipadder' id='b'>
				<h3>Kata Sandi Baru</h3>
				<input type='password' name='newpass1' placeholder='Kata sandi baru'/><br/>
				<input type='password' name='newpass2' placeholder='Kata sandi baru, sekali lagi'/><br/>
				<help>Biarkan kosong jika anda tidak ingin mengubahnya.</help>
			</div>
			<hr/>
			<div class='minipadder' id='c'>
				<h3>Nama</h3>
				<input type='text' name='fname' value="<?= $fname ?>" placeholder='Nama depan'/><br/>
				<input type='text' name='lname' value="<?= $lname ?>" placeholder='Nama belakang'/><br/>
				<help><a href='pengguna/bantuan#akun'>Di mana nama ini akan muncul?</a></help>
			</div>
			<hr/>
			<div class='minipadder' id='d'>
				<h3>Sifat Map Dasar</h3>
				<select name='rootmap' id='rootmapselect'>
					<option value='0' <?php if ($visibility == 0) {
    echo 'selected';
} ?>>Pribadi</option>
					<option value='1' <?php if ($visibility == 1) {
    echo 'selected';
} ?>>Tertutup</option>
					<option value='2' <?php if ($visibility == 2) {
    echo 'selected';
} ?>>Publik</option>
				</select>
				<help id='rootlink' <?php if ($visibility != 2) {
    ?>style='visibility:hidden'<?php 
} ?>>Pranala map dasar anda: <a href='<?= base_url(); ?>berkas/map/<?= $key ?>'><?= base_url(); ?>berkas/map/<?= $key ?></a></help>
			</div>
			<hr/>
			<div align='right'><img id='spin' style='display:none; vertical-align:middle;' width='16px' height='16px' src='assets/img/ajax-arrow.gif'/><img id='success' style='display:none; vertical-align:middle;' width='16px' height='16px' src='assets/img/ok.png'/> <input type='submit' value='Simpan'/></div>
		</form>
	</div>
</div>