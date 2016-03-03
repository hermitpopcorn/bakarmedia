<script>
	var filter = "";
	
	function ffilter() {
		filter = $('#filterbox').val();
		jax_pengguna(0);
	}
	
	function save(u) {
		if(typeof(u) === "undefined") { return; }
		
		selector1 = "input[user=\"" + u + "\"]";
		selector2 = "select[user=\"" + u + "\"]";
		
		NProgress.start();
		$.ajax({
			type: "POST",
			url: 'administrasi/ubah_data_pengguna',
			data: { 'who': u, 'quota': $(selector1).val(), 'level': $(selector2).val() },
			success: function(data) {
				alert(data);
				NProgress.done();
			}
		});
	}
	
	function jax_pengguna(p) {
		p = (typeof(p) === "undefined") ? 0 : p;
		
		NProgress.start();
		$.ajax({
			type: "POST",
			url: 'administrasi/pengguna/' + p,
			data: { 'filter': filter },
			success: function(data) {
				$('tbody#pengguna').html(data);
				NProgress.done();
			}
		});
	}
	
	$(document).ready(function() {
		jax_pengguna(0);
	});
</script>
<div class='wrapper padder' style='margin-top:6px'>
	<h1>Pengguna</h1>
	<div align='right' style='margin-bottom:6px;'>
		<input id='filterbox' type='text' placeholder='Cari berdasarkan alamat surel' style='width:350px'/>
		<button onClick='ffilter()'>Cari</button>
	</div>
	<table cellspacing=0 >
		<thead>
			<tr class='head'>
				<td>Alamat Surel</td>
				<td>Nama Depan</td>
				<td>Nama Belakang</td>
				<td>Kuota</td>
				<td>Status</td>
				<td>&nbsp;</td>
			</tr>
		</thead>
		<tbody id='pengguna'>
		</tbody>
	</table>
</div>