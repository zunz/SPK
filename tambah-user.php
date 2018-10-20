<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1)); ?>

<?php
$errors = array();
$sukses = false;

$username = (isset($_POST['username'])) ? trim($_POST['username']) : '';
$password = (isset($_POST['password'])) ? trim($_POST['password']) : '';
$password2 = (isset($_POST['password2'])) ? trim($_POST['password2']) : '';
$nama = (isset($_POST['nama'])) ? trim($_POST['nama']) : '';
$email = (isset($_POST['email'])) ? trim($_POST['email']) : '';
$alamat = (isset($_POST['alamat'])) ? trim($_POST['alamat']) : '';
$role = (isset($_POST['role'])) ? trim($_POST['role']) : '';

if(isset($_POST['submit'])):		
	
	// Validasi Username
	if(!$username) {
		$errors[] = 'Username tidak boleh kosong';
	}		
	// Validasi Password
	if(!$password) {
		$errors[] = 'Password tidak boleh kosong';
	}		
	// Validasi Password 2
	if($password != $password2) {
		$errors[] = 'Password harus sama keduanya';
	}		
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
	
	// Cek Username
	if($username) {
		$query = $pdo->prepare('SELECT username FROM user WHERE user.username = :username');
		$query->execute(array('username' => $username));
		$result = $query->fetch();
		if(!empty($result)) {
			$errors[] = 'Username sudah digunakan';
		}
	}
	
	
	// Jika lolos validasi lakukan hal di bawah ini
	if(empty($errors)):
		
		$handle = $pdo->prepare('INSERT INTO user (username, password, nama, email, alamat, role) VALUES (:username, :password, :nama, :email, :alamat, :role)');
		$handle->execute( array(
			'username' => $username,
			'password' => sha1($password),
			'nama' => $nama,
			'email' => $email,
			'alamat' => $alamat,
			'username' => $username,
			'role' => $role
		) );
		$sukses = "<strong>{$username}</strong> berhasil dimasukkan.";
	
	endif;

endif;
?>

<?php
$judul_page = 'Tambah User';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-user.php'); ?>
	
		<div class="main-content the-content">
			<h1>Tambah User</h1>
			
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
					<p><?php echo $sukses; ?></p>
				</div>	
				
			<?php else: ?>
			
				<form action="tambah-user.php" method="post">
					<div class="field-wrap clearfix">					
						<label>Username <span class="red">*</span></label>
						<input type="text" name="username" value="<?php echo $username; ?>">
					</div>
					<div class="field-wrap clearfix">					
						<label>Password <span class="red">*</span></label>
						<input type="password" name="password">
					</div>
					<div class="field-wrap clearfix">					
						<label>Password Lagi <span class="red">*</span></label>
						<input type="password" name="password2">
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
						<button type="submit" name="submit" value="submit" class="button">Tambah User</button>
					</div>
				</form>
				
			<?php endif; ?>			
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');