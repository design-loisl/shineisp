<?php

/**
 * CustomersDomainsRegistrars
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class CustomersDomainsRegistrars extends BaseCustomersDomainsRegistrars
{

	
    /**
     * getList
     * Get all the records for the select objects
     * @param $customer_id, $empty
     * @return Array
     */
    public static function getList($customer_id = null, $empty = false) {
        $theitems = array ();
        $dq = Doctrine_Query::create ()
        						->select ( "cr.nic_id, cr.value, c.customer_id, c.company as company, c.lastname as lastname, c.firstname as firstname, DATE_FORMAT(cr.creationdate, '".Settings::getMySQLDateFormat('dateformat')."') as creationdate" )
        						->from ( 'CustomersDomainsRegistrars cr' )
        						->leftJoin('cr.Customers c');
        
        if(is_numeric($customer_id)){
            $dq->where('cr.customer_id = ?', $customer_id);
        }
        
        $items = $dq->execute ( array (), Doctrine::HYDRATE_ARRAY );
        
        if($empty){
            $theitems[] = "";
        }
        
        foreach ( $items as $c ) {
            $theitems [$c ['nic_id']] = $c ['value'] . " - " . $c['lastname'] . " " . $c['firstname'] . " - " . $c['company'];
        }
        return $theitems;
    }
    
    
	public static function del($customerid) {
		return Doctrine_Query::create ()->delete ( 'CustomersDomainsRegistrars' )->where ( 'customer_id = ?', $customerid )->execute ();
	}    
    
    /**
     * chkIfExist
     * Check if a Nic is already saved in the archive
     * @param $nic
     * @return Array
     */
    public static function chkIfExist($nic) {
        $theitems = array ();
        $dq = Doctrine_Query::create ()->select ( "nic_id as nicid, value as nicHandle" )->from ( 'CustomersDomainsRegistrars cr' );
        
        if(!empty($nic)){
            $dq->where('cr.value = ?', $nic);
        }
        
        $items = $dq->execute ( array (), Doctrine::HYDRATE_ARRAY );
        return !empty($items[0]['nicHandle']) ? $items[0]['nicHandle'] : null;
    }  
    
    /**
     * getAllInfo
     * Get one or more records using the NicHandle
     * @param $nic
     * @return Array
     */
    public static function getAllInfo($nic="", $fields="*") {
        $theitems = array ();
        $dq = Doctrine_Query::create ()->select ( $fields )->from ( 'CustomersDomainsRegistrars cr' )
        ->leftJoin('cr.Customers')
        ->leftJoin('cr.Registrars')
        ->leftJoin('cr.Domains');
        
        if(!empty($nic)){
            $dq->where('cr.value = ?', $nic);
        }
        
        $items = $dq->execute ( array (), Doctrine::HYDRATE_ARRAY );
        return $items;
    }  

    
    /**
     * chkIfCustomerExist
     * Check if a Nic is already saved for a particular customer in the archive
     * @param $customer_id
     * @return Array
     */
    public static function chkIfCustomerExist($customer_id) {
        $theitems = array ();
        $dq = Doctrine_Query::create ()->select ( "Count(*) as total" )->from ( 'CustomersDomainsRegistrars cr' );
        
        if(!empty($customer_id)){
            $dq->where('cr.customer_id = ?', $customer_id);
        }
        
        $items = $dq->execute ( array (), Doctrine::HYDRATE_ARRAY );
        return $items[0]['total'];
    }      
    
    /**
     * getCustomerNicbyCustomerID
     * Check if a Nic is already saved for a particular customer in the archive
     * @param $customer_id
     * @return Array
     */
    public static function getCustomerNicbyCustomerID($customer_id) {
        $theitems = array ();
        $dq = Doctrine_Query::create ()
                    ->select('registrars_id, r.name as registrar, value as nichandle')
                    ->from ( 'CustomersDomainsRegistrars cr' )
                    ->leftJoin('Registrars r')
                    ->limit(1);
        
        if(!empty($customer_id)){
            $dq->where('cr.customer_id = ?', $customer_id);
        }
        
        $item = $dq->execute ( array (), Doctrine::HYDRATE_ARRAY );
        return $item;
    }  

    /**
     * addNicHandle
     * Add a new NicHandle
     * @param unknown_type $customerID
     * @param unknown_type $domainID
     */
    public static function addNicHandle($domainID, $nicHandle, $type="owner", $profileID=null){
    	$nic = self::chkIfExist ( $nicHandle );
    	
		if (empty($nic)) {

			// Get the domain information
			$domain = Domains::findbyId($domainID);
			
			$CustomerDomainsRegistrars = new CustomersDomainsRegistrars ( );
			$CustomerDomainsRegistrars->customer_id = $domain['customer_id'];
			$CustomerDomainsRegistrars->registrars_id = $domain['registrars_id'];
			$CustomerDomainsRegistrars->creationdate = date('Y-m-d :H:i:s');
			$CustomerDomainsRegistrars->domain_id = $domainID;
			$CustomerDomainsRegistrars->profile_id = $profileID;
			$CustomerDomainsRegistrars->value = $nicHandle;
			$CustomerDomainsRegistrars->type = $type;
			$CustomerDomainsRegistrars->save ();
			return $nicHandle;
		}else{
			return $nic;
		}    	
    }
       
}