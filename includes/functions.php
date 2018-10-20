<?php
function ambil_template($nama_template = '') {
	if($nama_template) {
		require_once('template-parts/'.$nama_template.'.php');
	}	
}

function selected($param1='', $param2='') {
	if($param1 == $param2) {
		echo 'selected="selected"';
	}
}

function redirect_to($url = '') {
	header('Location: '.$url);
	exit();
}

function cek_login($role = array()) {
	
	if(isset($_SESSION['user_id']) && isset($_SESSION['role']) && in_array($_SESSION['role'], $role)) {
		// do nothing
	} else {
		redirect_to("login.php");
	}	
}

function get_role() {
	
	if(isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
		if($_SESSION['role'] == '1') {
			return 'admin';
		} else {
			return 'petugas';
		}
	} else {
		return false;
	}	
}