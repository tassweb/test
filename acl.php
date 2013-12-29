<?php 
	function getWritePerm($current_group) {
		$writeGroup = array('Write','Administrator');
		return in_array($current_group,$writeGroup) ? true : false ;  
	}
	
	function getAdminPerm($current_group) {
		$AdminGroup = array('Administrator');
		return in_array($current_group,$AdminGroup) ? true : false ; 
	}
	
	function getReadPerm($current_group) {
		$ReadGroup = array('Administrator','Write','Read');
		return in_array($current_group,$ReadGroup) ? true : false ; 
	}
?>