<?php

/*
Plugin Name: Foundry Notes
Plugin URI: mailto:jonesjaycob@gmail.com
Description:  Creates a section for notes page by page.
Author: Jaycob A. Jones
Version: 1.0
Author URI: mailto:jonesjaycob@gmail.com
*/

/**
 * Adds a meta box to the post editing screen
 */
function foundry_custom_meta() {
    add_meta_box( 'foundry_meta', __( 'Foundry Notes', 'foundry-textdomain' ), 'foundry_meta_callback', 'post' );
    add_meta_box( 'foundry_meta', __( 'Foundry Notes', 'foundry-textdomain' ), 'foundry_meta_callback', 'page' );
}
add_action( 'add_meta_boxes', 'foundry_custom_meta' );



/**
 * Outputs the content of the meta box
 */
function foundry_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'foundry_nonce' );
    $foundry_stored_meta = get_post_meta( $post->ID );

    // make sure the form request comes from WordPress
    wp_nonce_field( basename( __FILE__ ), 'food_meta_box_nonce' );
    // retrieve the _food_cholesterol current value
    $current_cholesterol = get_post_meta( $post->ID, '_food_cholesterol', true );
    // retrieve the _food_carbohydrates current value
    $current_carbohydrates = get_post_meta( $post->ID, '_food_carbohydrates', true );

    $vitamins = array( 'Knock Knock', 'Thiamin (B1)', 'Riboflavin (B2)', 'Niacin (B3)', 'Pantothenic Acid (B5)', 'Vitamin B6', 'Vitamin B12', 'Vitamin C', 'Vitamin D', 'Vitamin E', 'Vitamin K' );

    // stores _food_vitamins array
    $current_vitamins = ( get_post_meta( $post->ID, '_food_vitamins', true ) ) ? get_post_meta( $post->ID, '_food_vitamins', true ) : array();

    ?>

    <p xmlns="http://www.w3.org/1999/html">
        <label for="meta-text" class="foundry-row-title"><?php _e( 'Insert Notes:', 'foundry-textdomain' )?></label>
        <br><br>
        <textarea name="meta-text" id="meta-text" value="" style="width:100%;height:180px;" /><?php if ( isset ( $foundry_stored_meta['meta-text'] ) ) echo $foundry_stored_meta['meta-text'][0]; ?></textarea>
        <br>

    </p>

<!--    <div class='inside'>-->
<!---->
<!--        <h3>--><?php //_e( 'Cholesterol', 'food_example_plugin' ); ?><!--</h3>-->
<!--        <p>-->
<!--            <input type="radio" name="cholesterol" value="0" --><?php //checked( $current_cholesterol, '0' ); ?><!-- /> Yes<br />-->
<!--            <input type="radio" name="cholesterol" value="1" --><?php //checked( $current_cholesterol, '1' ); ?><!-- /> No-->
<!--        </p>-->
<!---->
<!--        <h3>--><?php //_e( 'Carbohydrates', 'food_example_plugin' ); ?><!--</h3>-->
<!--       -->
<!---->
<!--        <h3>--><?php //_e( 'Vitamins', 'food_example_plugin' ); ?><!--</h3>-->
<!--        <p>-->
<!--            --><?php
//            foreach ( $vitamins as $vitamin ) {
//                ?>
<!--                <input type="checkbox" name="vitamins[]" value="--><?php //echo $vitamin; ?><!--" --><?php //checked( ( in_array( $vitamin, $current_vitamins ) ) ? $vitamin : '', $vitamin ); ?><!-- />--><?php //echo $vitamin; ?><!-- <br />-->
<!--                --><?php
//            }
//            ?>
<!--        </p>-->
<!--    </div>-->


    <?php
}



/**
 * Saves the custom meta input
 */
function foundry_meta_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'foundry_nonce' ] ) && wp_verify_nonce( $_POST[ 'foundry_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'meta-text' ] ) ) {
        update_post_meta( $post_id, 'meta-text', sanitize_text_field( $_POST[ 'meta-text' ] ) );
    }


    if ( !isset( $_POST['food_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['food_meta_box_nonce'], basename( __FILE__ ) ) ){
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ){
        return;
    }
    if ( isset( $_REQUEST['cholesterol'] ) ) {
        update_post_meta( $post_id, '_food_cholesterol', sanitize_text_field( $_POST['cholesterol'] ) );
    }
// store custom fields values
// carbohydrates string
    if ( isset( $_REQUEST['carbohydrates'] ) ) {
        update_post_meta( $post_id, '_food_carbohydrates', sanitize_text_field( $_POST['carbohydrates'] ) );
    }
// store custom fields values
// vitamins array
    if( isset( $_POST['vitamins'] ) ){
        $vitamins = (array) $_POST['vitamins'];
        // sinitize array
        $vitamins = array_map( 'sanitize_text_field', $vitamins );
        // save data
        update_post_meta( $post_id, '_food_vitamins', $vitamins );
    }else{
        // delete data
        delete_post_meta( $post_id, '_food_vitamins' );
    }
}
add_action( 'save_post', 'foundry_meta_save' );