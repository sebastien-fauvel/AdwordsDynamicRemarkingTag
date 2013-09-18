<?php
/**
 * AdWords Dynamic Remarketing Tag 1.5
 *
 * Dynamic remarketing lets you reach your past site visitors with ads 
 * that show the specific products people viewed on your site. Once 
 * you've set up your dynamic remarketing campaign, you'll need to add 
 * the dynamic remarketing tag, including custom parameters, to your 
 * site so your lists can start working.
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.richardfu.net/
 * @copyright Copyright 2013, Richard Fu
 * @author Richard Fu
 * @filesource 
 */

/**
 * Adwords Dynamic Remarketing Tag Class
 *
 * The adwords_tag class provides fetching page and output functions
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.richardfu.net/
 * @copyright Copyright 2013, Richard Fu
 * @author Richard Fu
 */
 
class adwords_tag {
	
	/**
	 * $page_totalvalue is the current page type
	 * @var object
	 */
	var $page_type;
	
	/**
	 * $page_totalvalue is the current page products ids
	 * @var object
	 */
	var $page_products_id;

	/**
	 * $page_totalvalue is the current page products total value
	 * @var object
	 */
	var $page_totalvalue;
	
	/**
	 * $installer is the installer object
	 * @var object
	 */
	var $installer;
	
	function adwords_tag() {
		$this->installer = new adwords_tag_install;
	
		$scriptname = ltrim(basename($_SERVER['SCRIPT_NAME']));
		$return = array();
		switch($scriptname){
			case FILENAME_DEFAULT:
				global $category_depth;
				if($category_depth == 'top')
					$this->page_type = 'home';
				else
					$this->page_type = 'category';
				$this->page_products_id = "''";
				break;
			case FILENAME_PRODUCT_INFO:
				global $_GET, $currency, $currencies;
				if(tep_not_null($_GET['products_id'])){
					$this->page_products_id = "'" . $_GET['products_id'] . "'";
					global $product_info;
					$rate = $currencies->currencies[$currency]['value'];
					$decimal_places = $currencies->currencies[$currency]['decimal_places'];
					$this->page_totalvalue = number_format(tep_add_tax($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) * $rate, $decimal_places);
					if($new_price = tep_get_products_special_price($product_info['products_id']))
						$this->page_totalvalue = number_format(tep_add_tax($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])), $decimal_places);
				}else{
					$this->page_products_id = "''";
				}
				$this->page_type = 'product';
				break;
			case FILENAME_SHOPPING_CART:
				global $cart, $currency, $currencies;
				if($cart && is_object($cart)){
					$products_id_list = array();
					foreach($cart->contents as $products_id => $products_content)
						$products_id_list[] = "'" . (int)$products_id . "'";
					if(count($products_id_list) <= 0)
						$this->page_products_id = "''";
					elseif(count($products_id_list) == 1)
						$this->page_products_id = $products_id_list[0];
					else
						$this->page_products_id = '[' . implode(',', $products_id_list) . ']';
					$rate = $currencies->currencies[$currency]['value'];
					$decimal_places = $currencies->currencies[$currency]['decimal_places'];
					$this->page_totalvalue = number_format($cart->show_total() * $rate, $decimal_places);
				}else{
					$this->page_products_id = "''";
				}
				$this->page_type = 'cart';
				break;
			case FILENAME_CHECKOUT_SHIPPING:
			case FILENAME_CHECKOUT_PAYMENT:
			case FILENAME_CHECKOUT_CONFIRMATION:
			case FILENAME_CHECKOUT_SUCCESS:
				global $order, $currency, $currencies;
				if($order && is_object($order)){
					$products_id_list = array();
					foreach($order->products as $product)
						$products_id_list[] = "'" . (int)$product['id'] . "'";
					if(count($products_id_list) <= 0)
						$this->page_products_id = "''";
					elseif(count($products_id_list) == 1)
						$this->page_products_id = $products_id_list[0];
					else
						$this->page_products_id = '[' . implode(',', $products_id_list) . ']';
					$rate = $currencies->currencies[$currency]['value'];
					$decimal_places = $currencies->currencies[$currency]['decimal_places'];
					if($scriptname == FILENAME_CHECKOUT_SHIPPING || $scriptname == FILENAME_CHECKOUT_PAYMENT){ // calculate the shipping tax
						if($scriptname == FILENAME_CHECKOUT_PAYMENT){
							require(DIR_WS_CLASSES . 'shipping.php');
							$shipping_modules = new shipping;
						}
						$module = substr($GLOBALS['shipping']['id'], 0, strpos($GLOBALS['shipping']['id'], '_'));
						$shipping_tax = tep_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
						$order->info['total'] += tep_calculate_tax($order->info['shipping_cost'], $shipping_tax);
					}
					$this->page_totalvalue = number_format($order->info['total'] * $rate, $decimal_places);
				}else{
					$this->page_products_id = "''";
				}
				$this->page_type = 'purchase';
				break;
			case FILENAME_ADVANCED_SEARCH_RESULT:
				$this->page_products_id = "''";
				$this->page_type = 'searchresults';
				break;
			default:
				$this->page_products_id = "''";
				$this->page_type = 'other';
		}	
	}
	
	function output() {
		echo '<script type="text/javascript">' . "\n";
		echo 'var google_tag_params = {' . "\n";
		echo 'ecomm_prodid: ' . $this->page_products_id . ',' . "\n";
		echo 'ecomm_pagetype: \'' . $this->page_type . '\',' . "\n";
		echo 'ecomm_totalvalue: \'' . $this->page_totalvalue . '\'' . "\n";
		echo '};' . "\n";
		echo '</script>' . "\n";
		
		echo '<script type="text/javascript">' . "\n";
		echo '/* <![CDATA[ */' . "\n";
		echo 'var google_conversion_id = ' . ADWORDS_TAG_GOOGLE_CONVERSION_ID . ';' . "\n";
		if (tep_not_null(ADWORDS_TAG_GOOGLE_CONVERSION_LABEL))
			echo 'var google_conversion_label = "' . ADWORDS_TAG_GOOGLE_CONVERSION_LABEL . '";' . "\n";
		echo 'var google_custom_params = window.google_tag_params;' . "\n";
		echo 'var google_remarketing_only = true;' . "\n";
		echo '/* ]]> */' . "\n";
		echo '</script>' . "\n";

		echo '<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">' . "\n";
		echo '</script>' . "\n";

		echo '<noscript>' . "\n";
		echo '<div style="display:inline;">' . "\n";
		echo '<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/' . ADWORDS_TAG_GOOGLE_CONVERSION_ID . '/?value=0&amp;' . (tep_not_null(ADWORDS_TAG_GOOGLE_CONVERSION_LABEL) ? 'label=' . ADWORDS_TAG_GOOGLE_CONVERSION_LABEL . '&amp;' : '') . 'guid=ON&amp;script=0"/>' . "\n";
		echo '</div>' . "\n";
		echo '</noscript>' . "\n";
	}
}

