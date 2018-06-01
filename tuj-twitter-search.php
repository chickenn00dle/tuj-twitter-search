<?php 

/**
 * Plugin Name:   TUJ Twitter Search Widget
 * Plugin URI:    https://www.tuj.ac.jp
 * Description:   Adds a widget for TUJintheMedia displaying twitter searches by title.
 * Version:       1.0.0
 * Author:        Rasmy Nguyen
 * Author URI:    https://twitter.com/ChickenN00dle
 */

require_once('includes/functions.php');


class tuj_Twitter_Search_Widget extends WP_Widget {
  // Constructor function
  public function __construct() {
    $widget_options = array( 
      'classname' => 'twitter_search_widget',
      'description' => 'This is a Twitter Search Widget',
    );
    parent::__construct( 'twitter_search_widget', 'Twitter Search Widget', $widget_options );
  }

  // Display in the front end widget space
  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance[ 'title' ] );
    $titleClass = $instance['title-class'];
    $hashtag = $instance['hashtag'];
    $banner = isset($instance['banner']) ? $instance['banner'] : false;
    $data = queryTwitter($hashtag);

    echo $args['before_widget'];

    echo '<h3 class="' . $instance['title-class'] . '">' . $title . '</h3>';
    if ($banner) {
      echo '<div class="tweet-category">
              <figure class="bm-20px">
                <a href="https://twitter.com/search?q=%23' . $hashtag . '&src=typd" target="_blank">
                  <img src="' . $banner . '" alt="#' . $hashtag  . ' Tweets" />
                </a>
              </figure>
              <div class="tweet-result">';
    }

    if (isset($data->statuses[0])) {
      $user = $data->statuses[0]->user;
      $profileName = $user->name;
      $profileScreen = $user->screen_name;
      $profileImg = $user->profile_image_url;
      $profileUrl = $user->url;

      echo '<div class="tweet-author">
              <div class="tweet-author-img">
                <a href="https://twitter.com/tujweb" target="_blank">
                  <img class="tweet-profile-img" src="' . $profileImg . '"/>
                </a>
              </div>
              <div class="tweet-author-details">
                <p class="tweet-profile-name bm-none">' . $profileName  . '</p>
                <p class="tweet-profile-screen bm-none"><a href="https://twitter.com/tujweb" target="_blank">@' . $profileScreen  . '</a></p>
              </div>
            </div>
            <hr class="tweet-divider">';
      foreach ($data->statuses as $status) {
        $date = date('F j, Y', strtotime($status->created_at));
        $text = $status->full_text;
        $text = preg_replace( '/https:\/\/.*/i', '', $text);
        $url = isset($status->entities->urls[0]) ? $status->entities->urls[0]->url : false;
        echo '<p class="tweet-text bm-20px">' . $text . '<a href="' . $url . '" target="_blank">' . $url . '</a> <a href="https://twitter.com/search?q=from%3Atujweb+%23' . $hashtag . '&src=typd" target="_blank">#' . $hashtag . '</a></p>
        
          <hr class="tweet-divider">';
      }
    } else {
      echo '<p class="tweet-text bm-20px">See all of our <a href="https://twitter.com/search?q=from%3Atujweb+%23' . $hashtag . '" target="_blank">#' . $hashtag .  '</a> tweets on our official <a href="https://twitter.com/tujweb" target="_blank">@TUJWeb</a> Twitter account.</p>
        
        <hr class="tweet-divider">';
    }

    echo '</div>
      </div>';
    
    echo $args['after_widget'];
  }

  // Display widget options in wp-admin
  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
    $titleClass = ! empty( $instance['title-class'] ) ? $instance['title-class'] : '';
    $hashtag = ! empty( $instance['hashtag'] ) ? $instance['hashtag'] : '';
    $banner = ! empty( $instance['banner'] ) ? $instance['banner'] : '';

    echo '<br /><div><label for="' . $this->get_field_id( 'title' ) . '">Title: </label><input type="text" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" value="' .  esc_attr( $title ) . '" /></div><br />';

    echo '<div><label for="' . $this->get_field_id( 'title-class' ) . '">Title Class: </label><input type="text" id="' . $this->get_field_id( 'title-class' ) . '" name="' . $this->get_field_name( 'title-class' ) . '" value="' .  esc_attr( $titleClass ) . '" /></div><br />';

    echo '<div><label for="' . $this->get_field_id( 'hashtag' ) . '">Hashtag: </label><input type="text" id="' . $this->get_field_id( 'hashtag' ) . '" name="' . $this->get_field_name( 'hashtag' ) . '" value="' .  esc_attr( $hashtag ) . '" /></div><br />';

    echo '<div><label for="' . $this->get_field_id( 'banner' ) . '">Banner Source: </label><input type="text" id="' . $this->get_field_id( 'banner' ) . '" name="' . $this->get_field_name( 'banner' ) . '" value="' .  esc_attr( $banner ) . '" /></div><br />';
  }

  // Updates database with widget values
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
    $instance[ 'title-class' ] = strip_tags( $new_instance[ 'title-class' ] );
    $instance[ 'hashtag' ] = strip_tags( $new_instance[ 'hashtag' ] );
    $instance[ 'banner' ] = strip_tags( $new_instance[ 'banner' ] );
    return $instance;
  }
}

// Add plugin CSS
function add_twitter_search_css(){
    wp_enqueue_style( 'twitter-search-stylesheet', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
}
add_action('wp_enqueue_scripts', "add_twitter_search_css");

// Register plugin
function tuj_register_twitter_search_widget() { 
  register_widget( 'tuj_Twitter_Search_Widget' );
}
add_action( 'widgets_init', 'tuj_register_twitter_search_widget' );
?>