<?php
/*
Plugin Name: JumpByteTest plugin GIt
Description: Starting with creating WordPress' Plugin.
Author: JumpByte
Version: 1.0
*/

add_action('admin_menu', 'jb_plugin_setup_menu');

if( !defined( 'ABSPATH' ) ){
	echo "<h1>No script kiddies please!!!</h1>";
	exit; // Exit if accessed directly
}

/* plugin install and uninstall hooks */
register_activation_hook(__FILE__, 'jb_activate_plugin' );

/*
 * Function to add option when Plugin installed.
 */
function jb_activate_plugin(){
	if( !get_option('jb_posttype_backup') ){
		add_option('jb_posttype_backup');
	}
	if( !get_option('jb_post_types') ){
		add_option('jb_post_types');
	}
	/* Example for Add Option with Values.
	if( !get_option('jb_post_types') ){
			// post options
			$options6 = array('sfsi_show_Onposts'=>'no',
			'sfsi_show_Onbottom'=>'no',
			'sfsi_icons_postPositon'=>'source',
			'sfsi_icons_alignment'=>'center-right',
			'sfsi_rss_countsDisplay'=>'no',
			'sfsi_textBefor_icons'=>'Please follow and like us:',
			'sfsi_icons_DisplayCounts'=>'no',
			'sfsi_rectsub'=>'yes',
			'sfsi_rectfb'=>'yes',
			'sfsi_rectgp'=>'yes',
			'sfsi_rectshr'=>'no',
			'sfsi_recttwtr'=>'yes',
			'sfsi_rectpinit'=>'yes',
			'sfsi_rectfbshare'=>'yes'
		);
		add_option('jb_post_types', serialize($options6));
	} */
}
/* END - function */



/* Filter the single_template with our custom function*/
if( 'Twenty Sixteen' == wp_get_theme() || 'Twenty Fifteen' == wp_get_theme() ){
	function my_custom_template($single) {
		global $wp_query, $post;

		/* Checks for single template by post type */
		if ( $post->post_type == 'team' ) {
			if ( file_exists( dirname( __FILE__ ) . '/single-team.php' ) ) {
				return dirname( __FILE__ ) . '/single-team.php';
				exit;
			}
		}
		if ( $post->post_type == 'client' ) {
			if ( file_exists( dirname( __FILE__ ) . '/single-client.php' ) ) {
				return dirname( __FILE__ ) . '/single-client.php';
				exit;
			}
		}
		if ( $post->post_type == 'project' ) {
			if ( file_exists( dirname( __FILE__ ) . '/single-project.php' ) ) {
				return dirname( __FILE__ ) . '/single-project.php';
				exit;
			}
		}
		return $single;

		}
		add_filter('single_template', 'my_custom_template', 99);
		//remove_filter('single_template','my_custom_template');
}	






function jb_plugin_setup_menu(){
	/* add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position); */
	/* add_menu_page( 'Page Title', 'Menu Title / Name', 'manage_options', 'page name in URL', 'Function to call','https://lh3.googleusercontent.com/FBP3gbsGv0LrE2tpZ0KMvp6N2vJwT-c9QIzh5cLkB4l_-QV-jQgXWd9c8rnMMZDA9hpakmBlDgYVp8TxxO8'); */
	add_menu_page( 'JumpByte Overview', 'JumpByte', 'manage_options', 'JumpByte', 'jb_init' );

	/* add_submenu_page( 'Parent Slug', 'Page Title', 'Menu Title / Name', 'manage_options', 'page name in URL', 'jb_Setting' ); */
	add_submenu_page( 'JumpByte', 'JumpByte Setting', 'Setting', 'manage_options', 'JB_Setting', 'jb_setting' );
	add_submenu_page( 'JumpByte', 'JumpByte - About', 'About', 'manage_options', 'jb_about', 'jb_about' );
}

/*
 * Add Plugin CSS and JS.
 */
function jb_enqueue_scripts_styles() {
	// Register.
	wp_register_style( 'jb_style', plugins_url( 'JumpByteTest/style.css' ) );
	//wp_register_style( 'cupp_admin_css', plugins_url( 'custom-user-profile-photo/css/styles.css' ), false, '1.0.0', 'all' );
	//wp_register_script( 'cupp_admin_js', plugins_url( 'custom-user-profile-photo/js/scripts.js' ), array( 'jquery' ), '1.0.0', true );

	// Enqueue.
	wp_enqueue_style( 'jb_style' );
	//wp_enqueue_script( 'cupp_admin_js' );
}

add_action( 'admin_enqueue_scripts', 'jb_enqueue_scripts_styles' );


add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
	wp_register_style( 'jb_style', plugins_url( 'JumpByteTest/style.css' ) );
	wp_enqueue_style( 'jb_style' );
}

/*
 * JumpByte Project Shortcode.
 */
