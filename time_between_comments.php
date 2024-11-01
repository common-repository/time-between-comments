<?php
/*
Plugin Name: Time Between Comments
Version: 0.1
Plugin URI: http://wp123.info/plugins/time-between-commentstime-between-comments/
Description: Set time interval between comments.
Author: Levani Melikishvili
Author URI: http://wp123.info

Copyright 2010 Levani Melikishvili (email: levani9191@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

		function tbc_time_between_comments($commentdata) {
		global $wpdb;
		
		$options = get_option('TbcOptions');
		$tbc = $options['tbc'];
		$tbc_error = $options['tbc_error'];
		$tbc_error = str_replace("{number}",$tbc,$tbc_error);
			
			if ( $tbc != '' && $tbc > 0 ) {
			
		$ip = $_SERVER['REMOTE_ADDR']; //This is current user ip address
		
		//Get last comment time by the ip address
		$lasttime = $wpdb->get_row("SELECT comment_date FROM $wpdb->comments WHERE comment_author_IP = '$ip' ORDER BY comment_date DESC LIMIT 1");
			if ($lasttime != '') {
		$lasttime = strtotime($lasttime->comment_date);

			if (( time() - $lasttime ) < $tbc ) {
			wp_die( $tbc_error );
			} else {
			return $commentdata;
			}
			} else {
			return $commentdata;
			}
			}
			return $commentdata;
		}
		
		function tbc_add_config_page() {
			add_submenu_page('options-general.php', 'Time Between Comments Configuration', 'Time Between Comments', 10, basename(__FILE__), 'tbc_config_page');
		}
		
		function tbc_config_page() { 
		
		$options['tbc'] = 0;
		$options['tbc_error'] = "Please wait {number} seconds.";
		add_option('TbcOptions', $options);
		
		if ( isset($_POST['submit']) ) {
		if (isset($_POST['tbc']) && is_numeric($_POST['tbc'])) 
					$options['tbc'] = $_POST['tbc'];
		if (isset($_POST['tbc_error']) && $_POST['tbc_error'] != "") 
					$options['tbc_error'] = $_POST['tbc_error'];

				update_option('TbcOptions', $options);
		}
		$options = get_option('TbcOptions');
		?>
		
			<div class="wrap">
			
		<h2>Time Between Comments Settings</h2>
		<form action="" method="post" id="tbc-conf">
		
	<table class="form-table">
	<tbody>
	<tr valign="top">
	<th scope="row">Time between comments:</th>
	<td>
	<input name="tbc" id="tbc" class="small-text" value="<?php echo $options['tbc']; ?>" type="text">
	<label for="tbc">Seconds</label> <i>(<strong>0</strong> means no limitations.)</i>
	</td>
	</tr>
	
	<tr valign="top">
	<th scope="row">Error message:</th>
	<td>
	<textarea name="tbc_error" id="tbc_error" class="large-text code" cols="40" rows="5"><?php echo $options['tbc_error']; ?></textarea><br />
	<small><strong>{number}</strong> will be replaces with the amount of seconds.</small>
	</td>
	</tr>
	</tbody></table>
	
<p class="submit">
	<input name="submit" class="button-primary" value="Save Changes" type="submit">
</p>

		</form>

			</div>
			
		<?php }
		
		add_filter('preprocess_comment','tbc_time_between_comments');
		add_action('admin_menu', 'tbc_add_config_page');
?>