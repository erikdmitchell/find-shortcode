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
    
    public $version = '0.1.0';
	
	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts_styles'));
		add_action('admin_menu', array($this, 'add_menu_page'));
		add_action('wp_ajax_find_shortcode', array($this, 'ajax_find_shortcode'));
	}
	
	public function admin_scripts_styles() {
		wp_enqueue_script('find-shortcode-search-script', FIND_SHORTCODE_URL.'js/search.js', array('jquery'), $this->version, true);
		
		wp_enqueue_style('find-shortcode-css', FIND_SHORTCODE_URL.'css/admin.css', '', $this->version);
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

						<tr>
							<th scope="row"><label for="list-shortcodes">List Shortcodes</label></th>
							<td>
								<label for="list-all-shortcodes"><input type="checkbox" name="list-all-shortcodes" id="list-all-shortcodes">List All Shortcodes</label>
								<p class="description">This will list all shortcodes and their pages. It overrides the specific search above.</p>
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
    	if (isset($_POST['term']) && '' != $_POST['term'] ) :
    		$return = $this->find_shortcode($_POST['term']);
        else :
            $return = $this->list_all_shortcodes();
        endif;

		echo $return;
		
		wp_die();
	}

	protected function find_shortcode($string='') { 
		$html='';
		$list='';
		$get_post_types=get_post_types();
		$post_types=array();
		$counter=0;
        $shortcode_pattern = get_shortcode_regex();
		
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
                if ( preg_match_all( '/'. $shortcode_pattern .'/s', $post->post_content, $matches ) && array_key_exists( 2, $matches ) && in_array( $string, $matches[2] ) ) :
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

	protected function list_all_shortcodes() { 
		$html='';
		$list='';
		$get_post_types=get_post_types();
		$post_types=array();
        $shortcode_pattern = get_shortcode_regex();
		
		foreach ($get_post_types as $post_type) :
			$post_types[]=$post_type;
		endforeach;
		
		$posts=get_posts(array(
			'posts_per_page' => -1,
			'post_type' => $post_types
		));
 
		if (empty($posts))
        	return '';

		$list.='<ul class="all-shortcodes-list">';

			foreach ($posts as $post) :
                if ( preg_match_all( '/'. $shortcode_pattern .'/s', $post->post_content, $matches ) && array_key_exists( 2, $matches ) ) :
                    $shortcodes = array_unique($matches[2]);

                    $list.='<li class="post-page">';
                        $list .= '<a href="'.get_permalink($post->ID).'" target="_blank">';
                            $list .= $post->post_title;
                        $list .= '</a>';
                            
                        $list .= '<ul class="shortcodes-list">';
                            foreach ($shortcodes as $shortcode) :
                                $list .= '<li class="shortcode">'.$shortcode.'</li>';
                            endforeach;
                        $list .= '</ul>';
                            
                    $list .= '</li>'; 
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