function jb_project_shortcode_function($atts) {
	if( isset( $atts['view'] ) && !empty( $atts['view'] ) && '' != $atts['view'] ){
		if( 'list' == $atts['view'] ){
			$view = 'list-item';
		}else{
			$view = 'grid';
		}
	}else{
		$view = 'grid';
	}

	$project_args = array(
		'post_status' => 'publish', 
		'post_type' => 'project' ,
	);
	$project_query = new WP_Query($project_args);
	if ( $project_query->have_posts() ) :
		echo "<div class='jb_wrapper' style='display: $view;'>";
			while ( $project_query->have_posts() ) : $project_query->the_post();
			$current_id = get_the_ID();
				?>
				<div>
					<?php the_title( '<h3 class=""><b><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</b></a></h3>' ); ?>
					<a href="<?php the_permalink(); ?>">
						<?php the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
					</a>
					<p>
					<?php
						the_content( sprintf(
							__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'jumpbytetest' ),
							get_the_title()
						) );
					?>
					</p>
					<p><b>Client</b></p>
					<?php
						$client_args = array( 'post_type' => 'client');
						$loop = new WP_Query( $client_args );
						while ( $loop->have_posts() ) : $loop->the_post();
							global $post;
							$client_id .= $post->ID.',';
						endwhile;
						$client_array = explode(',',$client_id);
						$client_array_count = count($client_array);
						for ($c=0; $c < $client_array_count ; $c++) {
							$client_id = $client_array[$c];
							$clients_project = get_post_meta($client_id,'project_list_meta',TRUE);
							if( '' != $clients_project){
								for ($d=0; $d < count($clients_project); $d++) { 
									if( $current_id == $clients_project[$d] ){
										$team_link = get_permalink( $client_id );
										$team_title = get_the_title( $client_id );
										echo "<a href='$team_link'>$team_title</a></br></br>";
									}					
								}
							//echo "<pre>";print_r($clients_project);echo "</pre>";
							}			
						}
						wp_reset_postdata();
						$project_team = get_post_meta($current_id,'project_team_list_meta',TRUE);
						if( '' != $project_team ){
							?>
							<p><b>
								<?php _e( 'Teams assign to this Project', 'jumpbytetest' ); ?>
							</b></p>
							<?php
							$project_count = count($project_team);
							for ($i=0; $i < $project_count; $i++) {			 
								$val = $project_team[$i];
								$team_post = get_post( $val );
								$team_title = $team_post->post_title;
								$team_link = get_permalink($val);
								?>
								<p><a href="<?php echo $team_link; ?>">
									<?php _e( $team_title, 'jumpbytetest' ); ?>
								</a></p>
								<?php
							}
							?>
							<p><b>
								<?php _e( 'Teams Members', 'jumpbytetest' ); ?>
							</b></p>
							<?php
							for ($i=0; $i < $project_count; $i++) {			 
								$val = $project_team[$i];
								$selected = get_post_meta($val,'team_list_meta',TRUE);
								if( '' !=$selected ){
									foreach($selected as $team){
										$user = get_userdatabylogin($team);
										$user_id = $user->data->ID;
										$user_link = get_author_posts_url( $user_id );
										if($user){
											echo "<a href='$user_link'>$user->display_name</a></br>";
										}
									}
								}else{
									?>
									<p><b><?php _e( 'No Team Found !!!', 'jumpbytetest' ); ?></b></p>
									<?php
								}
							}
						}else{
							?>
							<p><b><?php _e( 'Nothing Found !!!', 'jumpbytetest' ); ?></b></p>
							<?php
						}
					?>
				</div>
				<?php
			endwhile;
		echo "</div>";
	endif;
	wp_reset_postdata();
} 
add_shortcode('jb-project-shortcode', 'jb_project_shortcode_function');

/*
 * JumpByte Team Shortcode.
 */
function jb_team_shortcode_function($atts) {
	if( isset( $atts['view'] ) && !empty( $atts['view'] ) && '' != $atts['view'] ){
		if( 'list' == $atts['view'] ){
			$view = '';
		}else{
			$view = 'float: left; width: 33.3%;';
		}
	}else{
		$view = 'float: left; width: 33.3%;';
	}
	$team_args = array(
		'post_status' => 'publish', 
		'post_type' => 'team' ,
	);
	$team_query = new WP_Query($team_args);
	if ( $team_query->have_posts() ) :
		echo "<div class='row'>";
		while ( $team_query->have_posts() ) : $team_query->the_post();
			?>
			<div class="column" style="<?php echo $view; ?>">
				<div class="card">
					<?php	the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
					<!-- <img src="/w3images/team1.jpg" alt="Jane" style="width:100%"> -->
					<div class="container">
						<?php the_title( '<h3 class=""><b><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</b></a></h3>' ); ?>
						<p>Some text that describes me lorem ipsum ipsum lorem.</p>
						<?php
							$selected = get_post_meta(get_the_ID(),'team_list_meta',TRUE);
							if( '' !=$selected ){
								?>
								<p class="test">
									<b><?php _e( 'Team Members', 'jumpbytetest' ); ?></b>
								</p>
								<?php
								foreach( $selected as $team ){
									$user = get_userdatabylogin($team);
									$user_id = $user->data->ID;
									$user_link = get_author_posts_url( $user_id );
									if($user){
										echo "<a href='$user_link'>$user->display_name</a></br>";
									}
								}
							}else{
								?>
								<p><b><?php _e( 'No Team Found !!!', 'jumpbytetest' ); ?></b></p>
								<?php
							}
							?>
					</div>
				</div>
			</div>			
			<?php
		endwhile;
		echo "</div>";
	endif;
	wp_reset_postdata();
} 
add_shortcode('jb-team-shortcode', 'jb_team_shortcode_function');

