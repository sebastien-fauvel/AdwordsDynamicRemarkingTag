ABOUT

This a light-weight add-on for Google AdWords dynamic remarketing tag for osCommerce 2.2.x and 2.3.x

Dynamic remarketing lets you reach your past site visitors with ads that show the specific products people viewed on your site. Once you've set up your dynamic remarketing campaign, you'll need to add the dynamic remarketing tag, including custom parameters, to your webpages so Google can memorize.

More on Google Support: https://support.google.com/adwords/answer/3103357?hl=en


INSTALL
1. Copy new file:
catalog/includes/classes/adwords_tag.php


2. catalog/includes/footer.php
ADD AT BOTTOM:

<!-- MOD: BOF - AdWords Dynamic Remarketing Tag -->
<?php
if (ADWORDS_TAG_ENABLED != 'false') {
	require(DIR_WS_CLASSES . 'adwords_tag.php');
	$adwords_tag = new adwords_tag();
	$adwords_tag->output();
}
?>
<!-- MOD: EOF - AdWords Dynamic Remarketing Tag -->


3. catalog/admin/includes/functions/general.php
ADD AT BOTTOM:

// Function to reset Adwords Tag database configuration entries
function tep_uninstall_adwords_tag($action){
	switch ($action){
		case 'uninstall':
			tep_db_query("DELETE FROM configuration_group WHERE configuration_group_title = 'Adwords Tag'");
			tep_db_query("DELETE FROM configuration WHERE configuration_key like 'ADWORDS_TAG_%'");
			break;
		default:
			break;
	}
	# The return value is used to set the value upon viewing
	# It's NOT returining a false to indicate failure!!
	return 'false';
}


4. Run any page in catalog so it initialize the adwords_tag class


5. Change your AdWords ID and Label in admin -> configuration -> Adwords Tag
More on find your finding your ID: https://support.google.com/adwords/answer/3103509


6. Test your tags using Google Chrome plugins:
Tag Assistant (by Google): https://chrome.google.com/webstore/detail/tag-assistant-by-google/kejbdjndbnbjgmefkgdddjlbokphdefk
Adwords Remarketing Tag Validation: https://chrome.google.com/webstore/detail/adwords-remarketing-tag-v/iokkmdmobnhjmhbapieilipodaaeohol


Addon link: http://addons.oscommerce.com/info/8853
Support thread: http://forums.oscommerce.com/topic/393619-addon-adwords-dynamic-remarketing-tag/
GitHub: https://github.com/furic/AdwordsDynamicRemarkingTag

---------------
http://richardfu.net/oscommerce-adwords-dynamic-remarketing-tag/

Aurthor:	
Richard Fu
richardfu.net