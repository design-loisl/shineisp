<?php
class Shineisp_Controller_Admin extends Shineisp_Controller_Common {
	/*
	 * Common for the whole admin controllers
	*/
	
	public function init() {
		// Get authenticated user
		$auth = Zend_Auth::getInstance()->getIdentity();

		// Store logged ISP. I'm inside admin, se we use only the logged user
		if ( isset($auth['isp_id']) ) {
			$isp_id = intval($auth['isp_id']);
			
			$ISP = new Isp();
			Shineisp_Registry::set('ISP', $ISP->find($isp_id));
		}

		parent::init();
    }	
}