/*
 * JumpByte Team Shortcode.
 */
function jb_client_shortcode_function() {
	$client_args = array(
		'post_status' => 'publish', 
		'post_type' => 'client' ,
	);
	$client_query = new WP_Query($client_args);
	if ( $client_query->have_posts() ) :
		while ( $client_query->have_posts() ) : $client_query->the_post();
			?>
			<?php
		endwhile;
	endif;
	wp_reset_postdata();
} 
add_shortcode('jb-client-shortcode', 'jb_client_shortcode_function');

if( !function_exists('jb_init') ){
	function jb_init(){

		//update_option( 'jb_post_types', '', $false );
		//$social_option = get_option('sfsi_section6_options',TRUE);
		//$social_option = unserialize(get_option('jb_post_types',false));

		$social_option = unserialize(get_option('jb_posttype_backup',false));
		
		if( '' != $social_option ){
			echo "<pre>";print_r($social_option);echo "</pre>";
		}
		?>
		<div class="wrapper">
			<div><h1>JumpByte Overview</h1></div>
		</div>

		<?php
		$jb_plugin_dir = dirname(__FILE__);

		$post_types = get_post_types();
		echo "<pre>";print_r($post_types);echo "</pre>";
		foreach ($post_types as $post_type => $value) {
			if( $value != 'post' && $value != 'page' && $value != 'attachment' && $value != 'revision' && $value != 'custom_css' && $value != 'customize_changeset' && $value != 'oembed_cache' && $value != 'nav_menu_item' ){
				
			}
		}
	}
}

/*
 * Create Custom Post type Multiple
 */

function create_post_type_multiple() {

	//$post_option_array = ['one','two','three'];
	$post_option_array = array();
	$post_option_array_lower = array();
	$all_post_types = get_post_types();
	$all_post_types_lower = array_map('strtolower', $all_post_types);

	/* Store value of "jb_post_types" WP_OPTION if not empty */
	if( !empty( get_option('jb_post_types',false) ) && '' != get_option('jb_post_types',false) ){
		$post_option_array = get_option('jb_post_types',false);
		$post_option_array_lower = array_map('strtolower', $post_option_array);
	}
	/* Check if "jb_post_types" WP_OPTION is not empty - If is empty nothing happen*/
	if( !empty( $post_option_array ) && '' != $post_option_array ){
		foreach( $post_option_array as $post_array => $value ){
			$value_lower = strtolower($value);

			/* Check if current value not exist in all post types */
			if( !in_array( $posttype_tvalue_lowerext,$all_post_types_lower ) ){
					$value = ucfirst( $value );
					$labels = array(
						'name'               => _x( $value.'s', 'post type general name', 'jumpbytetest' ),
						'singular_name'      => _x( $value, 'post type singular name', 'jumpbytetest' ),
						'menu_name'          => _x( $value, 'admin menu', 'jumpbytetest' ),
						'name_admin_bar'     => _x( $value.'s', 'add new on admin bar', 'jumpbytetest' ),
						'add_new'            => _x( 'Add '.$value, $value, 'jumpbytetest' ),
						'add_new_item'       => __( 'Add New '.$value, 'jumpbytetest' ),
						'new_item'           => __( 'New '.$value, 'jumpbytetest' ),
						'edit_item'          => __( 'Edit '.$value, 'jumpbytetest' ),
						'view_item'          => __( 'View '.$value, 'jumpbytetest' ),
						'all_items'          => __( 'All '.$value.'s', 'jumpbytetest' ),
						'search_items'       => __( 'Search ', 'jumpbytetest' ),
						'parent_item_colon'  => __( 'Parent :', 'jumpbytetest' ),
						'not_found'          => __( 'No '.$value.' found.', 'jumpbytetest' ),
						'not_found_in_trash' => __( 'No '.$value.' found in Trash.', 'jumpbytetest' )
					);
				
					$args = array(
						'labels'             => $labels,
						'description'        => __( 'Description.', 'jumpbytetest' ),
						'public'             => true,
						'publicly_queryable' => true,
						'show_ui'            => true,
						'show_in_menu'       => true,
						'query_var'          => true,
						'rewrite'            => array( 'slug' => $value ),
						'taxonomies' => array('post_tag','category'),
						'capability_type'    => 'post',
						'has_archive'        => true,
						'hierarchical'       => false,
						'menu_position'      => null,
						'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
					);
				
					register_post_type( $value, $args ); // Register current value post type
			}
		}
	}
}

