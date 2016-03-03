<div class='wrapper padder' style='margin-top:6px'>
	<h1 class='dirlist'><?php if ($rumah == 'rumah_sendiri') {
    echo 'Berkas Saya';
} else {
    echo $owner;
} ?></h1>
	<h1 class='dirlist' id='onfolder'><?php if ($rumah == 'rumah_sendiri') {
    echo 'dasar';
} else {
    echo $name;
} ?></h1>
	<?php if ($rumah == 'rumah_sendiri') {
    ?>
	<h1 class='dirlist' id='remaining' style='float:right'></h1>
	<?php 
} ?>
	<script src="assets/js/jquery.form.js"></script>
	<script>
		var on_dir = '.';
		var options = {};
		var upload_limit = <?= ini_get('upload_max_filesize') ?>;
		
		function loader() {
			$('div.dirlist').html("<div style='text-align:center; padding:50px;'><img src='assets/img/dir_loader.gif'/></div>");
		}
		
		function update_ontab() {
			var what = on_dir;
			if(what == ".") { what = <?php if ($rumah == 'rumah_sendiri') {
    echo "'dasar'";
} else {
    echo "'".$name."'";
} ?> }
			$('#onfolder').html(what);
		}
		
		function update_remaining() {
			$.ajax({
				url: 'pengguna/sisa',
				success: function(data) {
					var seg = data.split("//");
					$('#remaining').html('Sisa: ' + seg[0]);
					if(seg[1] < upload_limit) {
						upload_limit = seg[1];
					}
				}
			});
		}
		
		function openfolder(folder) {
			loader();
			$.ajax({
				type: "POST",
				url: 'berkas/<?= $rumah ?>/',
				data: { 'target': <?php if ($rumah == 'rumah_sendiri') {
    echo 'folder';
} else {
    echo("'".$key."'");
} ?> },
				success: function(data) {
					$('div.dirlist').html(data);
					on_dir = folder;
					update_ontab();
				}
			});
		}
		
		$(document).ready(function() {
			loader();
			openfolder('.');
			update_remaining();
			
			<?php if ($rumah == 'rumah_sendiri') {
    ?>
			$("#uploadform").submit(function () {
				options = {
					dataType: 'xml',
					type: 'POST',
					url: 'berkas/unggah/',
					data: { 'target': on_dir },
					target: '#output',
					uploadProgress: OnProgress,
					success: afterSuccess,
					complete: afterSuccess,
					resetForm: true
				};
				$("#uploadform").ajaxSubmit(options);
				$("#submit-btn-u").hide();
				return false;
			});
			
			$('#uploader').bind('change', function() {
				for(i in this.files) {
					if(this.files[i].size > upload_limit) {
						alert("Ukuran berkas melebihi batas (" + Math.round((upload_limit / 1024 / 1024) * 10) / 10 + " MB).");
						$('#uploader').val("");
						break;
					}	
				}
				
				$('#statustxt').html("0%");
			});
			
			$("#folderform").submit(function(e) {
				e.preventDefault();
				NProgress.start();
				$.ajax({
					type: "POST",
					data: $(this).serialize(),
					url: 'berkas/map_baru/',
					success: function(data) {
						alert(data);
						openfolder(on_dir);
						NProgress.done();
					}
				});
			});
			<?php 
} ?>
		});
		
		<?php if ($rumah == 'rumah_sendiri') {
    ?>
		function dirlist(what) {
			if(what == 'upload') {
				$('#folder').hide();
				$('#upload').show();
			}
			if(what == 'folder') {
				$('#folder').show();
				$('#upload').hide();
			}
		}
		
		function afterSuccess(r1) {
			$('#progressbar').width('100%');
			if(r1.responseText.length > 0) { alert(r1.responseText); }
			setTimeout(function() { openfolder(on_dir); }, 2000);
			$('#statustxt').html("Berkas telah terunggah.");
			$("#submit-btn-u").fadeIn();
			update_remaining();
		}
		
		function OnProgress(event, position, total, percentComplete) {
			$('#progressbar').width(percentComplete + '%');
			$('#statustxt').html(percentComplete + '%');
		}
		
		function file_del(kanjut) {
			if(NProgress.status != null) { return; }
			var c = confirm("Apakah anda yakin untuk meghapus berkas ini?");		
			if(c == true) {
				NProgress.start();
				$.ajax({
				type: "POST",
				data: { 'target': kanjut },
				url: 'berkas/hapus_berkas/',
				success: function(data) {
					openfolder(on_dir);
					NProgress.done();
					update_remaining();
				}
			});
			}
		}
		
		function file_rename(kanjut, kontol) {
			if(NProgress.status != null) { return; }
			var c = prompt("Nama baru untuk berkas ini:", kontol);
			if(c == kontol) { return; }
			if(c != null) {
				NProgress.start();
				$.ajax({
					type: "POST",
					data: { 'nama_baru': c, 'target': kanjut },
					url: 'berkas/ganti_nama_berkas/',
					success: function(data) {
						openfolder(on_dir);
						NProgress.done();
					}
				});
			}
		}
		
		function file_move(kanjut) {
			if(NProgress.status != null) { return; }
			var c = prompt("Masukkan nama map ke mana berkas ini akan dipindahkan. Jika tidak ada, maka akan dibuat map baru dengan tipe map tertutup.");
			if(c != null) {
				NProgress.start();
				$.ajax({
					type: "POST",
					data: { 'map_baru': c, 'target': kanjut },
					url: 'berkas/berkas_pindah/',
					success: function(data) {
						alert(data);
						openfolder(on_dir);
						NProgress.done();
					}
				});
			}
		}
		
		function folder_del(kanjut) {
			if(NProgress.status != null) { return; }
			var c = confirm("Apakah anda yakin untuk menghapus map ini?");		
			if(c == true) {
				c = confirm("Benarkah? Semua berkas dalam map ini juga akan ikut terhapus! Semua!");		
			}
			if(c == true) {
				NProgress.start();
				$.ajax({
				type: "POST",
				data: { 'target': kanjut },
				url: 'berkas/hapus_map/',
				success: function(data) {
					alert(data);
					openfolder('.');
					NProgress.done();
					update_remaining();
				}
				});
			}
		}
		
		function folder_vis(kanjut) {
			if(NProgress.status != null) { return; }
			NProgress.start();
			$.ajax({
			type: "POST",
			data: { 'target': kanjut },
			url: 'berkas/ganti_map/',
			success: function() {
				openfolder(on_dir);
				NProgress.done();
			}
			});
		}
		
		function folder_rename(kanjut, kontol) {
			if(NProgress.status != null) { return; }
			var c = prompt("Nama baru untuk map ini:", kontol);
			if(c == kontol) { return; }
			if(c != null) {
				NProgress.start();
				$.ajax({
					type: "POST",
					data: { 'nama_baru': c, 'target': kanjut },
					url: 'berkas/ganti_nama_map/',
					success: function(data) {
						openfolder(on_dir);
						NProgress.done();
					}
				});
			}
		}
		<?php 
} ?>
	</script>
	<div class='dirlist'>
	</div>
	<?php if ($rumah == 'rumah_sendiri') {
    ?>
	<div class='dirlistbotans'>
		<a href='javascript:void(0);' onClick="dirlist('upload')">Unggah</a>
		<a href='javascript:void(0);' onClick="dirlist('folder')">Map</a>
	</div>
	<div class='dirlistupload'>
		<div id='upload' align='right'>
			<link rel='stylesheet' href='assets/css/upload.css' />
			<div id="progressbox"><div id="progressbar"></div><div id="statustxt">Tidak ada berkas menunggu untuk diunggah.</div></div>
			<form action="berkas/unggah/" method="post" enctype="multipart/form-data" id="uploadform">
				<input name="file[]" id="uploader" type="file" multiple />
				<input type='submit' id='submit-btn-u' class='rbutton compact' value='Unggah'/>
			</form>
		</div>
		<div id='folder' style='display:none' align='right'>
			<form action="berkas/map_baru/" method="post" id='folderform'>
				<select name='tipe_map' class='clear'><option value='0'>Pribadi</option><option value='1'>Tertutup</option><option value='2'>Terbuka</option></select>
				<input name="nama_map" id="new_folder" type="text" class='clear'/>
				<input type='submit' id='submit-btn-f' style='display:none;'/>
				<a href='javascript:void(0)' onClick="$('#submit-btn-f').click()" class='rbutton compact'>Tambah Map Baru</a>
			</form>
		</div>
	</div>
	<?php 
} ?>
</div>