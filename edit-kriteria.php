<?php require_once('includes/init.php'); ?>

<?php
$errors = array();
$sukses = false;

$ada_error = false;
$result = '';

$id_kriteria = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_kriteria) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT * FROM kriteria WHERE kriteria.id_kriteria = :id_kriteria');
	$query->execute(array('id_kriteria' => $id_kriteria));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}

	$id_kriteria = (isset($result['id_kriteria'])) ? trim($result['id_kriteria']) : '';
	$nama = (isset($result['nama'])) ? trim($result['nama']) : '';
	$type = (isset($result['type'])) ? trim($result['type']) : '';
	$bobot = (isset($result['bobot'])) ? trim($result['bobot']) : '';
	$jenis_nilai = (isset($result['ada_pilihan'])) ? $result['ada_pilihan'] : 0;
	$urutan_order = (isset($result['urutan_order'])) ? trim($result['urutan_order']) : 0;	
}

if(isset($_POST['submit'])):	
	
	$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
	$type = (isset($_POST['type'])) ? trim($_POST['type']) : '';
	$bobot = (isset($_POST['bobot'])) ? trim($_POST['bobot']) : '';
	$pilihan = (isset($_POST['pilihan'])) ? $_POST['pilihan'] : '';
	$jenis_nilai = (isset($_POST['jenis_nilai'])) ? trim($_POST['jenis_nilai']) : 0;
	$urutan_order = (isset($_POST['urutan_order'])) ? trim($_POST['urutan_order']) : 0;	
	
	// Validasi Nama Kriteria
	if(!$nama) {
		$errors[] = 'Nama kriteria tidak boleh kosong';
	}		
	// Validasi Tipe
	if(!$type) {
		$errors[] = 'Type kriteria tidak boleh kosong';
	}
	// Validasi Bobot
	if(!$bobot) {
		$errors[] = 'Bobot kriteria tidak boleh kosong';
	}
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):
		
		$prepare_query = 'UPDATE kriteria SET nama = :nama, type = :type, bobot = :bobot, urutan_order = :urutan_order, ada_pilihan = :jenis_nilai WHERE id_kriteria = :id_kriteria';
		$data = array(
			'nama' => $nama,
			'type' => $type,
			'bobot' => $bobot,
			'urutan_order' => $urutan_order,
			'id_kriteria' => $id_kriteria,
			'jenis_nilai' => $jenis_nilai			
		);		
		$handle = $pdo->prepare($prepare_query);		
		$sukses = $handle->execute($data);
		
		
		// Simpan Pilihan Kriteria / Variabel
		$ids_pilihan = array();
		if(!empty($pilihan)): foreach($pilihan as $pil):
			
			$nama = (isset($pil['nama'])) ? trim($pil['nama']) : '';
			$nilai = (isset($pil['nilai'])) ? floatval(trim($pil['nilai'])) : '';
			$id_pil_kriteria = (isset($pil['id'])) ? trim($pil['id']) : '';
			$urutan_order = (isset($pil['urutan']) && $pil['urutan']) ? (int) trim($pil['urutan']) : 0;

			echo $nilai;
			if($id_pil_kriteria && $nama != '' && ($nilai >= 0)):				
				// Update jika pilihan telah ada di database				
				$prepare_query = 'UPDATE pilihan_kriteria SET nama = :nama, id_kriteria = :id_kriteria, nilai = :nilai, urutan_order = :urutan_order WHERE id_pil_kriteria = :id_pil_kriteria';
				$data = array(
					'nama' => $nama,
					'id_kriteria' => $id_kriteria,
					'nilai' => $nilai,
					'urutan_order' => $urutan_order,
					'id_pil_kriteria' => $id_pil_kriteria		
				);		
				$handle = $pdo->prepare($prepare_query);		
				$sukses = $handle->execute($data);
				if($sukses):
					$ids_pilihan[] = $id_pil_kriteria;
				endif;					
				
			elseif(($nama != '') && ($nilai >= 0)):
				// Insert jika pilihan belum ada di database
				$prepare_query = 'INSERT INTO pilihan_kriteria (nama, id_kriteria, nilai, urutan_order) VALUES  (:nama, :id_kriteria, :nilai, :urutan_order)';
				$data = array(
					'nama' => $nama,
					'id_kriteria' => $id_kriteria,
					'nilai' => $nilai,
					'urutan_order' => $urutan_order	
				);		
				$handle = $pdo->prepare($prepare_query);		
				$sukses = $handle->execute($data);				
				if($sukses):
					$last_id = $pdo->lastInsertId();
					$ids_pilihan[] = $last_id;
				endif;
				
			endif;
			
		endforeach; endif; // end if(!empty($pilihan))
			
		// Bersihkan pilihan
		if(!empty($ids_pilihan)):
			$not_in = implode(',', $ids_pilihan);
			$prepare_query = 'DELETE FROM pilihan_kriteria WHERE id_pil_kriteria NOT IN ('.$not_in.')';
			$handle = $pdo->prepare($prepare_query);	
			$handle->execute();
		else:
			$prepare_query = 'DELETE FROM pilihan_kriteria WHERE id_kriteria = :id_kriteria';
			$handle = $pdo->prepare($prepare_query);	
			$handle->execute(array('id_kriteria' => $id_kriteria));
		endif;
		
		redirect_to('list-kriteria.php?status=sukses-edit');
		
	
	endif;