add_action( 'init', 'create_post_type_multiple' );
/* END - Create Custom Post type Multiple*/

/* Check if function already Exist */
if( !function_exists('jb_setting') ){
	/*
	 * Function for Jb_Setting pluging page.
	 */
	function jb_setting(){

		echo "<h1>JumpByte Setting</h1>";
		/* Add custom post type from backend JumpByte setting */
		echo "<h2>Add your Custom post types here:</h2>";

		/* Initilaize blank Array and Variables*/
		$post_type_values = array();
		$post_type_values_lower = array();
		$posttype_text = '';
		$posttype_text_orignal = '';

		/* Store all post types value */
		$all_post_types = get_post_types();
		$all_post_types_lower = array_map('strtolower', $all_post_types); // Store all post types value in lower case

		/* Store value of "jb_post_types" WP_OPTION if not empty */
		if( !empty( get_option('jb_post_types',false) ) && '' != get_option('jb_post_types',false) ){
			$post_type_values = get_option('jb_post_types',false);
			$post_type_values_lower = array_map('strtolower', $post_type_values); // Store all option post types value in lower case
		}

		/* Check if $_POST['post_type_name'] value is set */
		if( isset( $_POST['post_type_name'] ) && !empty( $_POST['post_type_name'] ) && '' != $_POST['post_type_name'] && strlen(trim( $_POST['post_type_name'] )) != 0 ){
			$posttype_text = strtolower($_POST['post_type_name']);
			$posttype_text_orignal = $_POST['post_type_name'];
		}

		/* Check if $_POST['post_type_name'] value is set */
		if( !empty( $posttype_text_orignal ) && '' != $posttype_text_orignal && strlen(trim($posttype_text_orignal)) != 0 ){

			/* Verify if Post Type Name Already Exists in All post types*/
			if( !in_array( $posttype_text,$all_post_types_lower ) ){

				/* Verify if Post Type Name Already Exists in All option post types*/
				if( in_array( $posttype_text,$post_type_values_lower ) ){
					echo "Post Type Already Exists";
				}else{
					$post_type_values[] = $posttype_text_orignal; // Add new value to Option Array.
					update_option('jb_post_types',$post_type_values);  // Update new array to "jb_post_types" WP_OPTION.
					echo "Post Type Added Succesfully."; // Success Message.
					echo "<p><b>Note: </b>Refresh if Post Type not in Dashboard.</p>";
				}
			}else{
				echo "Post Type Already Exists"; // Post Type Name Already Exists in All post types
			}

		}
		?>
		<!-- Form to add new value to "jb_post_types" WP_OPTION -->
		<form name="post_type_form" method="post">
			<input type="text" name="post_type_name" placeholder="Enter Your Post Type Name">
			<input type="submit" name="post_type_submit" value="ADD">
		</form>
		<!-- END - Form to add new value to "jb_post_types" WP_OPTION -->

		<?php
		
		/* Check If request recived to remove Post Type from Option*/
		if( isset( $_REQUEST['remove_post_type'] ) && '' != $_REQUEST['remove_post_type'] && !empty( $_REQUEST['remove_post_type']  && strlen(trim( $_REQUEST['remove_post_type'] )) != 0) ){

			$remove_post_type = $_REQUEST['remove_post_type']; // Store request value to variable
			$remove_post_type_lower = strtolower($_REQUEST['remove_post_type']); // Covert variable to lower string
			
			/* Check if "jb_post_types" WP_OPTION is not empty -If is empty nothing happen*/
			if( !empty( $post_type_values ) && '' != $post_type_values ){
				
				/* Check if Lower Request string is in "jb_post_types" WP_OPTION lower*/
				if( in_array($remove_post_type_lower, $post_type_values_lower) ){
					foreach( $post_type_values as $values => $value ){
						$value_lower = strtolower($value); // Strore current value to lowercase.

						/* Chek if lower values for Remove request and Current array value are equal */
						if( $remove_post_type_lower == $value_lower ){
							unset($post_type_values[$values]);		
						}
					}
					update_option('jb_post_types',$post_type_values); // Update new array to "jb_post_types" WP_OPTION.
					echo "Post Type Removed Succesfully."; // Success Message.
					echo "<p><b>Note: </b>Refresh if Post Type still in Dashboard.</p>";
					wp_redirect( '?page=JB_Setting' );
					exit;
				}
			}
		}

		/* Check if "jb_post_types" WP_OPTION is not empty */
		if( !empty( $post_type_values ) && '' != $post_type_values ){
			echo "<table><th>Post Types</th><th>Delete</th>";
				foreach( $post_type_values as $values => $value ){
					$value = ucfirst( $value );
					echo "<tr><td>".$value."</td><td>";
					echo '<a href="?page=JB_Setting&remove_post_type='.$value.'">Remove</a></br>'; // Remove link for specific Post Type.
					echo "</td></tr>";
				}
			echo "</table>";
		}else{
			echo "<h3>No Post Type Created</h3>";
			echo "<p><b>Note: </b>Enter your post type name above and Click on ADD Button.</p>";
		}
	}
	/* END - Add custom post type from backend JumpByte setting */
}

