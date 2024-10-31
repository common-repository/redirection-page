<?php
/*
Plugin Name: Redirection Page
Version: 1.2
Plugin URI: http://www.yusuf.asia/go/p4-homepage/
Description: Redirect your specified pages, it is usefull when you have 404/not-found pages. Go to <a href="options-general.php?page=redirection-page">Settings Page</a> to start redirection.
Author: Yusuf
Author URI: http://www.yusuf.asia/
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.*/

function redirectionpage_main() {
	$pages = get_option('redirectionpage_data');
	if (!empty($pages[2])) {
		foreach ($pages[2] as $source => $redir) {
			if ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] == $source){
				header('location:http://'.$redir);
				die();
			}
		}
	}
}
add_action('init','redirectionpage_main');

function redirectionpage_admin(){
	if (!empty($_GET['redirectionpage_action'])) {
		if($_GET['redirectionpage_action'] == 'add'){
			if (!empty ($_POST['source']) && !empty ($_POST['redir'])) {
				$data = get_option('redirectionpage_data');
				if (!in_array($_POST['source'],  $data[0])) {
					array_push($data[0], $_POST['source']);
					array_push($data[1], $_POST['redir']);
					$data[2] = array_combine($data[0], $data[1]);
					update_option('redirectionpage_data', $data);
					echo '<div class="updated"><p>Redirection Saved.</p></div>';
				} else {
					echo '<div class="error settings-error" id="setting-error-invalid_home"><p><strong>http://'.$_POST['source'].'</strong> is already found on database.</p></div>';
				}
			} else {
				echo '<div class="error settings-error" id="setting-error-invalid_home"><p>Redirection is empty.</p></div>';
			}
		}
		if ($_GET['redirectionpage_action'] == 'delete'){
			$data = get_option('redirectionpage_data');
			unset($data[0][$_GET['redirectionpage_no']]);
			unset($data[1][$_GET['redirectionpage_no']]);
			update_option('redirectionpage_data',$data);
	
			$data = get_option('redirectionpage_data');
			$newarray = array(array(), array(), array());
			foreach($data[0] as $value){
				if(!empty($value))
					array_push($newarray[0], $value);
			}
			foreach($data[1] as $value){
				if(!empty($value))
					array_push($newarray[1], $value);
			}
			if (!empty($newarray[0])) {
				$newarray[2] = array_combine($newarray[0], $newarray[1]);
				update_option('redirectionpage_data', $newarray);
			} else {
				update_option('redirectionpage_data', array(array(), array(), array()));
			}
		}
	}
	echo '
	<div class="wrap" id="wpmd_div"><h2>Redirection Page</h2>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
					<div id="wpmd_about" class="postbox">
						<h3 class="hndle"><span>About this Plugin:</span></h3>
							<div class="inside">
								<p><a href="http://www.yusuf.asia/go/p4-home/">Plugin Homepage</a></p>
								<p><a href="http://www.yusuf.asia/go/p4-support/">Support Forum</a></p>
								<p><a href="http://www.yusuf.asia/">Author</a></p>
							</div>
					</div>
				</div>
			</div>
			<div class="has-sidebar sm-padded" >
				<div id="post-body-content" class="has-sidebar-content">
					<div class="meta-box-sortabless">
						<div id="wpmd_satu" class="postbox">
							<h3 class="hndle"><span>Redirection</span></h3>
								<div class="inside">
									<ul><li>
									<br />
									<form method="post" action="options-general.php?page=redirection-page&redirectionpage_action=add">
									http://<input type="text" class="regular-text" name="source" value="">
									<span class="description">(your source page)</span>
									<br />
									<br />
									http://<input type="text" class="regular-text" name="redir" value="">
									<span class="description">(destination page)</span>
									<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="submit" name="submit" class="button" value="Add Redirection">
									</form>
									
									</li></ul>
								</div>
						</div>
						<div id="wpmd_dua" class="postbox">
							<h3 class="hndle"><span>Redirection list</span></h3>
								<div class="inside">
									<ul><li>';
									$data = get_option('redirectionpage_data');
									if (!empty ($data[2])) {
										$n = 0;
										foreach ($data[2] as $source => $redir) {
											echo '<p>http://'.$source.'</p><p><strong>Redirect to :</strong></p><p>http://'.$redir.'</p>';
											echo '<a href="options-general.php?page=redirection-page&redirectionpage_action=delete&redirectionpage_no=' .$n. ' ">delete</a>';
											echo '<hr />';
											$n++;
										}
									} else {
										echo 'There is no Redirection.';
									}
									echo '</li></ul>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	';

} 

function redirectionpage_admin_menu() {
	add_options_page('Redirection Setting Page','Redirection Page','manage_options','redirection-page','redirectionpage_admin');
}
add_action('admin_menu', 'redirectionpage_admin_menu');

function redirectionpage_active(){
	add_option('redirectionpage_data', array( array (), array (), array() ));
}

function redirectionpage_deactive(){
	delete_option('redirectionpage_data');
}

register_activation_hook( __FILE__, 'redirectionpage_active' );
register_deactivation_hook(__FILE__, 'redirectionpage_deactive'); 

?>