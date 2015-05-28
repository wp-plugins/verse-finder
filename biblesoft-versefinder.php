<?php
/*
Plugin Name: Verse Finder
Plugin URI:  http://www.biblesoftonline.com/
Description: Automatically provides a tooltip on hover with full Bible passages and commentary references for pages and posts.
Version:     1
Author:      Biblesoft.com
Author URI:  http://terryfritsch.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

define("vf_applyTo", "body");
define("vf_bible", "KJV");
define("vf_commentary", "MHC-abridged");
define("vf_hoverIn", "1000");
define("vf_hoverOut", "2500");
define("vf_redLetters", "0");
define("vf_verseNumbers", "1");
define("vf_ignoreQuery", "code,pre,h1,h2,h3,script");
define("vf_minimumSearchError", "0.4");
define("vf_width", "400");
define("vf_height", "175");
define("vf_lazyLoad", "1");


Class verseFinder {
	
	public function __construct() {

         if ( is_admin() ){ // admin actions
		  add_action('admin_menu', array( $this, 'verseFinder_menu') );
		  add_action( 'admin_init', array( $this, 'verseFinder_settings') );
		  register_deactivation_hook( __FILE__, array( $this,  'verseFinder_deactivate') );
		}
		else{
			add_action( 'wp_enqueue_scripts', array( $this, 'verseFinder_assets') );
			add_action('wp_footer', array( $this, 'verseFinder_footer'),50 );
		}
    }
	
	
	
	/**
    * Add javascript file to footer
    * 
    */
	public function verseFinder_assets() {
		wp_enqueue_script( 'verseFinder', '//app.biblesoftonline.com/verseFinder/biblesoft.min.js', array(), '1.0', true );
	}


	/**
    * Create admin settings tab
    * 
    */
	public function verseFinder_menu() {
		add_menu_page('verseFinder Settings', 'Verse Finder', 'administrator', 'versefinder-settings',  array( $this, 'verseFinder_settings_page'), 'dashicons-admin-generic');
	}
	
	
	/**
    * Create HTML for the admin settings page
    * 
    */
	public function verseFinder_settings_page() {
		$bible = esc_attr( get_option('bible',vf_bible) );
		$com = esc_attr( get_option('commentary',vf_commentary) ); 
	?>
		<div class="wrap">
		<style>
			
			.default{
				font-style:italic;
				color:#A3A3A3;
			}
			
			input[type="checkbox"]{
				-webkit-appearance:none;
				display:inline-block;
				width:28px;
				height:16px;
				background:#DEDBDC;
				border-radius:40px;
				outline:none;
				position:relative;
				transition:background 0.1s;
			}
			
			input[type="checkbox"]:checked{
				background:#8CE196;
				color:#8CE196;
				
			}
			input[type="checkbox"]:after{
				width:16px;
				height:16px;
				border-radius:100%;
				top:-2px;
				position:absolute;
				display:inline-block;
				content:'';
				transition:left 0.1s;
				background:white;
				border: 1px solid #DEDBDC;
				left:-2px;
			}
			
			input[type="checkbox"]:checked:after{
				left:14px;
			}
			
			input[type="checkbox"]:checked:before{
				content:'';
				color:#8CE196 !important;
			}
			
			
		</style>
		<h2><img src="https://biblesoft.com/img/biblesoft-1407381645.jpg"> </h2>
		<h2>VerseFinder Settings</h2>
		<p>Learn More at <a href="http://www.biblesoftonline.com/versefinder" target="_blank">http://www.biblesoftonline.com/versefinder</a></p>
		<form method="post" action="options.php">
		<?php settings_fields( 'verseFinder-settings-group' ); ?>
		<?php do_settings_sections( 'verseFinder-settings-group' ); ?>
		<table class="form-table">
		
		<tr valign="top">
		<th scope="row">Default Bible</th>
		<td>
			<select name="bible"  style="width:220px;">
			  <option value="KJV" <?php if ($bible == 'KJV') { print 'selected="SELECTED"'; } ?>>KJV</option>
			  <option value="NKJV" <?php if ($bible == 'NKJV') { print 'selected="SELECTED"'; } ?>>NKJV</option>
			  <option value="NIV" <?php if ($bible == 'NIV') { print 'selected="SELECTED"'; } ?>>NIV</option>
			  <option value="NASB" <?php if ($bible == 'NASB') { print 'selected="SELECTED"'; } ?>>NASB</option>
			  <option value="ESV" <?php if ($bible == 'ESV') { print 'selected="SELECTED"'; } ?>>ESV</option>
			  <option value="ASV" <?php if ($bible == 'ASV') { print 'selected="SELECTED"'; } ?>>ASV</option>
			  <option value="NLT" <?php if ($bible == 'NLT') { print 'selected="SELECTED"'; } ?>>NLT</option>
			  <option value="YLT" <?php if ($bible == 'YLT') { print 'selected="SELECTED"'; } ?>>YLT</option>
			  <option value="DARBY" <?php if ($bible == 'DARBY') { print 'selected="SELECTED"'; } ?>>DARBY</option>
			  <option value="NET Bible" <?php if ($bible == 'NET BIBLE') { print 'selected="SELECTED"'; } ?>>NET</option>
			  <option value="BBE" <?php if ($bible == 'BBE') { print 'selected="SELECTED"'; } ?>>Bible in Basic English</option>
			  <option value="Webster" <?php if ($bible == 'Webster') { print 'selected="SELECTED"'; } ?>>Noah Webster</option>
			  <option value="WEB" <?php if ($bible == 'WEB') { print 'selected="SELECTED"'; } ?>>World Eglish Bible</option>
			  <option value="The Message" <?php if ($bible == 'The Message') { print 'selected="SELECTED"'; } ?>>The Message</option>
			</select>
			
			<span class="default">(default: <?php echo vf_bible; ?>)</span>
		
		
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">Default Commentary</th>
		<td>
			<select name="commentary"  style="width:220px;">
			  <option value="MHC-abridged" <?php if ($com == 'MHC-abridged') { print 'selected="SELECTED"'; } ?>>Matthew Henry's Abridged</option>
			  <option value="CLARKE" <?php if ($com == 'CLARKE') { print 'selected="SELECTED"'; } ?>>Adam Clark</option>
			  <option value="JFB" <?php if ($com == 'JFB') { print 'selected="SELECTED"'; } ?>>James, Fausset, and Brown</option>
			  <option value="BARNES" <?php if ($com == 'BARNES') { print 'selected="SELECTED"'; } ?>>Barnes' Notes</option>
			</select>
			
			<span class="default">(default: <?php echo vf_commentary; ?>)</span>
		</td>
		</tr>
		<tr valign="top">
		<th scope="row">Apply To Tags & Classes</th>
		<td><input type="text" name="applyTo" value="<?php echo esc_attr( get_option('applyTo', vf_applyTo ) ); ?>" /> <span class="default">(default: <?php echo vf_applyTo; ?> - ex: .content, .main, #content)</span></td>
		</tr>
		<tr valign="top">
		<th scope="row">Ignore Tags & Classes</th>
		<td><input type="text" name="ignoreQuery" value="<?php echo esc_attr( get_option('ignoreQuery',vf_ignoreQuery) ); ?>" /> <span class="default">(default: <?php echo vf_ignoreQuery; ?> - ex: .ignoreMe, #noVerses)</span></td>
		</tr>
		<tr valign="top">
		<th scope="row">Red Letters</th>
		<td><input type="checkbox" name="redLetters" value="1" <?php echo checked(1, get_option('redLetters',vf_redLetters), false);?>/></td>
		</tr>
		<tr valign="top">
		<th scope="row">Verse Numbers</th>
		<td><input type="checkbox" name="verseNumbers" value="1" <?php echo checked(1, get_option('verseNumbers',vf_verseNumbers), false);?> /> </td>
		</tr>
		<tr valign="top">
		<th scope="row">Lazy Load (pre-load all verses on the page)</th>
		<td><input type="checkbox" name="lazyLoad" value="1" <?php echo checked(1, get_option('lazyLoad',vf_lazyLoad), false);?>/> </td>
		</tr>
		<tr valign="top">
		<th scope="row">Width</th>
		<td><input type="text" name="width" value="<?php echo esc_attr( get_option('width',vf_width) ); ?>"  size="10"/> <span class="default">(default: <?php echo vf_width; ?>)</span></td>
		</tr>
		<tr valign="top">
		<th scope="row">Height</th>
		<td><input type="text" name="height" value="<?php echo esc_attr( get_option('height',vf_height) ); ?>"  size="10"/> <span class="default">(default: <?php echo vf_height; ?>)</span></td>
		</tr>
		<tr valign="top">
		<th scope="row">Hover In Time (ms)</th>
		<td><input type="text" name="hoverIn" value="<?php echo esc_attr( get_option('hoverIn',vf_hoverIn) ); ?>"  size="10"/> <span class="default">(default: <?php echo vf_hoverIn; ?>)</span></td>
		</tr>
		<tr valign="top">
		<th scope="row">Hover Out Time (ms)</th>
		<td><input type="text" name="hoverOut" value="<?php echo esc_attr( get_option('hoverOut',vf_hoverOut) ); ?>" size="10"/> <span class="default">(default: <?php echo vf_hoverOut; ?>) </span></td>
		</tr>
		
		</table>
		<input type="hidden" name="minimumSearchError" value="<?php echo esc_attr( get_option('minimumSearchError',vf_minimumSearchError) ); ?>" />
		<?php submit_button(); ?>
		 
		</form>
		</div>
	<?php
	}

	/**
    * Add verseFinder init to page/post footers
    * 
    */
	public function verseFinder_footer(){
		
		?>
		<script>
		(function() {
			
			Biblesoft.config({

			  bible:"<?php echo esc_attr( get_option('bible', vf_bible ) ); ?>",
			  commentary:"<?php echo esc_attr( get_option('commentary', vf_commentary ) ); ?>",
			  hoverIn:<?php echo esc_attr( get_option('hoverIn', vf_hoverIn ) ); ?>, 
			  hoverOut:<?php echo esc_attr( get_option('hoverOut', vf_hoverOut ) ); ?>,
			  redLetter:parseFloat(<?php echo esc_attr( get_option('redLetters', vf_redLetters ) ); ?>),
			  verseNumbers:parseFloat(<?php echo esc_attr( get_option('verseNumbers', vf_verseNumbers ) ); ?>),
			  ignoreQuery:"<?php echo esc_attr( get_option('ignoreQuery', vf_ignoreQuery ) ); ?>",
			  minimumSearchError: <?php echo esc_attr( get_option('minimumSearchError', vf_minimumSearchError ) ); ?>,
			  width:<?php echo esc_attr( get_option('width', vf_width ) ); ?>,
			  height:<?php echo esc_attr( get_option('height', vf_height ) ); ?>,
			  onload:"<?php echo esc_attr( get_option('applyTo', vf_applyTo ) ); ?>",
			  lazyLoad:parseFloat(<?php echo esc_attr( get_option('lazyLoad', vf_lazyLoad ) ); ?>)
			});

			}());
			</script>
		<?php
	}


	/**
    * Define setting options for the admin settings
    * 
    */
	public function verseFinder_settings() {
		
		
		
		register_setting( 'verseFinder-settings-group', 'applyTo' );
		register_setting( 'verseFinder-settings-group', 'bible' );
		register_setting( 'verseFinder-settings-group', 'commentary' );
		register_setting( 'verseFinder-settings-group', 'hoverIn' );
		register_setting( 'verseFinder-settings-group', 'hoverOut' );
		register_setting( 'verseFinder-settings-group', 'redLetters' );
		register_setting( 'verseFinder-settings-group', 'verseNumbers' );
		register_setting( 'verseFinder-settings-group', 'ignoreQuery' );
		register_setting( 'verseFinder-settings-group', 'minimumSearchError' );
		register_setting( 'verseFinder-settings-group', 'width' );
		register_setting( 'verseFinder-settings-group', 'height' );
		register_setting( 'verseFinder-settings-group', 'lazyLoad' );
	}

	
	/**
    * Remove settings options from db
    * 
    */
	public function verseFinder_deactivate(){
		
		delete_option('bible');
		delete_option('commentary');
		delete_option('applyTo');
		delete_option('hoverIn');
		delete_option('hoverOut');
		delete_option('redLetters');
		delete_option('verseNumbers');
		delete_option('ignoreQuery');
		delete_option('minimumSearchError');
		delete_option('width');
		delete_option('height');
		delete_option('lazyLoad');
		
		unregister_setting( 'verseFinder-settings-group', 'applyTo' );
		unregister_setting( 'verseFinder-settings-group', 'bible' );
		unregister_setting( 'verseFinder-settings-group', 'commentary' );
		unregister_setting( 'verseFinder-settings-group', 'hoverIn' );
		unregister_setting( 'verseFinder-settings-group', 'hoverOut' );
		unregister_setting( 'verseFinder-settings-group', 'redLetters' );
		unregister_setting( 'verseFinder-settings-group', 'verseNumbers' );
		unregister_setting( 'verseFinder-settings-group', 'ignoreQuery' );
		unregister_setting( 'verseFinder-settings-group', 'minimumSearchError' );
		unregister_setting( 'verseFinder-settings-group', 'width' );
		unregister_setting( 'verseFinder-settings-group', 'height' );
		unregister_setting( 'verseFinder-settings-group', 'lazyLoad' );
		
	}
	
	
}

  
  
$verseFinder = new verseFinder;



?>