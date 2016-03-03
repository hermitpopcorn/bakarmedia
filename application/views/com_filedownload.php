<div class='padder wrapper center'>
	<h1><?= $filedata['filename'] ?></h1>
	<p>Ukuran berkas: <?= $filedata['filesize'] ?></p>
	<p>Tanggal diunggah: <?= $filedata['uploaddate'] ?></p>
	<br/>
	<p id='g'>Mempersiapkan berkas...</p>
	<script>
		$(document).ready(function() {
			setTimeout(function() {
				$.ajax({
					url: 'berkas/hijau/<?= $filedata['greencode'] ?>',
					success: function(data) {
						if(data == 'siap') {
							$('p#g').fadeOut(0);
							$('p#dl').fadeIn();
						}
					}
				})
			}, 2000);
		});
	</script>
	<p id='dl' style='display:none'><a href='berkas/ambil/<?= $filedata['id'] ?>' class='rbutton'>Unduh</a></p>
</div>