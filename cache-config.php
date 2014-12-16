<?php

function add_ncc_options_to_menu(){
	add_options_page( '', 'Nginx Cache Control', 'manage_options', 'ncc_options', 'ncc_options_form');
}

add_action('admin_menu', 'add_ncc_options_to_menu');

add_action( 'admin_init', 'ncc_options_init' );
function ncc_options_init() {
    register_setting( 'ncc_options', 'ncc_options' );
    add_settings_section( 'ncc-options', 'Purge Options', 'ncc_options_callback', 'ncc_options' );
    add_settings_field( 'purge', 'Enable Purge', 'ncc_callback', 'ncc_options', 'ncc-options' ,  array( 'name' => 'purge') );
}

function ncc_options_callback()
   {
	echo 'Nginx Purge Options';
   }

function ncc_callback(array $args)
   {
        $options = get_option('ncc_options');
        $name = $args['name'];
        $checked = $options[$name];
        echo "<input name='ncc_options[$name]' type='hidden' value='0' />";
        echo "<input name='ncc_options[$name]' type='checkbox' value='1' " . checked( 1, $checked, false ) . " /> ";
   }

function ncc_options_form() 
  {
    ?>
     <div class="wrap">
        <h2>Nginx Cache Control</h2>  
        <p>Invalidates Cache on Post Update</p>

        <hr />
	
        <form method="post" action="options.php">
        <?php settings_fields( 'ncc_options' ); ?>

         <?php do_settings_sections( 'ncc_options' ); ?>
         <?php submit_button(); ?>
       </form>
     </div>
    <?php
 }

?>
