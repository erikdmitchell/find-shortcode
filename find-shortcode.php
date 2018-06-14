<?php
/*
Plugin Name: Find Shortcode
Plugin URI:  
Description: Find if (and where) a shortcode is being used on a site.
Version:     0.1.0
Author:      Erik Mitchell
Author URI:  https://erikmitchell.net
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: find-shortcode
Domain Path: /languages
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('FIND_SHORTCODE_PATH', plugin_dir_path(__FILE__));
define('FIND_SHORTCODE_URL', plugin_dir_url(__FILE__));

class Find_Shortcode {
	
	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_styles'));
		add_action('admin_menu', array($this, 'add_menu_page'));
		add_action('wp_ajax_find_shortcode', array($this, 'ajax_find_shortcode'));
	}
	
	public function admin_scripts_styles() {
		wp_enqueue_script('find-shortcode-search-script', FIND_SHORTCODE_URL.'js/search.js', array('jquery'), '0.1.0', true);
	}
	
	public function add_menu_page() {
		add_management_page('Find Shortcode', 'Find Shortcode', 'manage_options', 'find-shortcode', array($this, 'admin_page'));
	}
	
	public function admin_page() {
		?>
		
		<div class="wrap">
			<h1>Find Shortcode</h1>
			
			<form name="find-shortcode" id="find-shortcode" method="post" action="">
				
				<table class="form-table">
					<tbody>
						
						<tr>
							<th scope="row"><label for="shortcode-search">Search Shortcode</label></th>
							<td>
								<input type="text" name="shortcode-search" id="shortcode-search" class="regular-text" value="" placeholder="Enter shortcode" />
								<p class="description">Enter shortcode slug with no '[]' or attributes</p>
							</td>
						</tr>
						
					</tbody>
				</table>
				
				<p class="submit">
					<input type="submit" name="submit" id="search-button" class="button button-primary" value="Search">
				</p>
				
			</form>
			
			<div id="find-shortcode-results"></div>
			
		</div>
		
		<?php
	}

	public function ajax_find_shortcode() {
		$return=$this->find_shortcode($_POST['term']);

		echo $return;
		
		wp_die();
	}

	public function find_shortcode($string='') { 
		$html='';
		$list='';
		$get_post_types=get_post_types();
		$post_types=array();
		$counter=0;
		
		foreach ($get_post_types as $post_type) :
			$post_types[]=$post_type;
		endforeach;
		
		$posts=get_posts(array(
			'posts_per_page' => -1,
			'post_type' => $post_types
		));
  
		if (empty($posts))
        	return '';

		$list.='<ul>';

			foreach ($posts as $post) :
		        // check the post content for the short code
				if (stripos($post->post_content, '['.$string)) :
					$list.='<li><a href="'.get_permalink($post->ID).'" target="_blank">'.$post->post_title.'</a></li>'; 
					$counter++; 
				endif;          
			endforeach;
		
		$list.='</ul>';

        $html.='<p>';
        	$html.='<b>Found '.$counter.' posts/pages</b>';
        	$html.=$list;
        $html.='</p>';
	
		return $html;
	}	
	
}

new Find_Shortcode();