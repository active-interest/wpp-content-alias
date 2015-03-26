<?php

require_once(__DIR__.'/core/class-wpp-content-alias.php');
require_once(__DIR__.'/core/class-wpp-content-alias-admin.php');
/**
 * Implements example command.
 */
class ContentAlias_Command extends WP_CLI_Command {

    private $current_offset = 0;
    private $posts_per_page = 10;
    private $post_types = array();

    /**
     * Inits WPP-Content-Alias
     * 
     * ## EXAMPLES
     * 
     *     wp content-alias init
     *
     */
    function init( $args, $assoc_args ) {
        list( $name ) = $args;
        $this->post_types = get_post_types(array(
            'public' => true,
            'publicly_queryable' => true,
        ));

        $posts = $this->get_posts();
        while(count($posts) > 0) {
            foreach($posts as $post) {
                $aliases = get_post_meta( $post->ID, WPP_Content_Alias::POSTMETA_CONTENT_ALIAS );
                // the new alias
                $post_alias = WPP_Content_Alias::sanitize_url_path( get_permalink($post) );
                // add to list
                array_push($aliases, $post_alias);
                // sync old and new
                WPP_Content_Alias_Admin::sync_aliases($post->ID, $aliases);

                WP_CLI::line('(' . $post->ID . ') ' . $post->post_title . ' aliased as ' . $post_alias . ' with ' . WPP_Content_Alias::POSTMETA_CONTENT_ALIAS);
            }
            flush();
            ob_flush();
            $posts = $this->get_posts();
        }
    }

    private function get_posts() {
        $posts = get_posts(array(
            'posts_per_page' => $this->posts_per_page,
            'offset' => $this->current_offset,
            'orderby' => 'ID',
            'order' => 'ASC',
            'post_type' => $this->post_types,
        ));
        $this->current_offset += $this->posts_per_page;
        return $posts;
    }
}

WP_CLI::add_command( 'content-alias', 'ContentAlias_Command' );