/* Check if function already Exist */
if( !function_exists('jb_about') ){
	/*
	 * Functio for Jb_Aboutr page 
	 */
	function jb_about(){
		echo "<h1>About JumpByte	</h1>";
		$post_types = get_post_types();
		echo "<pre>";print_r($post_types);echo "</pre>";
		foreach ($post_types as $post_type => $value) {
			if( $value != 'post' && $value != 'page' && $value != 'attachment' && $value != 'revision' && $value != 'custom_css' && $value != 'customize_changeset' && $value != 'oembed_cache' && $value != 'nav_menu_item' ){
				
			}
		}
	}
}

/**
 * Add Team User Role.
 *
 */
$team_role_add = add_role(
	'team_contributor',
	__( 'Team' ),
		array(
			'read'			=> true, // true allows this capability
			'edit_posts'	=> true,
			'delete_posts'	=> false, // Use false to explicitly deny
		)
	);

add_action( 'init', 'create_post_type_team' );
/**
 * Register a Team post type.
 *
 */
function create_post_type_team() {
	$labels = array(
		'name'				=> _x( 'Teams', 'post type general name', 'jumpbytetest' ),
		'singular_name'		=> _x( 'Team', 'post type singular name', 'jumpbytetest' ),
		'menu_name'			=> _x( 'Teams', 'admin menu', 'jumpbytetest' ),
		'name_admin_bar'	=> _x( 'Teams', 'add new on admin bar', 'jumpbytetest' ),
		'add_new'			=> _x( 'Add New', 'team', 'jumpbytetest' ),
		'add_new_item'		=> __( 'Add New Team', 'jumpbytetest' ),
		'new_item'			=> __( 'New Team', 'jumpbytetest' ),
		'edit_item'			=> __( 'Edit Team', 'jumpbytetest' ),
		'view_item'			=> __( 'View Team', 'jumpbytetest' ),
		'all_items'			=> __( 'All Teams Members', 'jumpbytetest' ),
		'search_items'		=> __( 'Search ', 'jumpbytetest' ),
		'parent_item_colon'	=> __( 'Parent :', 'jumpbytetest' ),
		'not_found'			=> __( 'No Team Member found.', 'jumpbytetest' ),
		'not_found_in_trash'=> __( 'No Team Member found in Trash.', 'jumpbytetest' )
	);

	$args = array(
		'labels'				=> $labels,
		'description'			=> __( 'Description.', 'jumpbytetest' ),
		'public'				=> true,
		'publicly_queryable'	=> true,
		'show_ui'				=> true,
		'show_in_menu'			=> true,
		'query_var'				=> true,
		'rewrite'				=> array( 'slug' => 'Team' ),
		'taxonomies'			=> array('post_tag','category'),
		'capability_type'		=> 'post',
		'has_archive'			=> true,
		'hierarchical'			=> false,
		'menu_position'			=> null,
		'supports'				=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'team', $args );
}

add_action( 'init', 'create_post_type_project' );
/**
 * Register a Project post type.
 *
 */
function create_post_type_project() {
	$labels = array(
		'name'				=> _x( 'Projects', 'post type general name', 'jumpbytetest' ),
		'singular_name'		=> _x( 'Project', 'post type singular name', 'jumpbytetest' ),
		'menu_name'			=> _x( 'Projects', 'admin menu', 'jumpbytetest' ),
		'name_admin_bar'	=> _x( 'Project', 'add new on admin bar', 'jumpbytetest' ),
		'add_new'			=> _x( 'Add New Project', 'project', 'jumpbytetest' ),
		'add_new_item'		=> __( 'Add New Project', 'jumpbytetest' ),
		'new_item'			=> __( 'New Project', 'jumpbytetest' ),
		'edit_item'			=> __( 'Edit Project', 'jumpbytetest' ),
		'view_item'			=> __( 'View Project', 'jumpbytetest' ),
		'all_items'			=> __( 'All Projects', 'jumpbytetest' ),
		'search_items'		=> __( 'Search Projects', 'jumpbytetest' ),
		'parent_item_colon'	=> __( 'Parent Projects:', 'jumpbytetest' ),
		'not_found'			=> __( 'No Projects found.', 'jumpbytetest' ),
		'not_found_in_trash'=> __( 'No Projects found in Trash.', 'jumpbytetest' )
	);

	$args = array(
		'labels'			=> $labels,
		'description'		=> __( 'Description.', 'jumpbytetest' ),
		'public'			=> true,
		'publicly_queryable'=> true,
		'show_ui'			=> true,
		'show_in_menu'		=> true,
		'query_var'			=> true,
		'rewrite'			=> array( 'slug' => 'project' ),
		'taxonomies'		=> array('post_tag','category'),
		'capability_type'	=> 'post',
		'has_archive'		=> true,
		'hierarchical'		=> false,
		'menu_position'		=> null,
		'supports'			=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'project', $args );
}