/**
 * Adwords Dynamic Remarketing Tag Installer and Configuration Class
 *
 * adwords_tag_install class offers a modular and easy to manage method of 
 * configuration.  The class enables the base class to be configured and 
 * installed on the fly without the hassle of calling additional scripts 
 * or executing SQL.
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://www.richardfu.net/
 * @copyright Copyright 2013, Richard Fu
 * @author Richard Fu
 */
class adwords_tag_install {        
	/**
	 * The default_config array has all the default settings which should be all that is needed to make the base class work.
	 * @var array
	 */
	var $default_config;
	/**
	 * $attributes array holds information about this instance
	 * @var array
	 */
	var $attributes;
        
/**
 * adwords_tag_install class constructor 
 * @author Richard Fu
 * @version 1.0
 */        
	function adwords_tag_install() {
		$this->attributes = array();
		
		$x = 0;
		$this->default_config = array();
		$this->default_config['ADWORDS_TAG_ENABLED'] = array('DEFAULT' => 'true',
							  'QUERY' => "INSERT INTO `".TABLE_CONFIGURATION."` VALUES (NULL, 'Enable Adwords Dynamic Remarketing Tag?', 'ADWORDS_TAG_ENABLED', 'true', 'Enable Adwords Dynamic Remarketing Tag? This is a global setting and will turn them off completely.', GROUP_INSERT_ID, ".$x.", NOW(), NOW(), NULL, 'tep_cfg_select_option(array(''true'', ''false''),')");
		$x++;
		$this->default_config['ADWORDS_TAG_GOOGLE_CONVERSION_ID'] = array('DEFAULT' => '',
							  'QUERY' => "INSERT INTO `".TABLE_CONFIGURATION."` VALUES (NULL, 'Google Converstion ID (Required)', 'ADWORDS_TAG_GOOGLE_CONVERSION_ID', '1234567', 'Google Conversion ID. Must not be empty.', GROUP_INSERT_ID, ".$x.", NOW(), NOW(), NULL, '')");
		$x++;
		$this->default_config['ADWORDS_TAG_GOOGLE_CONVERSION_LABEL'] = array('DEFAULT' => '',
							  'QUERY' => "INSERT INTO `".TABLE_CONFIGURATION."` VALUES (NULL, 'Google Converstion ID (Optional)?', 'ADWORDS_TAG_GOOGLE_CONVERSION_LABEL', '', 'Google Conversion Label. Optional, leave empty to skip.', GROUP_INSERT_ID, ".$x.", NOW(), NOW(), NULL, '')");
		$x++;
		$this->default_config['ADWORDS_TAG_UNINSTALL'] = array('DEFAULT' => 'false',
							  'QUERY' => "INSERT INTO `".TABLE_CONFIGURATION."` VALUES (NULL, 'Uninstall Adwords Dynamic Remarketing Tag', 'ADWORDS_TAG_UNINSTALL', 'false', 'This will delete all of the entries in the configuration table for Adwords Dynamic Remarketing Tag', GROUP_INSERT_ID, ".$x.", NOW(), NOW(), 'tep_uninstall_adwords_tag', 'tep_cfg_select_option(array(''uninstall'', ''false''),')");
		$this->init();
	} # end class constructor
        
/**
 * Initializer - if there are settings not defined the default config will be used and database settings installed. 
 * @author Richard Fu
 * @version 1.0
 */        
	function init() {
		foreach( $this->default_config as $key => $value ){
			$container[] = defined($key) ? 'true' : 'false';
		} # end foreach
		$this->attributes['IS_DEFINED'] = in_array('false', $container) ? false : true;

		switch(true){
			case ( !$this->attributes['IS_DEFINED'] ):
				$this->eval_defaults();
				$sql = "SELECT configuration_key, configuration_value  
				        FROM " . TABLE_CONFIGURATION . " 
				        WHERE configuration_key LIKE 'ADWORDS_TAG_%'";
				$result = tep_db_query($sql);
				$num_rows = tep_db_num_rows($result);     
				$this->attributes['IS_INSTALLED'] = (sizeof($container) == $num_rows) ? true : false;
				if ( !$this->attributes['IS_INSTALLED'] ){
					$this->install_settings(); 
				}
				break;
			default:
				$this->attributes['IS_INSTALLED'] = true;
				break;
		} # end switch
	} # end function
        
/**
 * This function evaluates the default serrings into defined constants 
 * @author Richard Fu
 * @version 1.0
 */        
	function eval_defaults(){
		foreach( $this->default_config as $key => $value ){
			if (! defined($key))
				define($key, $value['DEFAULT']);
		} # end foreach
	} # end function
        
/**
 * This function removes the database settings (configuration and cache)
 * @author Richard Fu
 * @version 1.0
 */
	function uninstall_settings(){
		$cfgId_query = "SELECT configuration_group_id as ID FROM `".TABLE_CONFIGURATION_GROUP."` WHERE configuration_group_title = 'Adwords Tag'";
		$cfgID = tep_db_fetch_array( tep_db_query($cfgId_query) );
		tep_db_query("DELETE FROM `".TABLE_CONFIGURATION_GROUP."` WHERE `configuration_group_title` = 'Adwords Tag'");
		tep_db_query("DELETE FROM `".TABLE_CONFIGURATION."` WHERE configuration_group_id = '" . $cfgID['ID'] . "' OR configuration_key LIKE 'ADWORDS_TAG_%'");
	} # end function
        
/**
 * This function installs the database settings
 * @author Richard Fu
 * @version 1.0
 */        
	function install_settings(){
		$this->uninstall_settings();
		$sort_order_query = "SELECT MAX(sort_order) as max_sort FROM `".TABLE_CONFIGURATION_GROUP."`";
		$sort = tep_db_fetch_array( tep_db_query($sort_order_query) );
		$next_sort = $sort['max_sort'] + 1;
		$insert_group = "INSERT INTO `".TABLE_CONFIGURATION_GROUP."` VALUES (NULL, 'Adwords Tag', 'Options for Adwords Dynamic Tag by fuR', '".$next_sort."', '1')";
		tep_db_query($insert_group);
		$group_id = tep_db_insert_id();

		foreach ($this->default_config as $key => $value){
				$sql = str_replace('GROUP_INSERT_ID', $group_id, $value['QUERY']);
				tep_db_query($sql);
		}
	} # end function        
} # end class
	