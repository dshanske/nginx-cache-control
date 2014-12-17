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

// Add timestamps as a comment to posts
function add_timestamps() {
            
          //  if (options['enable_stamp'] != 1)
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


 function invalidate_cache_cron(){
  wp_schedule_single_event(time(), 'invalidate_cache');
}
// add_action('save_post', 'invalidate_cache_cron');



// Improvement over Old Trigger Purge which would only work
// on Post Publish - This will also purge if article is unpublished
function transition_stat( $new, $old, $post ) {
        if ( 'publish' !== $old && 'publish' !== $new )
            return;
            $post = get_post( $post );
            $url = get_permalink( $post );

        // Purge this URL

        invalidate( $url );

        // Purge the front page
        invalidate( home_url( '/' ) );

        // If Post_Type is a Post 
	if ( 'post' === $post->post_type ) {
            // Purge the main feeds
            invalidate( home_url( '/feed/' ) );
            invalidate( home_url( '/feed/atom/' ) );
            invalidate( home_url( '/feed/rdf/' ) );
	}
       $args = array(
			'public'   => true,
		);
		/* get taxonomies as objects */
		$taxonomies = get_taxonomies( $args, 'objects' );
	if ( !empty( $taxonomies ) ) {
			foreach ( $taxonomies  as $taxonomy ) {
				/* reset array, just in case */
				$terms = array();
				/* get all the terms for this taxonomy, only if not empty */
				$sargs = array(
					'hide_empty'    => true,
					'fields'        => 'all',
					'hierarchical'  =>false,
				);
				$terms = get_terms ( $taxonomy->name , $sargs );
				if ( !empty ( $terms ) ) {
					foreach ( $terms as $term ) {
						/* get the permalink for the term */
						$link = get_term_link ( $term->slug, $taxonomy->name );
						/* remove the taxonomy name from the link, lots of plugins remove this for SEO, it's better to include them than leave them out
						   in worst case, we cache some 404 as well
						*/
						$link = str_replace ( '/'.$taxonomy->rewrite['slug'], '', $link  );
						/* Invalidate */
						invalidate($link);
					}
				}
			}
		}
    }

add_action( 'save_post', 'transition_stat' );

function invalidate( $url ) {
	$response = wp_remote_get( $url, array( 'timeout' => 0.01, 'blocking' => false, 'headers' => array( 'X-Nginx-Cache-Purge' => '1' ) ) );
	if ( is_wp_error( $response ) ) {
	    $_errors_str = implode( " - ", $response->get_error_messages() );
       // $this->log( "Error while purging URL. " . $_errors_str, "ERROR" );
	   } else {
		if ( $response[ 'response' ][ 'code' ] ) {
		     switch ( $response[ 'response' ][ 'code' ] ) {
			case 200:
			   // " *** PURGED ***"
			      break;
			case 404:
			    //  " not cached"
			      break;
			default:
			  //    "WARNING"
					}
				}
			}
	}


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


// Deprecated Older Function
//	add_action('publish_post', 'trigger_purge');
?>
