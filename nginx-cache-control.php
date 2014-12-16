<?php
/**
 * Plugin Name: Nginx Cache Control
 * Plugin URI: https://github.com/dshanske/nginx-cache-control
 * Description: Handles cache control for Nginx FastCGI without need for the Purge module
 * Version: 0.01
 * Author: David Shanske
 * Author URI: http://david.shanske.com
 * License: CC0
 */

       function add_timestamps() {
            
          //  if ($this->options['enable_stamp'] != 1)
           //     return;
            if (is_admin())
                return;
            foreach (headers_list() as $header) {
                list($key, $value) = explode(':', $header, 2);
                if ($key == 'Content-Type' && strpos(trim($value), 'text/html') !== 0) {
                    return;
                }
                if ($key == 'Content-Type')
                    break;
            }
            if (defined('DOING_AJAX') && DOING_AJAX)
                return;
            $timestamps = "\n<!--" .
                    "Generated on " . current_time('mysql') . ". " .
                    "It took " . get_num_queries() . " queries executed in " . timer_stop() . " seconds." .
                    "-->\n";
            echo $timestamps;
        }

  add_action('shutdown', 'add_timestamps', 99999);



	function trigger_purge () {
	               if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;       			$post = get_post( $post );
			$url = get_permalink( $post );
			// Purge this URL
			invalidate( $url );
			// Purge the front page
			invalidate( home_url( '/' ) );
			// Purge the feed
                        // purge( home_url( '/feed/' ) )
			// Purge the News Sitemap
                        // purge( home_url( '/news-sitemap.xml' ) );
                        // Purge the sitemap index
                       //  purge( home_url( '/sitemap_index.xml' ) );
	}

	function invalidate( $url ) {
		wp_remote_get( $url, array( 'timeout' => 0.01, 'blocking' => false, 'headers' => array( 'X-Nginx-Cache-Purge' => '1' ) ) );
	}

	add_action('publish_post', 'trigger_purge');
?>
