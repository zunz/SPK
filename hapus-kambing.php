<?php require_once('includes/init.php'); ?>
<?php cek_login($role = array(1, 2)); ?>

<?php
$ada_error = false;
$result = '';

$id_kambing = (isset($_GET['id'])) ? trim($_GET['id']) : '';

if(!$id_kambing) {
	$ada_error = 'Maaf, data tidak dapat diproses.';
} else {
	$query = $pdo->prepare('SELECT id_kambing FROM kambing WHERE id_kambing = :id_kambing');
	$query->execute(array('id_kambing' => $id_kambing));
	$result = $query->fetch();
	
	if(empty($result)) {
		$ada_error = 'Maaf, data tidak dapat diproses.';
	} else {
		
		$handle = $pdo->prepare('DELETE FROM nilai_kambing WHERE id_kambing = :id_kambing');				
		$handle->execute(array(
			'id_kambing' => $result['id_kambing']
		));
		$handle = $pdo->prepare('DELETE FROM kambing WHERE id_kambing = :id_kambing');				
		$handle->execute(array(
			'id_kambing' => $result['id_kambing']
		));
		redirect_to('list-kambing.php?status=sukses-hapus');
		
	}
}
?>

<?php
$judul_page = 'Hapus Kambing';
require_once('template-parts/header.php');
?>

	<div class="main-content-row">
	<div class="container clearfix">
	
		<?php include_once('template-parts/sidebar-kambing.php'); ?>
	
		<div class="main-content the-content">
			<h1><?php echo $judul_page; ?></h1>
			
			<?php if($ada_error): ?>
			
				<?php echo '<p>'.$ada_error.'</p>'; ?>	
			
			<?php endif; ?>
			
		</div>
	
	</div><!-- .container -->
	</div><!-- .main-content-row -->


<?php
require_once('template-parts/footer.php');