<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$errors = array();
$sukses = false;

$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
$type = (isset($_POST['type'])) ? trim($_POST['type']) : '';
$bobot = (isset($_POST['bobot'])) ? trim($_POST['bobot']) : '';
$jenis_nilai = (isset($_POST['jenis_nilai'])) ? trim($_POST['jenis_nilai']) : 0;
$pilihan = (isset($_POST['pilihan'])) ? $_POST['pilihan'] : '';
$urutan_order = (isset($_POST['urutan_order'])) ? trim($_POST['urutan_order']) : '0';

if(isset($_POST['submit'])):	
	
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
		
		$handle = $pdo->prepare('INSERT INTO kriteria (nama, type, bobot, urutan_order, ada_pilihan) VALUES (:nama, :type, :bobot, :urutan_order, :jenis_nilai)');
		$handle->execute( array(
			'nama' => $nama,
			'type' => $type,
			'bobot' => $bobot,
			'urutan_order' => $urutan_order,
			'jenis_nilai' => $jenis_nilai			
		) );
		$id_kriteria = $pdo->lastInsertId();
		
		if($id_kriteria && $jenis_nilai == 1 && !empty($pilihan)): foreach($pilihan as $pil):
			
			$nama = (isset($pil['nama'])) ? trim($pil['nama']) : '';
			$nilai = (isset($pil['nilai'])) ? floatval($pil['nilai']) : '';
			$urutan_order = (isset($pil['urutan']) && $pil['urutan']) ? (int) trim($pil['urutan']) : 0;
						
			
			if($nama != '' && ($nilai >= 0)):
				
				$prepare_query = 'INSERT INTO pilihan_kriteria (nama, id_kriteria, nilai, urutan_order) VALUES  (:nama, :id_kriteria, :nilai, :urutan_order)';
				$data = array(
					'nama' => $nama,
					'id_kriteria' => $id_kriteria,
					'nilai' => $nilai,
					'urutan_order' => $urutan_order	
				);		
				$handle = $pdo->prepare($prepare_query);		
				$sukses = $handle->execute($data);				
				
			endif;		
		endforeach; endif;
		
		redirect_to('list-kriteria.php?status=sukses-baru');		
	
	endif;

endif;
?>

<?php
$judul_page = 'Tambah User';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kriteria.php'); ?>
	
		<div class="main-content the-content">
			<h1>Tambah Kriteria</h1>
			
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
			
				<form action="tambah-kriteria.php" method="post">
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
							<option value="0">Inputan Langsung</option>
							<option value="1">Menggunakan Pilihan Variabel</option>						
						</select>
					</div>
					
					<div class="field-wrap list-var clearfix sembunyikan">					
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
							</tbody>
						</table>
						<div class="align-right">
							<a href="#" class="button tambah-pilihan">Tambah Pilihan</a>
						</div>
					</div>
					
					<div class="field-wrap clearfix">
						<button type="submit" name="submit" value="submit" class="button">Tambah Kriteria</button>
					</div>
				</form>
				
			<?php //endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');