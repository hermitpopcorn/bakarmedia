<div class='padder wrapper'>
	<?php if (isset($msg)) {
    ?>
	<div class='message'>Terima kasih atas laporannya. Laporan ini akan kami proses secepatnya.</div>
	<?php 
} ?>
	<form id='lapor' method='POST' action='berkas/lapor/'>
		<label for='l1'><b>Kode berkas</b></label><br/>
		<input name='code' id='l1' type='text' value='<?= $code ?>' maxlength='32' style='min-width:500px'/><br/>
		
		<label for='l2'><b>Macam pelanggaran</b></label><br/>
		<select id='l2' name='type' style='min-width:500px'>
			<option value='inf1'>Berkas ini mengandung konten tidak pantas</option>
			<option value='inf2'>Berkas ini melanggar hak cipta</option>
			<option value='inf3'>Berkas ini homo banget</option>
		</select><br/>
		
		<label for='l3' style='vertical-align:top'><b>Lebih jelasnya</b></label><br/>
		<textarea id='l3' name='desc' type='text' style='min-width:500px'></textarea><br/>
		
		<input type='submit' value='Lapor'/>
	</form>
</div>