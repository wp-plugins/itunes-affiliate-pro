<?php
/*  
Copyright 2012  Jorge A. Gonzalez  (email : jorge@zeropaid.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

Plugin Name: iTunes Affiliate Pro
Plugin URI: http://wordpress.org/extend/plugins/itunes-affiliate-pro/
Description: Automatically adds iTunes affiliate links to content.
Version: 1.2
Author: Jorge A. Gonzalez
Author URI: http://www.foxybay.com
License: GPL2
*/


if ( get_option("IAP-isactive") AND get_option("IAP-affiliate-code") ) {
    add_filter( 'the_content', 'IAP_ParseText', 99 );
} 

if ( !get_option("IAP-affiliate-code") ) {
   add_action('admin_notices', 'IAP_notice');
}

add_action('admin_menu', 'IAP_create_menu');

function IAP_create_menu() { 
    add_options_page('iTunes Affiliate Pro Options', 'iTunes Affiliate Pro', 'manage_options', 'IAP_menu', 'IAP_settings_page'); 
	add_action( 'admin_init', 'IAP_register' );
}
 
function IAP_register() {
    add_option("IAP-affiliate-code", "");
    add_option("IAP-isactive", "");
	register_setting( 'IAP-settings', 'IAP-affiliate-code' ); 
	register_setting( 'IAP-settings', 'IAP-isactive' ); 
}

function IAP_settings_page() { ?>
    <div class="wrap">
        <div id="icon-upload" class="icon32"></div> 
        <h2>iTunes Affiliate Pro Options</h2> 
        
        <p>To get started you need a free LinkShare account. <br /><br /><input type="submit" class="button" value="Create Account" onClick="window.open('http://goo.gl/HOVzV','external')" /></p>
        <p>Once you <a href="http://goo.gl/HOVzV" target="_blank">create a LinkShare account</a>, you will need to apply for the iTunes affiliate program.<br />It takes seconds to apply and you will be approved within minutes. <a href="http://goo.gl/9e8Ls" target="_blank">Click Here to Apply</a></p>
            
            <form method="post" action="options.php"> 
                <?php 
                settings_fields('IAP-settings'); 
                if( get_option("IAP-isactive") ){ $checked = "checked=\"checked\""; }  else { $checked = ""; }   
                ?>
                <table class="form-table">
                    <tr> 
                    <td nowrap valign="top" /><strong>LinkShare Affiliate ID</strong></th>
                    <td width="100%"><input type="text" name="IAP-affiliate-code" value="<?php echo get_option('IAP-affiliate-code'); ?>" size="50" /><br /><a href="http://goo.gl/TpcN4" target="_blank">Learn how to get your LinkShare Affiliate ID</a> / Example: <em>G/REHtk3*ZI</em></td>
                    </tr>  
                    <tr>
                    <td nowrap /><strong>Activate Filter</strong></th>
                    <td width="100%"><input type="checkbox" name="IAP-isactive" <?=$checked;?>/> Once you're ready to start adding affiliate links to your content click this box.</td>
                    </tr>  
                </table>
                <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>        
            </form> 
    </div>
<?php } 

 

function IAP_notice(){
    echo '<div class="error">
       <p><a href="/wp-admin/options-general.php?page=IAP_menu">iTunes Affiliate Pro</a> requires a free LinkShare account. <input type="submit" class="button" value="Create Account" onClick="window.open(\'http://goo.gl/HOVzV\',\'external\')"></p>
    </div>';
}


function IAP_ParseText( $text ) {
    
  $IAP_affiliate_URL = 'http://click.linksynergy.com/fs-bin/stat?id='.get_option("IAP-affiliate-code").'&offerid=146261&type=3&subid=0&tmpid=1826&RD_PARM1='; 
    
    preg_match_all("/<a href=\"http:\/\/itunes.apple.com\/us\/app\/(.*?)\".*?>(.*?)<\/a>/i", $text, $matches); 
    for($i=0;$i<count($matches[0]);$i++) { 
      if(!preg_match("/rel=[\"\']*external nofollow[\"\']*/",$matches[0][$i])) { 
        preg_match_all("/<a.*? href=\"(.*?)\"(.*?)>(.*?)<\/a>/i", $matches[0][$i], $matches1);   
            $text = str_replace($matches1[1][0],$IAP_affiliate_URL.urlencode($matches1[1][0]),$text);
            $text = str_replace(">".$matches1[3][0]."</a>"," target='_blank' rel='external nofollow'>".$matches1[3][0]."</a>",$text);
            
       }
     } 
return $text;
}



?>