add_action( 'init', 'create_post_type_client' );
/**
 * Register a Client post type.
 *
 */
function create_post_type_client() {
	$labels = array(
		'name'               => _x( 'Clients', 'post type general name', 'jumpbytetest' ),
		'singular_name'      => _x( 'Client', 'post type singular name', 'jumpbytetest' ),
		'menu_name'          => _x( 'Clients', 'admin menu', 'jumpbytetest' ),
		'name_admin_bar'     => _x( 'Client', 'add new on admin bar', 'jumpbytetest' ),
		'add_new'            => _x( 'Add New', 'client', 'jumpbytetest' ),
		'add_new_item'       => __( 'Add New Client', 'jumpbytetest' ),
		'new_item'           => __( 'New Client', 'jumpbytetest' ),
		'edit_item'          => __( 'Edit Client', 'jumpbytetest' ),
		'view_item'          => __( 'View Client', 'jumpbytetest' ),
		'all_items'          => __( 'All Clients', 'jumpbytetest' ),
		'search_items'       => __( 'Search Clients', 'jumpbytetest' ),
		'parent_item_colon'  => __( 'Parent Clients:', 'jumpbytetest' ),
		'not_found'          => __( 'No Clients found.', 'jumpbytetest' ),
		'not_found_in_trash' => __( 'No Clients found in Trash.', 'jumpbytetest' )
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Description.', 'jumpbytetest' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'client' ),
		'taxonomies' => array('post_tag','category'),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'gallery', 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'client', $args );
}

/* Meta box added for Team Members in Team Post Type*/
add_action( 'add_meta_boxes', 'cd_meta_box_add' );

function cd_meta_box_add()
{
	add_meta_box( 'my-project-meta-box-id', 'Team Members', 'cd_meta_box_cb', 'team', 'normal', 'high' );
}

function cd_meta_box_cb(){
	$post_id = get_the_ID();
	$selected = get_post_meta($post_id,'team_list_meta',TRUE);
	//print_r($selected);
	$args = array(
		'blog_id'	=> $GLOBALS['blog_id'],
		'role'		=> 'team_contributor',
		);
	$team_users = get_users( $args );
	$total = count($team_users);
	?>
	<select name="team_list[]" multiple="multiple">
		<?php
		for($i=0; $i<=$total - 1; $i++){
			$team_name = $team_users[$i]->data->user_nicename;
			?>
			<option value="<?php echo $team_name; ?>" <?php if( $selected != '' ){ if( in_array($team_name, $selected)){ echo "selected"; } } ?>><?php echo $team_name; ?></option>;
			<?php
		}
		?>
	</select>
	<?php
}

add_action( 'save_post', 'cd_meta_box_save' );
function cd_meta_box_save( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	if( !current_user_can( 'edit_post' ) ) return;

	if( !empty($_POST['team_list']) && '' != $_POST['team_list'] ){
		update_post_meta( $post_id, 'team_list_meta', $_POST['team_list'] );
	}
}
/* END - Meta box for Team Members in Team Post Type*/

/* Meta box added for Teams in Project Post Type*/
add_action( 'add_meta_boxes', 'teams_meta_box_add' );

function teams_meta_box_add()
{
	add_meta_box( 'team-list-meta-box-id', 'Team List', 'teams_meta_box_cb', 'project', 'normal', 'high' );
}

function teams_meta_box_cb(){
	$post_id = get_the_ID();
	$selected = get_post_meta($post_id,'project_team_list_meta',TRUE);

	$args = array(
		'post_status' => 'publish', 
		'post_type' => 'team' ,
	);
	$query = new WP_Query($args);
	$teams = $query->posts;
	$teams_count = $query->post_count;
	?>
	<select name="project_team_list[]" multiple="multiple">
	<?php
		for ($i=0; $i < $teams_count; $i++) {
			$project_title = $teams[$i]->post_title;
			$project_id = $teams[$i]->ID;
			?>
			<option value="<?php echo $project_id; ?>" <?php if( $selected != '' ){ if( in_array($project_id, $selected)){ echo "selected"; } } ?>><?php echo $project_title; ?></option>;
			<?php
		}
		?>
	</select>
	<?php
}

