<?php
/**
 * Bulk Noindex & Nofollow Toolkit Core Class
 *
 * Handles all the core operations required to run on a WordPress platform.
 *
 * @package bulk-noindex-toolkit
 * @since 3.4
 */


if(!class_exists('bulkNoindexToolkit')) 
{

	class bulkNoindexToolkit	
	{	
		public $index_val_set = '';
		public $follow_val_set = '';

		public function __construct()
	    {
	    		    	
	    	//identify and set a known SEO plugin currently being used by the site
			$this->set_active_seo_plugin();
	    		        
	    }

	   	/**
		 * Keep the bnitkmfd post_meta values in sync with the native SEO plugins
		 * even if the native plugins settings are changed from the post editor
		 * @access public
		 * @return void		 
		 */
	    public function after_updated_post($post_id, $post ){
	   
	   		$meta_key_noidx = $this->get_meta_keys('noindex');
	   		$meta_key_nofllw = $this->get_meta_keys('nofollow');

	   		$meta_key_noidx_cln = substr($meta_key_noidx,1);
	   		$meta_key_nofllw_cln = substr($meta_key_nofllw,1);
	   		


	   		//update or delete the bulk no index post_meta data (no )
	   		if(isset($_POST[$meta_key_noidx_cln]) && $_POST[$meta_key_noidx_cln] > 0){
	   			
	   			$noidx_field_cln = sanitize_text_field($_POST[$meta_key_noidx_cln]);	
				update_post_meta( $post_id, '_bnitk_mfd_meta-robots-noindex', $noidx_field_cln );  
				
	   		}else{
	   			delete_post_meta($post_id, '_bnitk_mfd_meta-robots-noindex');
	   		}

	   		//update or delete the bulk no index post_meta data (nofollow)
	   		if(isset($_POST[$meta_key_nofllw_cln]) && $_POST[$meta_key_nofllw_cln] > 0){

	   			$nofllw_field_cln = sanitize_text_field($_POST[$meta_key_nofllw_cln]);
	   			update_post_meta( $post_id, '_bnitk_mfd_meta-robots-nofollow', $nofllw_field_cln );
	   		
	   		}else{
	   			delete_post_meta($post_id, '_bnitk_mfd_meta-robots-nofollow');
	   		} 
	   			    
		}
	    	    
	    /**
		 * Check to see see if a meta robots tag should be implemented to noindex
		 * or nofollow the page
		 * @access public
		 * @return void		 
		 */
		public function check_meta_robots($term_page = False){
			
			//initialize the directive variables to empty
			$idx_directive = '';
			$follow_directive = '';
			$directive_array = array();

			if($this->active_seo_plugin == 'bnitkmfd' || $term_page == True){

				if(is_tax() || is_tag() || is_category()){
					$tag_id = get_queried_object()->term_id;
					
					$meta_data_noidx = get_term_meta($tag_id,'_bnitk_mfd_meta-robots-noindex');
					$meta_data_nofllw = get_term_meta($tag_id,'_bnitk_mfd_meta-robots-nofollow');	
					
				}elseif(is_singular()){
					$post_id = get_the_ID();
					$meta_data_noidx = get_post_meta($post_id,'_bnitk_mfd_meta-robots-noindex');
					$meta_data_nofllw = get_post_meta($post_id,'_bnitk_mfd_meta-robots-nofollow');
				}elseif(is_author()){
					$author_id = get_queried_object()->ID;
					if($this->active_seo_plugin == 'rankmath'){
						$rm_robots = get_user_meta($author_id, 'rank_math_robots', true);
						if(is_array($rm_robots)){
							$meta_data_noidx  = array( in_array('noindex',  $rm_robots) ? 1 : 0 );
							$meta_data_nofllw = array( in_array('nofollow', $rm_robots) ? 1 : 0 );
						} else {
							$meta_data_noidx  = get_user_meta($author_id, '_bnitk_mfd_meta-robots-noindex');
							$meta_data_nofllw = get_user_meta($author_id, '_bnitk_mfd_meta-robots-nofollow');
						}
					} else {
						$meta_data_noidx  = get_user_meta($author_id, '_bnitk_mfd_meta-robots-noindex');
						$meta_data_nofllw = get_user_meta($author_id, '_bnitk_mfd_meta-robots-nofollow');
					}
				}

				if(isset($meta_data_noidx[0])){					
					$idx_directive = ($meta_data_noidx[0] == 1) ? 'noindex' : 'index';	
					$this->index_val_set = $idx_directive;
				}

				if(isset($meta_data_nofllw[0])){					
					$follow_directive = ($meta_data_nofllw[0] == 1) ? 'nofollow' : 'follow';
					$this->follow_val_set = $follow_directive;
				}				

				if($idx_directive == 'noindex' || $follow_directive == 'nofollow'){
					
					//build the appropriate directive for the meta robots tag
					$directive_array[] = ($idx_directive == 'noindex') ? 'noindex' : 'index';
					$directive_array[] = ($follow_directive == 'nofollow') ? 'nofollow' : 'follow';
					
					//add the robots meta tag with the proper directive to the page
					$this->add_robots_meta_to_page($directive_array);
				}
				
			}			

		}

		/**
		 * Outputs a robots meta tag for the page or term
		 * @access public
		 * @return void		 
		 */
		public function add_robots_meta_to_page($directive_array = array()) {
	

			if($directive_array){
				$directive = implode(',',$directive_array);
				
				//this filter is used to debug the robots meta tag content				
				//add_filter( 'wp_robots', array(&$this,'list_hooks'),1);				
				
				//update the Yoast Meta Robots directive on tag pages
				if($this->active_seo_plugin == 'yoast'){

					add_filter( 'wpseo_robots', function( $robots ) use ( $directive_array ) {
						$fresh_robots = array();
						
						foreach(explode(', ',$robots) as $rVal){
							//if yoast has already set a noindex value, then obey that		
							if(in_array($rVal,array('noindex','index'))){							
								if($this->index_val_set && $rVal != $this->index_val_set){
									$fresh_robots[] = $this->index_val_set;
								}else{
									$fresh_robots[] = $rVal;
								}
							}
							
							//update follow/nofollow directive accordingly						
							if(in_array($rVal,array('nofollow','follow'))){
								$fresh_robots[] = ($this->follow_val_set) ? 'nofollow' : 'follow';

							}

						}

							echo "<!-- robots meta tag updated by Mad Fish bulk noindex plugin https://www.madfishdigital.com/wp-plugins/ -->\n";									        
				        	return implode(', ',$fresh_robots);
				    	
				    	}
					);
				//update the All in One SEO Meta Robots directive on tag pages
				}elseif($this->active_seo_plugin == 'aioseo'){					

					add_filter( 'aioseo_robots_meta', function( $robots ) use ( $directive_array ){

						if($directive_array){
							foreach($directive_array as $dKey => $dVal){

								$robots[$dVal] = $dVal;
							}					
						}

						return $robots;
					});

				//update the Rank Math Meta Robots directive on tag pages
				}elseif($this->active_seo_plugin == 'rankmath'){
					add_filter( 'rank_math/frontend/robots', function( $robots ) use ( $directive_array ){

						if($directive_array){
							foreach($directive_array as $dKey => $dVal){
								$robots[$dVal] = True;
							}					
						}

						return $robots;
					});

				}else{

					//push the directie to the WP Robots filter - custom robots directive
					add_filter( 'wp_robots', function( $robots ) use ( $directive_array ) {
						
						echo "<!-- robots meta tag added by Mad Fish bulk noindex plugin https://www.madfishdigital.com/wp-plugins/ -->\n";
						
						if($directive_array){
							foreach($directive_array as $dKey => $dVal){
								$robots[$dVal] = True;
							}					
						}
				        
				        return $robots;
				    	
				    	}
					);				
				}

			}

			
		}

		/**
		* This is function is for debugging the contents of the robots meta tag
		* Only used when debugging the output
		* @access public
		* @return void		 
		*/

		/**
		* Check to see if a specific plugin is active
		* can't rely on is_plugin_active() since it
		* may not have loaded before this plugin loads
		* @access public
		* @return void		 
		*/

		public function is_plugin_active_mfd($plugin){
			return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );

		}

		/**
		 * Set the appropriate post meta keys based on the installed SEO plugins
		 */
		public function set_meta_keys(){

			//if none are available, then use our post meta key

			//Yoast support
			$this->meta_keys['yoast'] = array( 
	        	'noindex' => '_yoast_wpseo_meta-robots-noindex', 
	        	'nofollow' => '_yoast_wpseo_meta-robots-nofollow' 
	        );

			//All in One SEO Support
	        $this->meta_keys['aioseo'] = array( 
	        	'noindex' => 'robots_noindex', 
	        	'nofollow' => 'robots_nofollow' 
	        );

	        //Rank Match SEO Support
	        $this->meta_keys['rankmath'] = array( 
	        	'noindex' => 'rank_math_robots', 
	        	'nofollow' => 'rank_math_robots' 
	        );

	        //Our own support
	       	$this->meta_keys['bnitkmfd'] = array( 
	        	'noindex' => '_bnitk_mfd_meta-robots-noindex', 
	        	'nofollow' => '_bnitk_mfd_meta-robots-nofollow'
	        );
		}
		
		/**
		 * Identify the active SEO plugins that are being used
		 * This way we don't add multiple meta robots tags to a page
		 * @access public
		 * @return void		 
		 */
		public function set_active_seo_plugin(){
			
			//if Yoast or AIOSEO are already installed, use thier post meta key instead of reinventing the wheel by using our own

			try {

				//double check that the get_plugins function has been loaded
				if ( ! function_exists( 'get_plugins' ) ) {
					require_once ABSPATH . 'wp-admin/includes/plugin.php';

				}

				//set the default value assuming Yoast and AIOSEO are not active
				$this->active_seo_plugin = 'bnitkmfd';

				//check to see if the yoast or AIOSEO plugins are active
				$apl=get_option('active_plugins');
				$plugins=get_plugins();
				$activated_plugins=array();
				foreach ($apl as $p){           
				    if(isset($plugins[$p])){				    	
				    	
				    	//confirm that Yoast is installed
				        if($plugins[$p]['Name'] == 'Yoast SEO'){
				        	$this->active_seo_plugin = 'yoast';	
				        
				        //confirm that AIOSEO is installed
				        }elseif($plugins[$p]['Name'] == 'All in One SEO Pro' || $plugins[$p]['Name'] == 'All in One SEO'){
				        	$this->active_seo_plugin = 'aioseo';
				        }elseif ($plugins[$p]['Name'] == 'Rank Math SEO') {
				        	$this->active_seo_plugin = 'rankmath';

				        }
				    }           
				}

					
	    		
			}catch (exception $e) {
			    //code to handle the exception
			    $this->active_seo_plugin = 'bnitkmfd';

			}			

		}

		/**
		 * Return the value of the current active SEO plugin in use
		 * @access public
		 * @return void		 
		 */

		public function get_seo_plugin(){
			 		    
			return $this->active_seo_plugin;
		}	

		/**
		 * Populate an array that contains the appropriate post_meta values
		 * @access public
		 * @return void		 
		 */

		public function get_meta_keys($directive = 'noindex'){
			
			$this->set_meta_keys();	    		    	

			return $this->meta_keys[$this->active_seo_plugin][$directive];
		}

		/**
		 * Get the appropriate user_meta key for storing author robots settings
		 * @access public
		 * @return string
		 */
		public function get_author_meta_key($directive = 'noindex'){
			// Rank Math uses its own user meta key (array-based, synced with its UI)
			if($this->active_seo_plugin == 'rankmath'){
				return 'rank_math_robots';
			}
			// Yoast and AIOSEO don't support per-author noindex/nofollow via user meta,
			// so always fall back to the plugin's own user meta key
			return '_bnitk_mfd_meta-robots-' . $directive;
		}

		/**
		 * create_menu function
		 * generate the link to the options page under settings
		 * @access public
		 * @return void
		 */
		public function create_menu() {
		  	
		  	//add the link to the tools section of the wordpress admin area
			add_submenu_page( 'tools.php', 'Bulk NoIndex/Nofollow Toolkit', 'Bulk NoIndex/NoFollow', 'manage_options', 'no-index-toolkit', array($this,'options_page')); 

		}
	
		/**
		 * create_search_form function
		 * generate the search box for the table
		 * @access public
		 * @return void
		 */

		public function create_search_box(){

			//set the search value if we're in the middle of a search
			$search_filter_val = '';
			if ( isset( $_GET['s'] ) AND $_GET['s'] ){
			$search_filter_val = sanitize_text_field($_GET['s']);
			}
            			
		    $rendered_html = '<form name="search" method="post">';
		    $rendered_html .= '<input type="hidden" name="page" value="no-index-toolkit" />';		           
		    $rendered_html .= '<p class="search-box">';
			$rendered_html .= '<label class="screen-reader-text" for="search-res-search-input">Search:</label>';
			$rendered_html .= '<input type="search" id="search-res-search-input" name="s" value="'.esc_attr($search_filter_val).'">';
			$rendered_html .= '<input type="submit" id="search-submit" class="button" value="Search"></p>';
			$rendered_html .= '</form>';

			return $rendered_html;
		}


		/**
		 * xss_clean function
		 * sanitize the search input to prevent Cross-Site Scripting (XSS) attack
		 * @access public
		 * @return void
		 */

		public function xss_clean($data)
		{
		return sanitize_text_field( $data );
		}


		/**
		 * options_page function
		 * generate the options page in the wordpress admin
		 * @access public
		 * @return void
		 */
		public function options_page() {
			
			//include the extendedtable class
			include('bulk-noindex-toolkit-table.php');

			//add the jquery javascript to the options page
			wp_enqueue_script( 'bulk-toolkit',  plugin_dir_url( __FILE__ ) .'../js/bulk-toolkit.js', array('jquery'), '1.11' );

			//add the CSS stylesheet to the options page
			wp_enqueue_style('bulk-toolkit',plugin_dir_url( __FILE__ ) .'../css/bulk-toolkit.css');

			
			//build the table that contains the list of posts/pages on the options page
			echo '<div class="wrap">';            
            echo '<div class="head">';
            echo '	<h2>Bulk NoIndex and NoFollow Posts</h2>';
            echo '</div>';
            echo '<div class="logo-col">';
            echo '<img src="'.plugin_dir_url( __FILE__ ) .'../img/madfishdigital-logo.png" width="200" alt="Mad Fish Digital Logo">';
            echo '<div class="docTxt">';            
            echo '<p>Built by <a href="https://www.madfishdigital.com/" target="_blank">Mad Fish Digital</a> to bulk manage NoIndex and NoFollow directives across posts, pages, categories, and author URLs. Toggle robots meta tags using the tabs below.</p>
            	<p>Supports <strong>Yoast SEO</strong>, <strong>AIOSEO</strong>, and <strong>Rank Math</strong>. If you activate or deactivate any of these plugins, review your settings here to ensure directives remain in sync.</p>
            	<p class="red-clr"><strong>Use with caution.</strong> NoIndexing a URL removes it from search engine results. The plugin creator is not responsible for unintended changes to your site\'s indexation.</p>
            	';

            echo '</div>';
            
            //div tag to handle notifications
            echo '<div class="request-notification"></div>';
         	
         	//show the correct tab based on the GET param
         	if(isset($_GET['tab']) && $_GET['tab'] == 'cats'){
         		$tab1 = '';
         		$tab2 = 'class="active"';
         		$tab3 = '';
         	} elseif(isset($_GET['tab']) && $_GET['tab'] == 'authors'){
         		$tab1 = '';
         		$tab2 = '';
         		$tab3 = 'class="active"';
         	} else {
         		$tab1 = 'class="active"';
         		$tab2 = '';
         		$tab3 = '';
         	}

            
			echo '<div id="container">
				  <header class="tabs-nav">
				    <ul>
				      <li '.$tab1.'><a href="'.esc_url(sanitize_text_field(remove_query_arg(array('ct','orderby','order','items_per_page','paged'))).'&tab=posts').'">Posts</a></li>
				      <li '.$tab2.'><a href="'.esc_url(sanitize_text_field(remove_query_arg(array('pt','orderby','order','items_per_page','paged'))).'&tab=cats').'">Categories</a></li>
				      <li '.$tab3.'><a href="'.esc_url(sanitize_text_field(remove_query_arg(array('pt','ct','orderby','order','items_per_page','paged'))).'&tab=authors').'">Authors</a></li>
				      
				    </ul>
				  </header>';

			echo '<section class="tabs-content">';            



            $BNI_Table = new BNI_MFD_WP_Table(); 
            $BNI_Table->set_search_filter();


            if(!isset($_GET['tab']) || $_GET['tab'] == 'posts' || !in_array($_GET['tab'],array('posts','cats','authors'))){
	        	echo '<div class="tabList" id="tab1" >';
	            echo '<div>';            
					//render the search filter            
	            	echo $this->create_search_box();

		            //render the items per page filter            
		            $BNI_Table->show_items_per_page();
	            
	            echo '</div>';
				
	            echo '<form id="bulk-update" method="post" rel="pageForm">';    
	            echo '<input type="hidden" name="nonce" value="'.wp_create_nonce('bulk-noindex').'">';        
	            $BNI_Table->prepare_items_post();            
	            $BNI_Table->display();
	            echo '</form>';

	            echo '</div>';  //end tab

	        }


            if(isset($_GET['tab'])  && $_GET['tab'] == 'cats'){
	            echo '<div class="tabList" id="tab2">';

 				echo '<div>';            
					//render the search filter            
	            	echo $this->create_search_box();

		            //render the items per page filter            
		            $BNI_Table->show_items_per_page();
	            
	            echo '</div>';

	            echo '<form id="bulk-update" method="post" rel="catForm">';    
	            echo '<input type="hidden" name="nonce" value="'.wp_create_nonce('bulk-noindex').'">';        

	            $BNI_Table->prepare_items_cats();            
	            $BNI_Table->display();
	            echo '</form>';

	            echo '</div>'; 
	        }

	        if(isset($_GET['tab']) && $_GET['tab'] == 'authors'){
	            echo '<div class="tabList" id="tab3">';

 				echo '<div>';
					//render the search filter
	            	echo $this->create_search_box();

		            //render the items per page filter
		            $BNI_Table->show_items_per_page();

	            echo '</div>';

	            echo '<form id="bulk-update" method="post" rel="authorForm">';
	            echo '<input type="hidden" name="nonce" value="'.wp_create_nonce('bulk-noindex').'">';

	            $BNI_Table->prepare_items_authors();
	            $BNI_Table->display();
	            echo '</form>';

	            echo '</div>';
	        }

            echo '</section>';
			echo '</div>';

            echo '</div>'; //end main wrapper


		}

	
		
		/**
		 * Check Page Status function
		 * this function serves as a fallback to implement a robots meta tag incase 
		 * no common SEO plugin is being used to implement the robots meta tag directives
		 * @access public
		 * @return void
		 */
		public function check_page_status(){
						

			if(is_archive()){
				$this->check_meta_robots(True);				
			}elseif(is_singular()){
				$this->check_meta_robots(False);	
			}			
		
		}	

		/**
		 * Update bulk pages noindex and nofollow status
		 * this function is used as an AJAX callback to modify the noindex or nofollow 
		 * status in bulk for categories and terms
		 * @access public
		 * @return void
		 */
		public function update_cat_bulk_callback(){
					

			$bulk_directive = sanitize_text_field($_POST['directive']);			
			$nonce = sanitize_text_field($_POST['nonce']);

			if(current_user_can('manage_options') && wp_verify_nonce( $nonce, 'bulk-noindex' ) ){
			

				if(isset($_POST) && $bulk_directive != '-1'){

					//set the appropriate directive based on the post
					$directive = explode('_',$bulk_directive);

					//set the appropriate post_meta key based on the current SEO plugin
					$active_key = $this->get_meta_keys($directive[0]);
					
					//set the appropriate post_meta key value to use based on the directve received from the bulk drop down form
					$key_val = ($directive[1] == 'set') ? 1 : 0;

					if(isset($_POST['post_ids'])){
						$post_id_vals = $_POST['post_ids'];
						$post_id_vals = array_map( 'absint', $post_id_vals );

					// Verify the user can edit each individual post
					foreach($post_id_vals as $idx => $pid){
						if( !current_user_can('edit_post', $pid) ){
							$status = array(
								'status' => 'err',
								'msg' => 'You do not have permission to edit one or more of the selected posts.'
							);
							echo json_encode($status);
							wp_die();
						}
					}

						foreach($post_id_vals as $idx => $post_id){

							
							//update each post with the new directive and key value
							if($key_val == 0){

								if($active_key){
									
									//we keep track of the current setting with a custom meta value
									//since Yoast and/or AISEO plugins are not yet suppoted for categories
									delete_term_meta($post_id, '_bnitk_mfd_meta-robots-'.$directive[0]);
								}
								
							}else{
								
								if($active_key){
																										
									//we keep track of the current setting with a custom meta value
									//just in case the Yoast or AISEO plugin are disabled
									update_term_meta( $post_id, '_bnitk_mfd_meta-robots-'.$directive[0], $key_val );
								} 
							}
							
						}
						$msg_post = (count($_POST['post_ids']) == 1) ? 'post' : 'posts';

						$msg_nfi = ($key_val == 1) ? 'added' : 'removed';
						
						$status = array(
							'status' => 'OK',
							'msg' => count($_POST['post_ids']).' '.$msg_post.' have had the '.ucwords($directive[0]).' robots direcive '.$msg_nfi,
							'directive' => $directive[0],
							'val' => $key_val,
							'post_ids' => $post_id_vals,

						);

					}else{
						$status = array(
							'status' => 'err',
							'msg' => 'No posts were selected'
						);

					}

					echo json_encode($status);

				}
			}else{
				$status = array(
					'status' => 'err',
					'msg' => 'Security check failed. No posts were changed.'
				);
				echo json_encode($status);
			}

			wp_die(); 

		}

		/**
		 * Update bulk author noindex and nofollow status
		 * this function is used as an AJAX callback to modify the noindex or nofollow
		 * status in bulk for author archive pages
		 * @access public
		 * @return void
		 */
		public function update_author_bulk_callback(){

			$bulk_directive = sanitize_text_field($_POST['directive']);
			$nonce = sanitize_text_field($_POST['nonce']);

			if(current_user_can('manage_options') && wp_verify_nonce( $nonce, 'bulk-noindex' ) ){

				if(isset($_POST) && $bulk_directive != '-1'){

					//set the appropriate directive based on the post
					$directive = explode('_',$bulk_directive);

					//set the appropriate post_meta key value to use
					$key_val = ($directive[1] == 'set') ? 1 : 0;

					if(isset($_POST['post_ids'])){
						$post_id_vals = $_POST['post_ids'];
						$post_id_vals = array_map( 'absint', $post_id_vals );

						foreach($post_id_vals as $idx => $author_id){

							if($this->active_seo_plugin == 'rankmath'){

								$rm_robots_dat = get_user_meta($author_id, 'rank_math_robots', true);
								if(!is_array($rm_robots_dat)){
									$rm_robots_dat = array();
								}

								if($key_val == 0){
									// Remove the directive from the Rank Math robots array
									$search_key = array_search($directive[0], $rm_robots_dat);
									if($search_key !== false){
										// Replace with the positive directive (noindex->index, nofollow->follow)
										$rm_robots_dat[$search_key] = str_replace('no', '', $directive[0]);
									}
								} else {
									// Add the directive to the Rank Math robots array
									$positive = str_replace('no', '', $directive[0]);
									$search_key = array_search($positive, $rm_robots_dat);
									if($search_key !== false){
										$rm_robots_dat[$search_key] = $directive[0];
									} elseif(!in_array($directive[0], $rm_robots_dat)){
										$rm_robots_dat[] = $directive[0];
									}
								}

								update_user_meta( $author_id, 'rank_math_robots', $rm_robots_dat );

							} else {

								if($key_val == 0){
									delete_user_meta($author_id, '_bnitk_mfd_meta-robots-'.$directive[0]);
								} else {
									update_user_meta( $author_id, '_bnitk_mfd_meta-robots-'.$directive[0], $key_val );
								}
							}

							// Always keep plugin's own meta in sync as a fallback
							if($key_val == 0){
								delete_user_meta($author_id, '_bnitk_mfd_meta-robots-'.$directive[0]);
							} else {
								update_user_meta( $author_id, '_bnitk_mfd_meta-robots-'.$directive[0], $key_val );
							}

						}

						$msg_author = (count($_POST['post_ids']) == 1) ? 'author' : 'authors';
						$msg_nfi = ($key_val == 1) ? 'added' : 'removed';

						$status = array(
							'status' => 'OK',
							'msg' => count($_POST['post_ids']).' '.$msg_author.' have had the '.ucwords($directive[0]).' robots directive '.$msg_nfi,
							'directive' => $directive[0],
							'val' => $key_val,
							'post_ids' => $post_id_vals,
						);

					}else{
						$status = array(
							'status' => 'err',
							'msg' => 'No authors were selected'
						);
					}

					echo json_encode($status);

				}
			}else{
				$status = array(
					'status' => 'err',
					'msg' => 'Security check failed. No authors were changed.'
				);
				echo json_encode($status);
			}

			wp_die();

		}

		/**
		 * Update bulk pages noindex and nofollow status
		 * this function is used as an AJAX callback to modify the noindex or nofollow 
		 * status of bulk posts/pages		 
		 * @access public
		 * @return void
		 */
		public function update_page_bulk_callback(){
					

			$bulk_directive = sanitize_text_field($_POST['directive']);			
			$nonce = sanitize_text_field($_POST['nonce']);
			
			if(current_user_can('edit_posts') && wp_verify_nonce( $nonce, 'bulk-noindex' ) ){

				if(isset($_POST) && $bulk_directive != '-1'){

					//set the appropriate directive based on the post
					$directive = explode('_',$bulk_directive);

					//set the appropriate post_meta key based on the current SEO plugin
					$active_key = $this->get_meta_keys($directive[0]);
					
					//set the appropriate post_meta key value to use based on the directve received from the bulk drop down form
					$key_val = ($directive[1] == 'set') ? 1 : 0;

					if(isset($_POST['post_ids'])){
						$post_id_vals = $_POST['post_ids'];
						$post_id_vals = array_map( 'absint', $post_id_vals );

						// Verify the user can edit each individual post
						foreach($post_id_vals as $idx => $pid){
							if( !current_user_can('edit_post', $pid) ){
								$status = array(
									'status' => 'err',
									'msg' => 'You do not have permission to edit one or more of the selected posts.'
								);
								echo json_encode($status);
								wp_die();
							}
						}

						foreach($post_id_vals as $idx => $post_id){

							
							
						
						//since AISEO uses it's own DB tables to store the post's robots settings
						//we need to access the post settings via AISEO

						if($this->active_seo_plugin == 'aioseo'){
							
							$postAISEO = aioseo()->core->db->start( 'aioseo_posts' )
							->where( 'post_id', $post_id )
							->run()
							->model( 'AIOSEO\\Plugin\\Common\\Models\\Post' ); 					
						}


							/**
							* Always update the fallback post meta key,
							* that way if any of the supported plugins are disabled, 
							* we don't lose track of the pages which should be noindexed
							* remove the fallback post_meta key if we're no longer nonindexing
							**/


							//update each post with the new directive and key value
							if($key_val == 0){

								if($active_key){

									//check to see which plugin is being used
									switch($this->active_seo_plugin){

										case "aioseo":
									
											
											$postAISEO->{$active_key} = false;


											//if there are no longer any custom AISEO settings for the post
											//reset the "Use default settings" option

											if($postAISEO->robots_noindex == 0 && 
												$postAISEO->robots_nofollow == 0 &&
												$postAISEO->robots_noarchive == 0 &&
												$postAISEO->robots_notranslate == 0 &&
												$postAISEO->robots_noimageindex == 0 &&
												$postAISEO->robots_nosnippet == 0 &&
												$postAISEO->robots_noodp == 0 &&

												$postAISEO->robots_max_snippet == -1 &&
												$postAISEO->robots_max_videopreview == -1 &&
												$postAISEO->robots_max_imagepreview == 'large'){
																					

												$postAISEO->robots_default = true;	
											}									
											
											$postAISEO->save();
										break;
										case "rankmath":
										
											$foundKey = 0;
											$rm_robots_dat = get_post_meta($post_id,'rank_math_robots');

											//loop through the rankmath postmeta value for the post's robots settings
											
											foreach($rm_robots_dat as $k1 => $r1){

												$keyFind = array_search($directive[0],$r1);

												//update the noindex array vaule to index
												if($directive[0] == 'noindex'){
													$rm_robots_dat[$k1][$keyFind] = 'index';
													$foundKey = $k1;
												}

												//remove the nofllow array vaule and resort the array
												if($directive[0] == 'nofollow'){
													$rm_robots_dat[$k1][$keyFind] = 'follow';
													unset($rm_robots_dat[$k1][$keyFind]);
													array_values($rm_robots_dat[$k1]);
													$foundKey = $k1;
												}											
												
											}

											update_post_meta( $post_id, sanitize_text_field( $active_key ), $rm_robots_dat[$foundKey] );

										break;	
										case "yoast":
											delete_post_meta($post_id, sanitize_text_field( $active_key ));
										break;								
										
									}
									//no matter what, we keep track of the current setting 
									//just in case the Yoast or AISEO plugin are disabled
									delete_post_meta($post_id, '_bnitk_mfd_meta-robots-'.$directive[0]);
								}
								
							}else{
								
								
								if($active_key){
									//check to see which plugin is being used
									switch($this->active_seo_plugin){

										case "aioseo":
											$postAISEO->robots_default = false; 
											$postAISEO->{$active_key} = $key_val;
											$postAISEO->save();  
										break;
										case "rankmath":

											$foundKey = 0;
											$rm_robots_dat = get_post_meta($post_id,'rank_math_robots');					

											//loop through the rankmath postmeta value for the post's robots settings
											//print_r($rm_robots_dat);

											foreach($rm_robots_dat as $k1 => $r1){
												$keyFind = array_search(str_replace('no','',$directive[0]),$r1);
												
												
												//print_r($r1);
												//print 'directive: '.$directive[0]."\n";
												//print 'keyfind: '.$keyFind."\n"; 

												//update the noindex or nofollow array value accordingly
												if($directive[0] == 'noindex' || $directive[0] == 'nofollow'){

													if($keyFind != ''){
														$rm_robots_dat[$k1][$keyFind] = $directive[0];
														$foundKey = $k1;
													}else{								

														if(!in_array($directive[0],$r1)){
															$rm_robots_dat[$k1][] = $directive[0];	
														}
														
													}
													

												}

											}
											//print_r($rm_robots_dat);
											//exit;
											update_post_meta( $post_id, sanitize_text_field( $active_key ), $rm_robots_dat[$foundKey] );

										break;
										case "yoast":
											update_post_meta( $post_id, sanitize_text_field( $active_key ), $key_val );
										break;
										
										
									}
									//no matter what, we keep track of the current setting 
									//just in case the Yoast or AISEO plugin are disabled
									update_post_meta( $post_id, '_bnitk_mfd_meta-robots-'.$directive[0], $key_val );
								} 
							}
							
						}
						$msg_post = (count($_POST['post_ids']) == 1) ? 'post' : 'posts';

						$msg_nfi = ($key_val == 1) ? 'added' : 'removed';
						
						$status = array(
							'status' => 'OK',
							'msg' => count($_POST['post_ids']).' '.$msg_post.' have had the '.ucwords($directive[0]).' robots direcive '.$msg_nfi,
							'directive' => $directive[0],
							'val' => $key_val,
							'post_ids' => $post_id_vals,

						);

					}else{
						$status = array(
							'status' => 'err',
							'msg' => 'No posts were selected'
						);

					}

					echo json_encode($status);

				}
			}else{
				$status = array(
					'status' => 'err',
					'msg' => 'Security check failed. No posts were changed.'
				);
				echo json_encode($status);
			}

			wp_die(); 

		}

		/**
		 * Update a single post/page's noindex and nofollow status
		 * this function is used as an AJAX callback to modify the noindex/nofollow 
		 * status of a single post or page 
		 * @access public
		 * @return void
		 */
		public function update_page_callback() {
				
				$new_val = 0;								
				
				//double check that the user has the neccessary permissions to edit posts	
				//and that the nonce is correct

			
				$post_id = (int)sanitize_text_field($_POST['post_id']);
				$nonce = sanitize_text_field($_POST['nonce']);
				
				if(current_user_can( 'edit_post', $post_id) && wp_verify_nonce( $nonce, 'bulk-noindex' )){
					
					if(isset($_POST) && isset($_POST['result'])){
						
						$directive = sanitize_text_field(str_replace('[]','',$_POST['check_class']));
						
						$active_key = $this->get_meta_keys($directive);
				
						
						$post_id = (int)sanitize_text_field($_POST['post_id']);
						$key_val = (sanitize_text_field($_POST['result']) == 1) ? 1 : 0;
					
						
				
						//since AISEO uses it's own DB tables to store the post's robots settings
						//we need to access the post settings via AISEO

						if($this->active_seo_plugin == 'aioseo'){
							
							$postAISEO = aioseo()->core->db->start( 'aioseo_posts' )
							->where( 'post_id', $post_id )
							->run()
							->model( 'AIOSEO\\Plugin\\Common\\Models\\Post' ); 					
						}

						/**
						* Always update the fallback post_meta key that's specific for this plugin
						* that way if any of the supported plugins are disabled, we don't lose
						* track of the pages which should be noindexed
						**/
							
						if($key_val == 0){

							if($active_key){

								//check to see which plugin is being used
								switch($this->active_seo_plugin){

									case "aioseo":
								
										
										$postAISEO->{$active_key} = false;


										//if there are no longer any custom AISEO settings for the post
										//reset the "Use default settings" option

										if($postAISEO->robots_noindex == 0 && 
											$postAISEO->robots_nofollow == 0 &&
											$postAISEO->robots_noarchive == 0 &&
											$postAISEO->robots_notranslate == 0 &&
											$postAISEO->robots_noimageindex == 0 &&
											$postAISEO->robots_nosnippet == 0 &&
											$postAISEO->robots_noodp == 0 &&

											$postAISEO->robots_max_snippet == -1 &&
											$postAISEO->robots_max_videopreview == -1 &&
											$postAISEO->robots_max_imagepreview == 'large'){
																				

											$postAISEO->robots_default = true;	
										}									
										
										$postAISEO->save();
									break;
									case "rankmath":
										
										$foundKey = 0;
										$rm_robots_dat = get_post_meta($post_id,'rank_math_robots');

										//loop through the rankmath postmeta value for the post's robots settings
										foreach($rm_robots_dat as $k1 => $r1){
											$keyFind = array_search($directive,$r1);

											
											
											//update the noindex array vaule to index
											if($directive == 'noindex'){
												$rm_robots_dat[$k1][$keyFind] = 'index';
												$foundKey = $k1;
											}

											//remove the nofllow array vaule and resort the array
											if($directive == 'nofollow'){
												$rm_robots_dat[$k1][$keyFind] = 'follow';
												unset($rm_robots_dat[$k1][$keyFind]);
												array_values($rm_robots_dat[$k1]);
												$foundKey = $k1;
											}
											
											
										}
										
										update_post_meta( $post_id, sanitize_text_field( $active_key ), $rm_robots_dat[$foundKey] );

									break;								
									case "yoast":
										delete_post_meta($post_id, sanitize_text_field( $active_key ));
									break;								
									
								}
								//no matter what, we keep track of the current setting 
								//just in case the Yoast or AISEO plugin are disabled
								delete_post_meta($post_id, '_bnitk_mfd_meta-robots-'.$directive);				
							}
							
						}else{
							
							if($active_key){
								//check to see which plugin is being used
								switch($this->active_seo_plugin){

									case "aioseo":
										$postAISEO->robots_default = false; 
										$postAISEO->{$active_key} = $key_val;
										$postAISEO->save();  
									break;
									case "rankmath":

										$foundKey = 0;
										$rm_robots_dat = get_post_meta($post_id,'rank_math_robots');
										
										//loop through the rankmath postmeta value for the post's robots settings
										
										foreach($rm_robots_dat as $k1 => $r1){
											$keyFind = array_search(str_replace('no','',$directive),$r1);
											
											//update the noindex or nofollow array value accordingly
											if($directive == 'noindex' || $directive == 'nofollow'){
												
												if($keyFind != ''){
													$rm_robots_dat[$k1][$keyFind] = $directive;
													$foundKey = $k1;
												}else{								
													if(!in_array($directive,$rm_robots_dat[$k1])){	
														$rm_robots_dat[$k1][] = $directive;
													}
												}
												

											}

										}

										update_post_meta( $post_id, sanitize_text_field( $active_key ), $rm_robots_dat[$foundKey] );

									break;
									case "yoast":
										update_post_meta( $post_id, sanitize_text_field( $active_key ), $key_val );
									break;
									
									
								}
								//no matter what, we keep track of the current setting 
								//just in case the Yoast or AISEO plugin are disabled
								update_post_meta( $post_id, '_bnitk_mfd_meta-robots-'.$directive, $key_val );
							} 
									
						}
						


				
						//let the user know that the update was successful
						if($key_val == 1){
							$message = 'The post\'s robots directive has been set to '.$directive;
						}else{
							$message = 'The '.$directive.' directive has been removed from the post';
						}
						$status = array(
							'status' => 'OK',
							'directive' => $directive,
							'msg' => $message,
							'val' => $key_val,
							'post_id' => $post_id
						);

					}else{
						$status = array(
							'status' => 'err',
							'msg' => 'No post was selected'
						);

					}
				}else{
					$status = array(
							'status' => 'err',
							'msg' => 'Edit failed. Must be logged in to make edits.'
						);

				}					
				
				echo json_encode($status);
			    
			    wp_die(); 
			}



	

	/**
		 * Update a single term's noindex and nofollow status
		 * this function is used as an AJAX callback to modify the noindex/nofollow 
		 * status of a single post or page 
		 * @access public
		 * @return void
		 */
		public function update_cat_callback() {
				
				$new_val = 0;								
				
				//double check that the user has the neccessary permissions to edit posts	
				//and that the nonce is correct

				$term_id = (int)sanitize_text_field($_POST['post_id']);
				$nonce = sanitize_text_field($_POST['nonce']);
				
				
				if(current_user_can( 'manage_options') &&  wp_verify_nonce( $nonce, 'bulk-noindex' )){
					
					if(isset($_POST) && isset($_POST['result'])){
						
						$directive = sanitize_text_field(str_replace('[]','',$_POST['check_class']));
						
						$active_key = $this->get_meta_keys($directive);
						
						$term_id = (int)sanitize_text_field($_POST['post_id']);
						$key_val = (sanitize_text_field($_POST['result']) == 1) ? 1 : 0;
					
						
				
						//since AISEO uses it's own DB tables to store the post's robots settings
						//we need to access the post settings via AISEO
						/*
						if($this->active_seo_plugin == 'aioseo'){
							
							$postAISEO = aioseo()->core->db->start( 'aioseo_posts' )
							->where( 'post_id', $post_id )
							->run()
							->model( 'AIOSEO\\Plugin\\Common\\Models\\Post' ); 					
						} */

						/**
						* Always update the fallback term_meta key that's specific for this plugin
						* that way if any of the supported plugins are disabled, we don't lose
						* track of the pages which should be noindexed
						**/
												

						if($active_key){

							switch($active_key){
								case "rank_math_robots":
								
									$meta_key_noidx = $this->get_meta_keys('noindex');
   									$meta_key_nofllw = $this->get_meta_keys('nofollow');
									
									$foundKey = 0;
									$rm_robots_dat = get_term_meta($term_id,$meta_key_noidx,true);	

									/*
									print $term_id."\n";
									print $meta_key_noidx."\n";
									print_r($rm_robots_dat);
									print $directive;
									*/
									
									
									if(!$rm_robots_dat){
										$rm_robots_dat = array();
									}
									
									//if disabling nofollow or noindex
									if($key_val == 0){	
																		
										$keyFind = array_search($directive,$rm_robots_dat);

										if($directive){
											switch($directive){
												case "noindex":
													$rm_robots_dat[$keyFind] = 'index';
												break;
												case "nofollow":
													$rm_robots_dat[$keyFind] = 'follow';
													
													if(count($rm_robots_dat) == 1){
														$rm_robots_dat[] = 'index';
													}
													unset($rm_robots_dat[$keyFind]);
													array_values($rm_robots_dat);
												break;
											}

										}
										
										update_term_meta( $term_id, sanitize_text_field( $active_key ), $rm_robots_dat );

									}else{
										//if activating nofollow or noindex

										$keyFind = array_search(str_replace('no','',$directive),$rm_robots_dat);
										
										//update the noindex or nofollow array value accordingly
										if($directive == 'noindex' || $directive == 'nofollow'){

											if($keyFind != ''){
												$rm_robots_dat[$keyFind] = $directive;
												$foundKey = $k1;
											}else{
												if(!in_array($directive,$rm_robots_dat)){
													$rm_robots_dat[] = $directive;
												}
											}
											
										}
																			
										update_term_meta( $term_id, sanitize_text_field( $active_key ), $rm_robots_dat );

								}

								break;
								/*
								still need to add support for AIOSEO and Yoast here at a future date
								default:
										
								break;
								*/
							}			
						}
						//no matter what, updated the plugins default meta-robots term metaß
						if($key_val == 0){

							update_term_meta( $term_id, '_bnitk_mfd_meta-robots-'.$directive, $key_val );

							//check to see which plugin is being used
							
							//no matter what, we keep track of the current setting 
							//just in case the Yoast or AISEO plugin are disabled
							delete_term_meta($term_id, '_bnitk_mfd_meta-robots-'.$directive);
							
						}else{
							
							if($active_key){
							
								//no matter what, we keep track of the current setting 
								//just in case the Yoast or AISEO plugin are disabled
								update_term_meta( $term_id, '_bnitk_mfd_meta-robots-'.$directive, $key_val );
							} 
									
						}
						


				
						//let the user know that the update was successful
						if($key_val == 1){
							$message = 'The term\'s robots directive has been set to '.$directive;
						}else{
							$message = 'The '.$directive.' directive has been removed from the term';
						}
						$status = array(
							'status' => 'OK',
							'directive' => $directive,
							'msg' => $message,
							'val' => $key_val,
							'term_id' => $term_id
						);

					}else{
						$status = array(
							'status' => 'err',
							'msg' => 'No post was selected'
						);

					}
				}else{
					$status = array(
							'status' => 'err',
							'msg' => 'Edit failed. Must be logged in to make edits.'
						);

				}					
				
				echo json_encode($status);
			    
			    wp_die(); 
			}


		/**
		 * Update single author noindex and nofollow status
		 * this function is used as an AJAX callback to modify the noindex or nofollow
		 * status of a single author archive page
		 * @access public
		 * @return void
		 */
		public function update_author_callback() {

			$new_val = 0;

			$author_id = absint($_POST['post_id']);
			$nonce = sanitize_text_field($_POST['nonce']);

			if(current_user_can( 'manage_options') && wp_verify_nonce( $nonce, 'bulk-noindex' )){

				if(isset($_POST) && isset($_POST['result'])){

					$directive = sanitize_text_field(str_replace('[]','',$_POST['check_class']));
					$key_val = (sanitize_text_field($_POST['result']) == 1) ? 1 : 0;

					if($this->active_seo_plugin == 'rankmath'){

						$rm_robots_dat = get_user_meta($author_id, 'rank_math_robots', true);
						if(!is_array($rm_robots_dat)){
							$rm_robots_dat = array();
						}

						if($key_val == 0){
							// Remove the directive (noindex -> index, nofollow -> follow)
							$keyFind = array_search($directive, $rm_robots_dat);
							if($directive == 'noindex'){
								if($keyFind !== false){
									$rm_robots_dat[$keyFind] = 'index';
								}
							} elseif($directive == 'nofollow'){
								if($keyFind !== false){
									unset($rm_robots_dat[$keyFind]);
									$rm_robots_dat = array_values($rm_robots_dat);
								}
							}
						} else {
							// Add the directive
							$positive = str_replace('no', '', $directive);
							$keyFind = array_search($positive, $rm_robots_dat);
							if($keyFind !== false){
								$rm_robots_dat[$keyFind] = $directive;
							} elseif(!in_array($directive, $rm_robots_dat)){
								$rm_robots_dat[] = $directive;
							}
						}

						update_user_meta( $author_id, 'rank_math_robots', $rm_robots_dat );

					}

					// Always keep plugin's own meta in sync as a fallback
					if($key_val == 0){
						delete_user_meta($author_id, '_bnitk_mfd_meta-robots-'.$directive);
					} else {
						update_user_meta( $author_id, '_bnitk_mfd_meta-robots-'.$directive, $key_val );
					}

					if($key_val == 1){
						$message = 'The author\'s robots directive has been set to '.$directive;
					}else{
						$message = 'The '.$directive.' directive has been removed from the author';
					}
					$status = array(
						'status' => 'OK',
						'directive' => $directive,
						'msg' => $message,
						'val' => $key_val,
						'term_id' => $author_id
					);

				}else{
					$status = array(
						'status' => 'err',
						'msg' => 'No author was selected'
					);
				}
			}else{
				$status = array(
					'status' => 'err',
					'msg' => 'Edit failed. Must be logged in to make edits.'
				);
			}

			echo json_encode($status);

			wp_die();
		}



	}


} //end check for the existing class