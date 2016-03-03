<?php

if (isset($folder)) {
    foreach ($folder as $item) {
        $simpler = $item->path;
        if (strlen($simpler) > 42) {
            $simpler = substr($simpler, 0, 42).'...';
        }
        echo "<div class='item folder v".$item->visibility."'><div class='inside'><a class='efname' href='javascript:void(0)'";
        if ($item->path == '.') {
            echo "onClick=\"openfolder('.')\"><img src='assets/img/folder.png'/>&lt; kembali ke dasar</a>";
        } else {
            echo "onClick=\"openfolder('".$item->path."')\"><img src='assets/img/folder.png'/>".$simpler.'</a>';
            echo "<a class='button' onClick=\"folder_del('".$item->key."');\"><img src='assets/img/del.png' title='Hapus map' height=12px width=12px /></a>";
            echo "<a class='button' onClick=\"folder_rename('".$item->key."', '".$item->path."')\"><img src='assets/img/edit.png' title='Ganti nama map' height=12px width=12px /></a>";
            echo "<a class='button' onClick=\"folder_vis('".$item->key."');\"><img src='assets/img/visib.png' title='Ganti warna folder' height=12px width=12px /></a>";
            if ($item->visibility == 2) {
                echo "<a class='button' onClick=\"window.prompt('Silakan disalin', '".base_url().'berkas/map/'.$item->key."');\"><img src='assets/img/link.png' title='Pranala map' height=12px width=12px /></a>";
            }
        }
        echo '</div></div>';
    }
}

if (isset($file)) {
    $link = $premium ? 'ambil' : 'unduh';
    foreach ($file as $item) {
        $simpler = $item->filename;
        if (strlen($simpler) > 42) {
            $simpler = substr($simpler, 0, 42).'...';
        }
        echo "<div class='item file'>
			<div class='inside'>
				<a class='efname' href='berkas/{$link}/".$item->code."'><img src='assets/img/file.png'/>".$simpler."</a> 
				<span class='size'>".$item->filesaiz."</span>
				<span class='date'>".$item->uploaddate."</span>
				<a class='button' onClick=\"window.prompt('Silakan disalin', '".base_url()."berkas/{$link}/".$item->code."');\"><img src='assets/img/link.png' title='Pranala berkas' height=12px width=12px /></a>";
        if ($self) {
            echo "<a class='button' onClick=\"file_rename('".$item->code."', '".$item->filename."')\"><img src='assets/img/edit.png' title='Ganti nama berkas' height=12px width=12px /></a>
				<a class='button' onClick=\"file_move('".$item->code."')\"><img src='assets/img/move.png' title='Pindahkan berkas' height=12px width=12px /></a>
				<a class='button' onClick=\"file_del('".$item->code."');\"><img src='assets/img/del.png' title='Hapus berkas' height=12px width=12px /></a>";
        }
        echo '</div>
		</div>';
    }
}

if (!isset($file) && !isset($folder)) {
    echo "<div class='item' align='center'><div class='inside'>Nampaknya di map ini tidak ada apapun.</div></div>";
}

if (isset($error)) {
    echo "<div class='wrapper padder center'>";
    foreach ($error as $item) {
        echo '<p>'.$item.'</p>';
    }
    echo '</div>';
}