add_action( 'save_post', 'teams_meta_box_save' );
function teams_meta_box_save( $post_id ){
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	if( !current_user_can( 'edit_post' ) ) return;

	if( !empty($_POST['project_team_list']) && '' != $_POST['project_team_list'] ){
		update_post_meta( $post_id, 'project_team_list_meta', $_POST['project_team_list'] );
	}
}
/* END - Meta box for Team List(varname) in Project Post Type*/

/* Meta box added for Project in clients Post Type*/
add_action( 'add_meta_boxes', 'projects_meta_box_add' );

function projects_meta_box_add()
{
	add_meta_box( 'project-list-meta-box-id', 'Project List', 'project_meta_box_cb', 'client', 'normal', 'high' );
}

function project_meta_box_cb(){
	$post_id = get_the_ID();
	$client_url = get_post_meta($post_id,'client_url_meta',TRUE);
	echo "<label>Enter your URL:</label></br>";
	echo "<input type='url' name='client_url' value='$client_url'></br>";
	echo "<label>Projects:</label></br>";
	
	$selected = get_post_meta($post_id,'project_list_meta',TRUE);

	$args = array(
		'post_status' => 'publish', 
		'post_type' => 'project' ,
	);
	$query = new WP_Query($args);
	$teams = $query->posts;
	$teams_count = $query->post_count;
	?>
	<select name="project_list[]" multiple="multiple">
	<?php
		for ($i=0; $i < $teams_count; $i++) {
			$project_title = $teams[$i]->post_title;
			$project_id = $teams[$i]->ID;
			?>
			<option value="<?php echo $project_id; ?>" <?php if( $selected != '' ){ if( in_array($project_id, $selected)){ echo "selected"; } } ?>><?php echo $project_title; ?></option>;
			<?php
		}
		?>
	</select>
	<?php
}

add_action( 'save_post', 'project_meta_box_save' );
function project_meta_box_save( $post_id ){
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	if( !current_user_can( 'edit_post' ) ) return;

	if( !empty($_POST['project_list']) && '' != $_POST['project_list'] ){
		update_post_meta( $post_id, 'project_list_meta', $_POST['project_list'] );
	}

	if( !empty($_POST['client_url']) && '' != $_POST['client_url'] ){
		update_post_meta( $post_id, 'client_url_meta', $_POST['client_url'] );
	} 
}
/* END - Meta box for Project List in clients Post Type*/



/* Ajax Project Shortcode actions and function */
add_action( 'wp_ajax_demo-pagination-load-posts', 'cvf_demo_pagination_load_posts' );

add_action( 'wp_ajax_nopriv_demo-pagination-load-posts', 'cvf_demo_pagination_load_posts' ); 

