<?php

/**
*
*	Process various gallery fucntions like fetching and saving images
*
*/
class aesopEditorProcessGallery {

	function __construct(){

		add_action( 'wp_ajax_process_get_images', 				array($this, 'process_get_images' ));
		add_action( 'wp_ajax_process_create_gallery', 				array($this, 'process_create_gallery' ));
	}

	function process_create_gallery(){

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'process_create_gallery' ) {

			// only run for logged in users and check caps
			if( !is_user_logged_in() || !current_user_can('edit_posts') )
				return;

			// ok security passes so let's process some data
			if ( wp_verify_nonce( $_POST['nonce'], 'aesop_create_gallery' ) ) {

				$gallery_ids = isset( $_POST['gallery_ids']) ? $_POST['gallery_ids'] : false;

				// insert a new gallery
				$post_args = array(
				  	'post_title'    => rand(),
				  	'post_status'   => 'publish',
				  	'post_type'	  	=> 'ase_gallery',
				  	'post_author'   => (int) get_current_user_ID()
				);

				$post_id = wp_insert_post( $post_args );

				// push gallery ids
				//update_post_meta( $post_id,'_ase_gallery_images', $gallery_ids );

				echo 'success';

			}
		}

		die();
	}

	/**
	*
	*	When the user clicks the settings icon in the gallery component it 
	*	opens the panel, gets the gallery unique, then makes a call to get the gallery images
	*	@since 0.12
	*/
	function process_get_images(){

		if ( isset( $_POST['action'] ) && $_POST['action'] == 'process_get_images' ) {

			// only run for logged in users and check caps
			if( !is_user_logged_in() || !current_user_can('edit_posts') )
				return;

			// bail if no id specified like on new galleries
			if ( empty( $_POST['post_id'] ) )
				return;

			// ok security passes so let's process some data
			if ( wp_verify_nonce( $_POST['nonce'], 'aesop_get_gallery_images' ) ) {

				$postid 	= isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;

				// fetch image ids from cache
				$image_ids 	= get_post_meta($postid,'_ase_gallery_images', true);

				// send ids to return images
				self::get_images( $image_ids );

			} else {

				echo 'error';
			}
		}

		die();
	}

	function get_images( $image_ids = '' ) {

		if ( empty( $image_ids ) )
			return;

		$image_ids 	= array_map('intval', explode(',', $image_ids));

		echo '<ul id="ase-gallery-images">';

			if ( !empty( $image_ids ) ):
				foreach ($image_ids as $image_id):

		            $image    =  wp_get_attachment_image_src($image_id, 'thumbnail', false);

		        	?>
		        	<li id="<?php echo $image_id;?>" class="ase-gallery-image">
		        		<i class="dashicons dashicons-no-alt" title="Delete From Gallery"></i>
		        		<i class='dashicons dashicons-edit' title="Edit Image Caption"></i>
		           	<img src="<?php echo $image[0];?>">
		          </li>
		          <?php

				endforeach;

			endif;

		echo '</ul>';

	}


}
new aesopEditorProcessGallery;



