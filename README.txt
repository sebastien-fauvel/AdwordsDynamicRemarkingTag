ABOUT

This a light-weight add-on for Google AdWords dynamic remarketing tag for osCommerce 2.2.x.

Dynamic remarketing lets you reach your past site visitors with ads that show the specific products people viewed on your site. Once you've set up your dynamic remarketing campaign, you'll need to add the dynamic remarketing tag, including custom parameters, to your site so your lists can start working.

More on: https://support.google.com/adwords/answer/3103357?hl=en


INSTALL
1. catalog/includes/footer.php
ADD AT BOTTOM:

<?php list($page_products_id, $page_type, $page_totalvalue) = tep_get_page_info(); ?>
<script type="text/javascript">
var google_tag_params = {
ecomm_prodid: <?php echo $page_products_id; ?>,
ecomm_pagetype: '<?php echo $page_type; ?>',
ecomm_totalvalue: '<?php echo $page_totalvalue; ?>'
};
</script>
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = XXXXXXXXXX;
var google_conversion_label = "YYYYYYYYYY";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/XXXXXXXXXX/?value=0&amp;label=YYYYYYYYYY&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>

<?php 
function tep_get_page_info(){
	$scriptname = ltrim(basename($PHP_SELF));
	$return = array();
	switch($scriptname){
		case FILENAME_DEFAULT:
			global $category_depth;
			if($category_depth == 'top')
				$return[1] = 'home';
			else
				$return[1] = 'category';
			$return[0] = "''";
			break;
		case FILENAME_PRODUCT_INFO:
			global $_GET, $currency, $currencies;
			if(tep_not_null($_GET['products_id'])){
				$return[0] = "'" . $_GET['products_id'] . "'";
				global $product_info;
				$rate = $currencies->currencies[$currency]['value'];
				$decimal_places = $currencies->currencies[$currency]['decimal_places'];
				$return[2] = number_format(tep_add_tax($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) * $rate, $decimal_places);
				if($new_price = tep_get_products_special_price($product_info['products_id']))
					$return[2] = number_format(tep_add_tax($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])), $decimal_places);
			}else{
				$return[0] = "''";
			}
			$return[1] = 'product';
			break;
		case FILENAME_SHOPPING_CART:
			global $cart, $currency, $currencies;
			if($cart && is_object($cart)){
				$products_id_list = array();
				foreach($cart->contents as $products_id => $products_content)
					$products_id_list[] = "'" . (int)$products_id . "'";
				if(count($products_id_list) <= 0)
					$return[0] = "''";
				elseif(count($products_id_list) == 1)
					$return[0] = $products_id_list[0];
				else
					$return[0] = '[' . implode(',', $products_id_list) . ']';
				$rate = $currencies->currencies[$currency]['value'];
				$decimal_places = $currencies->currencies[$currency]['decimal_places'];
				$return[2] = number_format($cart->show_total() * $rate, $decimal_places);
			}else{
				$return[0] = "''";
			}
			$return[1] = 'cart';
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
					$return[0] = "''";
				elseif(count($products_id_list) == 1)
					$return[0] = $products_id_list[0];
				else
					$return[0] = '[' . implode(',', $products_id_list) . ']';
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
				$return[2] = number_format($order->info['total'] * $rate, $decimal_places);
			}else{
				$return[0] = "''";
			}
			$return[1] = 'purchase';
			break;
		case FILENAME_ADVANCED_SEARCH_RESULT:
			$return[0] = "''";
			$return[1] = 'searchresults';
			break;
		default:
			$return[0] = "''";
			$return[1] = 'other';
	}	
	return $return;
}
?>


2. Change to you AdWords ID and Label(if you have, normally not needed) from 'XXXXXXXXXX' and 'YYYYYYYYYY'
More on find your finding your ID: https://support.google.com/adwords/answer/3103509


---------------
Aurthor:
Richard Fu
richardfu.net