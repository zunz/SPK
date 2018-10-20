<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$errors = array();
$sukses = false;

$ada_error = false;
$result = '';

$id_user = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_user) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT id_user, username, nama, email, alamat, role FROM user WHERE user.id_user = :id_user');
	$query->execute(array('id_user' => $id_user));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	}
	
	$username = (isset($result['username'])) ? trim($result['username']) : '';
	$nama = (isset($result['nama'])) ? trim($result['nama']) : '';
	$email = (isset($result['email'])) ? trim($result['email']) : '';
	$alamat = (isset($result['alamat'])) ? trim($result['alamat']) : '';
	$role = (isset($result['role'])) ? trim($result['role']) : '';	
}

if(isset($_POST['submit'])):	
	
	$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
	$email = (isset($_POST['email'])) ? trim($_POST['email']) : '';
	$alamat = (isset($_POST['alamat'])) ? trim($_POST['alamat']) : '';
	$role = (isset($_POST['role'])) ? trim($_POST['role']) : '';
	$password = (isset($_POST['password'])) ? trim($_POST['password']) : '';
	$password2 = (isset($_POST['password2'])) ? trim($_POST['password2']) : '';
	
	// Validasi Nama
	if(!$nama) {
		$errors[] = 'Nama tidak boleh kosong';
	}		
	// Validasi Email
	if(!$email) {
		$errors[] = 'Email tidak boleh kosong';
	}
	// Validasi role
	if(!$role) {
		$errors[] = 'Role tidak boleh kosong';
	}
	
	// Validasi ID
	if(!$id_user) {
		$errors[] = 'Id User salah';
	}
	
	if($password && ($password != $password2)) {
		$errors[] = 'Password harus sama keduanya';
	}
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):
		
		$prepare_query = 'UPDATE user SET nama = :nama, alamat = :alamat, email = :email, role = :role WHERE id_user = :id_user';
		$data = array(
			'nama' => $nama,
			'alamat' => $alamat,
			'email' => $email,
			'role' => $role,
			'id_user' => $id_user
		);
		if($password) {
			$prepare_query = 'UPDATE user SET nama = :nama, password = :password, alamat = :alamat, email = :email, role = :role WHERE id_user = :id_user';
			$data['password'] = sha1($password);
		}		
		$handle = $pdo->prepare($prepare_query);		
		$sukses = $handle->execute($data);
	
	endif;

endif;
?>

<?php
$judul_page = 'Edit User';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-user.php'); ?>
	
		<div class="main-content the-content">
			<h1>Edit User</h1>
			
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
				
				<form action="edit-user.php?id=<?php echo $id_user; ?>" method="post">
					<div class="field-wrap clearfix">					
						<label>Username (tidak dapat diubah)</label>
						<p><?php echo $result['username']; ?></p>
					</div>					
					<div class="field-wrap clearfix">					
						<label>Nama <span class="red">*</span></label>
						<input type="text" name="nama" value="<?php echo $nama; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Email <span class="red">*</span></label>
						<input type="email" name="email" value="<?php echo $email; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Alamat</label>
						<input type="text" name="alamat" value="<?php echo $alamat; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Role</label>
						<select name="role">
							<option value="2" <?php selected($role, 2); ?>>Petugas</option>
							<option value="1" <?php selected($role, 1); ?>>Administrator</option>						
						</select>
					</div>
					<div class="field-wrap clearfix">					
						<label>Ganti Password? (Kosongkan jika tidak ingin mengubah password)</label>
						<input type="password" name="password">
					</div>
					<div class="field-wrap clearfix">					
						<label>Password Lagi (Kosongkan jika tidak ingin mengubah password)</label>
						<input type="password" name="password2">
					</div>
					<div class="field-wrap clearfix">
						<button type="submit" name="submit" value="submit" class="button">Simpan User</button>
					</div>
				</form>
				
			<?php endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');