ABOUT

This a light-weight add-on for Google AdWords dynamic remarketing tag for osCommerce 2.2.x.

Dynamic remarketing lets you reach your past site visitors with ads that show the specific products people viewed on your site. Once you've set up your dynamic remarketing campaign, you'll need to add the dynamic remarketing tag, including custom parameters, to your site so your lists can start working.

More on: https://support.google.com/adwords/answer/3103357?hl=en


INSTALL
1. Copy new file:
catalog/includes/classes/adwords_tag.php

2. catalog/includes/footer.php
ADD AT BOTTOM:

<!-- MOD: BOF - AdWords Dynamic Remarketing Tag -->
<?php 
require(DIR_WS_CLASSES . 'adwords_tag.php');
$adwords_tag = new adwords_tag();
$adwords_tag->output();
?>
<!-- MOD: EOF - AdWords Dynamic Remarketing Tag -->


3. Run any page in catalog so it load the adwords_tag class

4. Change your AdWords ID and Label in admin -> configuration -> Adwords Tag
More on find your finding your ID: https://support.google.com/adwords/answer/3103509


---------------
Aurthor:	
Richard Fu
richardfu.net