endif;
?>

<?php
$judul_page = 'Edit Kriteria';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1>Edit Kriteria</h1>
			
			<?php if(!empty($errors)): ?>
			
				<div class="msg-box warning-box">
					<p><strong>Error:</strong></p>
					<ul>
						<?php foreach($errors as $error): ?>
							<li><?php echo $error; ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
				
			<?php endif; ?>
			
			<?php if($sukses): ?>
			
				<div class="msg-box">
					<p>Data berhasil disimpan</p>
				</div>	
				
			<?php elseif($ada_error): ?>
				
				<p><?php echo $ada_error; ?></p>
			
			<?php else: ?>				
				
				<form action="edit-kriteria.php?id=<?php echo $id_kriteria; ?>" method="post">
					<div class="field-wrap clearfix">					
						<label>Nama Kriteria <span class="red">*</span></label>
						<input type="text" name="nama" value="<?php echo $nama; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Type Kriteria <span class="red">*</span></label>
						<select name="type">
							<option value="benefit" <?php selected($type, 'benefit'); ?>>Benefit</option>
							<option value="cost" <?php selected($type, 'cost'); ?>>Cost</option>						
						</select>
					</div>
					<div class="field-wrap clearfix">					
						<label>Bobot Kriteria <span class="red">*</span></label>
						<input type="number" name="bobot" value="<?php echo $bobot; ?>" step="0.01">
					</div>					
					<div class="field-wrap clearfix">					
						<label>Urutan Order</label>
						<input type="number" name="urutan_order" value="<?php echo $urutan_order; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Cara Penilaian</label>
						<select name="jenis_nilai" id="jenis-nilai">
							<option value="0" <?php selected($jenis_nilai, 0); ?>>Inputan Langsung</option>
							<option value="1" <?php selected($jenis_nilai, 1); ?>>Menggunakan Pilihan Variabel</option>						
						</select>
					</div>
					
					<?php
					$the_class = ($jenis_nilai == 0) ? 'sembunyikan' : '';
					?>
					<div class="field-wrap list-var clearfix <?php echo $the_class; ?>">					
						<h3>Pilihan Variabel</h3>
						<table id="pilihan-var" class="pure-table pure-table-striped">
							<thead>
								<tr>
									<th>Nama Variabel</th>
									<th style="width: 120px;">Nilai</th>									
									<th style="width: 50px;">Urutan</th>									
									<th>Hapus</th>									
								</tr>
							</thead>
							<tbody>
								
								<?php
								$query = $pdo->prepare('SELECT * FROM pilihan_kriteria WHERE id_kriteria = :id_kriteria ORDER BY urutan_order ASC');			
								$query->execute(array(
									'id_kriteria' => $id_kriteria
								));
								// menampilkan berupa nama field
								$query->setFetchMode(PDO::FETCH_ASSOC);
								$ctr = 1;
								if($query->rowCount() > 0): while($results = $query->fetch()):
								?>								
									<tr data-counter="<?php echo $ctr; ?>">
										<td><input type="text" name="pilihan[<?php echo $ctr; ?>][nama]" value="<?php echo $results['nama']; ?>">
										<input type="hidden" name="pilihan[<?php echo $ctr; ?>][id]" value="<?php echo $results['id_pil_kriteria']; ?>">
										</td>							
										<td><input type="text" name="pilihan[<?php echo $ctr; ?>][nilai]" value="<?php echo $results['nilai']; ?>"></td>												
										<td><input type="text" name="pilihan[<?php echo $ctr; ?>][urutan]" value="<?php echo $results['urutan_order']; ?>"></td>																
										<td><a href="#" class="red del-this-row"><span class="fa fa-times"></span> Hapus</a></td>
									</tr>
									<?php $ctr++; ?>
								<?php endwhile; endif;?>
								
							</tbody>
						</table>
						<div class="align-right">
							<a href="#" class="button tambah-pilihan">Tambah Pilihan</a>
						</div>
					</div>
					
					<div class="field-wrap clearfix">
						<button type="submit" name="submit" value="submit" class="button">Simpan Kriteria</button>
					</div>
				</form>
				
			<?php endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');