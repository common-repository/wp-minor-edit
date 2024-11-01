<?php
/*
Plugin Name: WP Minor Edit
Plugin URI: http://ciarang.com/posts/wp-minor-edit
Description: Provide the ability to mark an edit as minor, meaning the update time is not modified.
Author: Ciaran Gultnieks
Version: 0.73
Author URI: http://ciarang.com

Revision history
0.1  - 2008-08-12: Preview release
0.2  - 2008-08-13: Updated to work with new hooks in trunk, see http://trac.wordpress.org/changeset/8635
0.3  - 2008-08-27: Updated to work with modified filter name
0.4  - 2008-08-29: Packaged properly, with README and subdirectory
0.5  - 2008-08-29: wordpress.org/extend compatible readme.txt
0.6  - 2008-09-07: Internationalization support (not released)
0.7  - 2008-12-04: Made style better match the new WP2.7 admin interface,
0.71 - 2009-06-26: Tested with 2.8
0.72 - 2010-07-10: Tested with 3.0
0.73 - 2012-08-21: Updated to confirm compatibility

*/

class MinorEdit
{

	// Our text domain, for internationalisation
	var $textdom='wp-minor-edit';

	// Constructor
	function MinorEdit() {
		add_action('post_submitbox_start', array(&$this,'post_submitbox_start'));
		add_filter('wp_insert_post_data', array(&$this,'wp_insert_post'),1,2);
		$this->inited=false;
	}

	// Lazy initialise. All non-trivial members should call this before doing anything else.
	function lazyinit() {
		if(!$this->inited) {
			load_plugin_textdomain($this->textdom, PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
			$this->inited=true;
		}
	}

	// Action handler - The 'Save' button is about to be drawn on the advanced edit screen...
	function post_submitbox_start()
	{
		$this->lazyinit();
		global $post;
		// Add the checkbox, but only if the post is already published.
		if($post->post_status=='publish') { ?>
			<div style="text-align:right;" id="minor_edit_box"><label for="minor_edit" class="selectit"><input style="min-width:25px;" name="minor_edit" type="checkbox" id="minor_edit" value="minor_edit" /><?php
			_e('Minor edit',$this->textdom) ?></label></div>
		<?php
		}
	}

	// Filter handler - Post data is about to be inserted
	function wp_insert_post($data,$post_arr) {
		$this->lazyinit();
		if(true==$post_arr['minor_edit']) {
			// The user specified that this is a minor edit, so put the modified date back to
			// what it was before the edit...
			$data['post_modified']=$post_arr['post_modified'];
			$data['post_modified_gmt']=$post_arr['post_modified_gmt'];
		}
		return $data;
	}


}

$wp_minoredit = new MinorEdit();


?>
