<?php

/**
 * ProductsTranches
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ShineISP
 * 
 * @author     Shine Software <info@shineisp.com>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ProductsTranches extends BaseProductsTranches
{
	/**
	 * Save the billing tranches
	 * 
	 * 
	 * @param integer $productId
	 * @param integer $billingId
	 * @param integer $qta
	 * @param string $measure
	 * @param float $price
	 */
	public static function saveAll($productId, $billingId, $qta, $measure, $price, $setupfee = 0) {
		$tranches = new ProductsTranches ();
		$tranches->quantity = $qta;
		$tranches->measurement = $measure;
		$tranches->billing_cycle_id = $billingId;
		$tranches->setupfee = $setupfee;
		$tranches->price = $price;
		$tranches->product_id = $productId;
		$tranches->save ();
        return $tranches;
	}
    
    /**
     * getTranchebyId
     * Get tranche by id
     * @param $id
     * @return array
     */
    public static function getTranchebyId($id) {
        if(is_numeric($id)){
            $record = Doctrine_Query::create ()
                     ->from ( 'ProductsTranches pt' )
                     ->leftJoin('pt.BillingCycle bc')
                     ->leftJoin('pt.Products p')
                     ->leftJoin('p.Taxes t')
                     ->where ( 'pt.tranche_id = ?', $id )
                     ->limit(1)
                     ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
        
            return !empty($record[0]) ? $record[0] : array();
        }
        return false;
    }   	
	    
    /**
     * delTranchebyId
     * Delete the tranche price by id
     * @param $id
     * @return Boolean
     */
    public static function delTranchebyId($id) {
        if(is_numeric($id)){
            return Doctrine::getTable ( 'ProductsTranches' )->find($id)->delete();
        }
        return false;
    } 
	    
    /**
     * setDefault
     * set as default tranche price by id
     * @param $id
     * @return Boolean
     */
    public static function setDefault($id) {
        if(is_numeric($id)){
        	$trance = self::getTranchebyId($id);
        	
        	$q = Doctrine_Query::create()
                ->update('ProductsTranches')
                ->set('selected', 0)
                ->where("product_id = ?", $trance['product_id'])->execute();
        	
        	$q = Doctrine_Query::create()
			    ->update('ProductsTranches')
			    ->set('selected', 1)
			    ->where("tranche_id = ?", $id)->execute();
			    
            return true;
        }
        return false;
    } 
    
    /**
     * Get the product options by productid
     * @param $productid
     * @return array 
     */
    public static function getTranches($productid, $fields="*") {
        try {
            
            $dq = Doctrine_Query::create ()
                    ->select($fields)
                     ->from ( 'ProductsTranches pt' )
                     ->leftJoin('pt.BillingCycle bc')
                     ->where ( 'pt.product_id = ?', $productid )
                     ->orderBy('pt.quantity');
                     
            $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            return $records;
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
    
    /**
     * Get the billing information starting from the product tranche setting
     * @param integer $trancheId
     * @return array 
     */
    public static function getBillingCycle($trancheId) {
        try {
            
        	if (!is_numeric($trancheId)){
            	return null;
            }
            
            $dq = Doctrine_Query::create ()
                     ->from ( 'ProductsTranches pt' )
                     ->leftJoin('pt.BillingCycle bc')
                     ->where ( 'pt.tranche_id= ?', $trancheId );
                     
            $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            return !empty($records['BillingCycle']) ? $records['BillingCycle'] : null;
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
    
    /**
     * Get the product options by Product ID and billing cycle ID
     * @param $id
     * @return array 
     */
    public static function getTranchesBy_ProductId_BillingId($id, $billid) {
        try {
            
            $dq = Doctrine_Query::create ()
                     ->from ( 'ProductsTranches pt' )
                     ->leftJoin('pt.BillingCycle bc')
                     ->where ( 'pt.product_id = ?', $id )
                     ->andWhere ( 'pt.billing_cycle_id = ?', $billid )
                     ->orderBy('pt.quantity');
                     
            $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            return $records;
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
    
    /**
     * getMinMaxTranches
     * Get the product the lowest tranches price
     * @param $productid
     * @return array 
     */
    public static function getMinMaxTranches($productid) {
        try {
            
            $records = Doctrine_Query::create ()
                     ->from ( 'ProductsTranches pt' )
                     ->leftJoin('pt.BillingCycle bc')
                     ->leftJoin('pt.Products p')
                     ->leftJoin('p.Taxes t')
                     ->where ( 'pt.product_id = ?', $productid )
                     ->orderBy('pt.price asc')
                     ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );

            if(count($records) > 1){
            	return array($records[0], $records[count($records)-1]);
            }elseif(count($records) > 0){
            	return $records[0];
            }
            return array();
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
    
    /**
     * getSuggestedTranche
     * Get the suggested price for the product selected
     * @param $productid
     * @return array 
     */
    public static function getSuggestedTranche($productid) {
        try {
            
            $records = Doctrine_Query::create ()
                     ->from ( 'ProductsTranches pt' )
                     ->leftJoin('pt.BillingCycle bc')
                     ->where ( 'pt.product_id = ?', $productid )
                     ->andWhere ( 'pt.selected = 1')
                     ->orderBy('pt.price asc')
                     ->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );

            return $records;
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
    
    /**
     * getList
     * Get the product options by productid
     * @param $productid
     * @return array 
     */
    public static function getList($productid, $refund = false) {
        $translator = Shineisp_Registry::getInstance ()->Zend_Translate;
        $currency = Shineisp_Registry::getInstance ()->Zend_Currency;
        
    	try {
            $items = array();
            
            $dq = Doctrine_Query::create ()
                     ->from ( 'ProductsTranches pt' )
                     ->leftJoin( 'pt.BillingCycle bc' )
                     ->where ( 'pt.product_id = ?', $productid )
                     ->orderBy('bc.months asc');
                     
            $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            
            // Check the refund options
	        foreach ( $records as $c ) {
				if( $refund !== false ) {
					$idBillingCircle		= $c['BillingCycle']['billing_cycle_id'];
					$monthBilling			= BillingCycle::getMonthsNumber($idBillingCircle);
					if( $monthBilling > 0 ) {
						$priceToPay				= $c['price'] * $monthBilling;
						$priceToPayWithRefund	= $priceToPay - $refund;
						if( $priceToPayWithRefund < 0 ) {
							$priceToPayWithRefund	= $priceToPay;
						}
						$c['price']	= round( $priceToPayWithRefund / $monthBilling,2 );
					} else {
						$priceToPayWithRefund	= $c['price'] - $refund;
						if( $priceToPayWithRefund > 0 ) {
							$c['price']	= $priceToPayWithRefund;
						}
					}					
				}
				
				$items [$c ['tranche_id']] = "";
				
				// If quantity is more than one ShineISP must show the item quantity 
				if($c ['quantity'] > 1){
					$items [$c ['tranche_id']] .= "No. " . $c ['quantity'] . " ";
				}
				
	        	$items [$c ['tranche_id']] .= "(" . $translator->translate($c['BillingCycle']['name']) .  ") - " . $currency->toCurrency($c ['price'], array('currency' => Settings::findbyParam('currency')));
	            
	            if(!empty($c ['measurement'])){
	            	 $items [$c ['tranche_id']] .= "/" . $translator->translate($c ['measurement']);
	            }
	        }
	        
            return $items;
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
    
    /**
     * Get the default item 
     * 
     * @param $productid
     * @return integer 
     */
    public static function getDefaultItem($productid) {
        try {
            $dq = Doctrine_Query::create ()
                     ->from ( 'ProductsTranches pt' )
                     ->where ( 'pt.product_id = ?', $productid )
                     ->andWhere('selected = ?', true)
                     ->orderBy('pt.quantity');
                     
            $records = $dq->execute ( array (), Doctrine_Core::HYDRATE_ARRAY );
            if(!empty($records[0]['selected']) && $records[0]['selected']){
            	return $records[0]['tranche_id'];
            }else{
            	return null;
            }
        
        } catch ( Exception $e ) {
            die ( $e->getMessage () );
        }
    }
}