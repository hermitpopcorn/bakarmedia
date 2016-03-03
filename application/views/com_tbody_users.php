<?php foreach ($users as $u) {
    ?>
<tr>
	<td><?= $u['email'] ?>&nbsp;</td>
	<td><?= $u['fname'] ?>&nbsp;</td>
	<td><?= $u['lname'] ?>&nbsp;</td>
	<td><input name='quota' user="<?= $u['email'] ?>" value='<?= $u['limit'] ?>'>MB&nbsp;</td>
	<td>
		<select name='level' user="<?= $u['email'] ?>">
			<?php foreach (array('u', 'a', 'p') as $l) {
    ?>
			<option value='<?= $l ?>' <?php if ($l == $u['level']) {
    echo 'selected';
}
    ?> ><?= $l ?></option>
			<?php 
}
    ?>
		</select>
	</td>
	<td>
		<a href='javascript:void(0)' onClick="save('<?= $u['email'] ?>')"><img src='assets/img/ok.png' width='16px' height='16px' /></a>
	</td>
</tr>
<?php 
} ?>
<?php if (sizeof($users) < 1) {
    ?>
<tr>
	<td colspan='7'>Data tidak ditemukan.</td>
</tr>
<?php 
} ?>
<tr>
	<td colspan='7' align='center'>
	<?php for ($i = 1; $i <= $pages; ++$i) {
    ?>
		<a href='javascript:void(0)' onClick='jax_pengguna(<?= $i - 1 ?>)'><?= $i ?></a>
	<?php 
} ?>
	</td>
</tr>