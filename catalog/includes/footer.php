<?php
/*
  $Id: footer.php 1739 2007-12-20 00:52:16Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require(DIR_WS_INCLUDES . 'counter.php');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="1">
  <tr class="footer">
    <td class="footer">&nbsp;&nbsp;<?php echo strftime(DATE_FORMAT_LONG); ?>&nbsp;&nbsp;</td>
    <td align="right" class="footer">&nbsp;&nbsp;<?php echo $counter_now . ' ' . FOOTER_TEXT_REQUESTS_SINCE . ' ' . $counter_startdate_formatted; ?>&nbsp;&nbsp;</td>
  </tr>
</table>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="smallText"><?php echo FOOTER_TEXT_BODY; ?></td>
  </tr>
</table>
<?php
  if ($banner = tep_banner_exists('dynamic', '468x50')) {
?>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><?php echo tep_display_banner('static', $banner); ?></td>
  </tr>
</table>
<?php
  }
?>

<!-- MOD: BOF - AdWords Dynamic Remarketing Tag -->
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
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/XXXXXXXXXX/?value=0&amp;label=YYYYYYYYYY&amp;guid=ON&amp;script=0"/>
</div>
</noscript> 

<?php 
function tep_get_page_info(){
	$scriptname = ltrim(basename($_SERVER['SCRIPT_NAME']));
	$return = array();
	switch($scriptname){
		case FILENAME_DEFAULT:
		case FILENAME_PRINTER_VIEW:
			global $category_depth;
			if($category_depth == 'top')
				$return[1] = 'home';
			else
				$return[1] = 'category';
			$return[0] = "''";
			break;
		case FILENAME_PRODUCT_INFO:
			global $_GET;
			if(tep_not_null($_GET['products_id'])){
				$return[0] = "'" . $_GET['products_id'] . "'";
				global $product_info;
				$return[2] = sprintf("%01.2f", $product_info['products_price']);
				if($new_price = tep_get_products_special_price($product_info['products_id']))
					$return[2] = sprintf("%01.2f", $new_price);
			}
			$return[1] = 'product';
			break;
		case FILENAME_SHOPPING_CART:
			global $cart;
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
				$return[2] = sprintf("%01.2f", $cart->total);
			}
			$return[1] = 'cart';
			break;
		case FILENAME_CHECKOUT_SHIPPING:
		case FILENAME_CHECKOUT_PAYMENT:
		case FILENAME_CHECKOUT_CONFIRMATION:
		case FILENAME_CHECKOUT_SUCCESS:
			global $order;
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
				$return[2] = sprintf("%01.2f", $order->info['total']);
			}
			$return[1] = 'purchase';
			break;
		case FILENAME_ADVANCED_SEARCH_RESULT:
			$return[0] = "''";
			$return[1] = 'searchresults';
		default:
			$return[0] = "''";
			$return[1] = 'siteview';
	}	
	return $return;
}
?>
<!-- MOD: EOF - AdWords Dynamic Remarketing Tag -->