function cvf_demo_pagination_load_posts() {
	global $wpdb;
	// Set default variables
	$msg = '';
	if( isset( $_POST['ptype'] ) && !empty( $_POST['ptype'] ) && '' != $_POST['ptype'] ){
		if( 'client' == $_POST['ptype'] ){
			$ptype = 'client';
		}elseif( 'project' == $_POST['ptype'] ){
			$ptype = 'project';
		}
		elseif( 'team' == $_POST['ptype'] ){
			$ptype = 'team';
		}
		else{
			$ptype = 'project';
		}
	}else{
		$ptype = 'project';
	}

	if( isset( $_POST['page'] ) ){
		// Sanitize the received page.
		$page = sanitize_text_field($_POST['page']);
		$cur_page = $page;
		$page -= 1;
		// Set the number of results to display
		$per_page = 1;
		$previous_btn = true;
		$next_btn = true;
		$first_btn = true;
		$last_btn = true;
		$start = $page * $per_page;

		// Set the table where we will be querying data
		$table_name = $wpdb->prefix . "posts";

		// Query the necessary posts
		//$all_blog_posts = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $table_name . " WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT %d, %d", $start, $per_page ) );

		// At the same time, count the number of queried posts
		//$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM " . $table_name . " WHERE post_type = 'post' AND post_status = 'publish'", array() ) );

		/**
		 * Use WP_Query:
		 */
		$all_blog_posts = new WP_Query(
			array(
				'post_type'			=> $ptype,
				'post_status '		=> 'publish',
				'posts_per_page'	=> $per_page,
				'offset'			=> $start
			)
		);

		$count = new WP_Query(
			array(
				'post_type'			=> $ptype,
				'post_status '		=> 'publish',
				'posts_per_page'	=> -1
			)
		);
		//echo "<pre>";print_r($count);echo "</pre>";
		//echo "<pre>";print_r($all_blog_posts->posts);echo "</pre>";

		// Loop into all the posts	
		foreach($all_blog_posts->posts as $post): 
			// Set the desired output into a variable
			$msg .= '
			<div class = "col-md-12">
				<h2><b><a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a></b></h2>
				<p>' . $post->post_excerpt . '</p>
				<p>' . $post->post_content . '</p>
			</div>';
		endforeach;

		// Optional, wrap the output into a container
		$msg = "<div class='cvf-universal-content'>" . $msg . "</div><br class = 'clear' />";

		// This is where the magic happens
		$no_of_paginations = ceil($count->post_count / $per_page);

		if ($cur_page >= 7) {
			$start_loop = $cur_page - 3;
			if ($no_of_paginations > $cur_page + 3)
				$end_loop = $cur_page + 3;
			else if ($cur_page <= $no_of_paginations && $cur_page > $no_of_paginations - 6) {
				$start_loop = $no_of_paginations - 6;
				$end_loop = $no_of_paginations;
			} else {
				$end_loop = $no_of_paginations;
			}
		} else {
			$start_loop = 1;
			if ($no_of_paginations > 7)
				$end_loop = 7;
			else
				$end_loop = $no_of_paginations;
		}

		// Pagination Buttons logic.
		$pag_container .= "<div class='cvf-universal-pagination'><ul>";

		if ($first_btn && $cur_page > 1) {
			$pag_container .= "<li p='1' class='active'>First</li>";
		} else if ($first_btn) {
			$pag_container .= "<li p='1' class='inactive'>First</li>";
		}

		if ($previous_btn && $cur_page > 1) {
			$pre = $cur_page - 1;
			$pag_container .= "<li p='$pre' class='active'>Previous</li>";
		} else if ($previous_btn) {
			$pag_container .= "<li class='inactive'>Previous</li>";
		}
		for ($i = $start_loop; $i <= $end_loop; $i++) {
			if ($cur_page == $i)
				$pag_container .= "<li p='$i' class = 'selected' >{$i}</li>";
			else
				$pag_container .= "<li p='$i' class='active'>{$i}</li>";
		}

		if ($next_btn && $cur_page < $no_of_paginations) {
			$nex = $cur_page + 1;
			$pag_container .= "<li p='$nex' class='active'>Next</li>";
		} else if ($next_btn) {
			$pag_container .= "<li class='inactive'>Next</li>";
		}

		if ($last_btn && $cur_page < $no_of_paginations) {
			$pag_container .= "<li p='$no_of_paginations' class='active'>Last</li>";
		} else if ($last_btn) {
			$pag_container .= "<li p='$no_of_paginations' class='inactive'>Last</li>";
		}

		$pag_container = $pag_container . "</ul></div>";

		// We echo the final output
		echo 
		'<div class = "cvf-pagination-content">' . $msg . '</div>' . 
		'<div class = "cvf-pagination-nav">' . $pag_container . '</div>';

	}
	// Always exit to avoid further execution
	exit();
}

/*
 * JumpByte AJAX Shortcode.
 */
function jb_ajax_shortcode_function($atts) {
	if( isset( $atts['ptype'] ) && !empty( $atts['ptype'] ) && '' != $atts['ptype'] ){
		if( 'client' == $atts['ptype'] ){
			$ptype = 'client';
		}elseif( 'project' == $atts['ptype'] ){
			$ptype = 'project';
		}
		elseif( 'team' == $atts['ptype'] ){
			$ptype = 'team';
		}
		else{
			$ptype = 'project';
		}
	}else{
		$ptype = 'project';
	}
	?>
	<section id="primary">
		<div id="content" role="main">
			<div class="col-md-12 content">
				<div class = "inner-box content no-right-margin darkviolet">
					<script type="text/javascript">
						jQuery(document).ready(function($) {
							// This is required for AJAX to work on our page
							var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

							function cvf_load_all_posts(page){
								// Start the transition
								$(".cvf_pag_loading").fadeIn().css('background','#ccc');

								// Data to receive from our server
								// the value in 'action' is the key that will be identified by the 'wp_ajax_' hook 
								var data = {
									page: page,
									ptype: '<?php echo $ptype; ?>',
									action: "demo-pagination-load-posts"
								};

								// Send the data
								$.post(ajaxurl, data, function(response) {
									// If successful Append the data into our html container
									$(".cvf_universal_container").html(response);
									// End the transition
									$(".cvf_pag_loading").css({'background':'none', 'transition':'all 1s ease-out'});
								});
							}

							// Load page 1 as the default
							cvf_load_all_posts(1);

							// Handle the clicks
							$('.cvf_universal_container .cvf-universal-pagination li.active').live('click',function(){
								var page = $(this).attr('p');
								cvf_load_all_posts(page);

							});

						});
					</script>
					<div class = "cvf_pag_loading">
						<div class = "cvf_universal_container">
							<div class="cvf-universal-content"></div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- #content -->
	</section><!-- #primary -->
	<?php
} 
add_shortcode('jb-ajax-shortcode', 'jb_ajax_shortcode_function');
/* JumpByte AJAX Shortcode. */

?>