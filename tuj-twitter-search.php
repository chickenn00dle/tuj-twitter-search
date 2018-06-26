<?php 

/**
 * Plugin Name:   TUJ Twitter Search Widget
 * Plugin URI:    https://www.tuj.ac.jp
 * Description:   Adds a widget to display twitter searches by hashtag.
 * Version:       1.0.0
 * Author:        Rasmy Nguyen
 * Author URI:    https://twitter.com/ChickenN00dle
 */

require_once('includes/tuj-twitter-search-admin.php');
require_once('includes/tuj-twitter-search-query.php');


class tuj_Twitter_Search_Widget extends WP_Widget {
  public function __construct() {
    $widget_options = array( 
      'classname' => 'twitter_search_widget',
      'description' => 'This is a Twitter Search Widget',
    );
    parent::__construct( 'twitter_search_widget', 'Twitter Search Widget', $widget_options );
  }

  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance[ 'title' ] );
    $titleClass = $instance['title-class'];
    $hashtag = $instance['hashtag'];
    $banner = isset($instance['banner']) ? $instance['banner'] : false;
    $data = queryTwitter($hashtag);

    _e($args['before_widget']);
    ?>
    <div class="tweet-category">
      <h3 class="tweet-title <?php _e($instance['title-class']);?>">
        <?php _e($title);?>
      </h3>
    <?php if ($banner) { ?>
        <figure class="bm-20px">
          <a href="https://twitter.com/search?q=%23<?php _e($hashtag);?>&src=typd" target="_blank">
            <img src="<?php _e($banner); ?>" alt="#<?php _e($hashtag);?> Tweets" />
          </a>
        </figure>
    <?php
      }

      if (isset($data->statuses[0])) {
        $user = $data->statuses[0]->user;
        $profileName = $user->name;
        $profileScreen = $user->screen_name;
        $profileImg = $user->profile_image_url;
        $profileUrl = $user->url;
    ?>
      <div class="tweet-author">
        <div class="tweet-author-img">
          <a href="https://twitter.com/tujweb" target="_blank">
            <img class="tweet-profile-img" src="<?php _e($profileImg); ?>"/>
          </a>
        </div>
        <div class="tweet-author-details">
          <p class="tweet-profile-name bm-none"><?php _e($profileName); ?></p>
          <p class="tweet-profile-screen bm-none"><a href="https://twitter.com/tujweb" target="_blank">@<?php _e($profileScreen); ?></a></p>
        </div>
      </div>
    <?php
      foreach ($data->statuses as $status) {
        $date = date('F j, Y', strtotime($status->created_at));
        $fullText = $status->full_text;
        preg_match('/\((.*)\)/m', $fullText, $mediaDate);
        preg_match( '/^(.*)\(.*$/m', $fullText, $text);
        $url = isset($status->entities->urls[0]) ? $status->entities->urls[0]->url : false;
    ?>
        <div class="tweet-result">
          <p class="tweet-media-date"><strong><?php _e($mediaDate[1]);?></strong></p>
          <p class="tweet-text">
            <?php _e($text[1]); ?>
            <?php if ($url) : ?>
              <a href="<?php _e($url); ?>" target="_blank"><?php _e($url); ?></a>
            <?php endif; ?>
            <a href="https://twitter.com/search?q=from%3Atujweb+%23
              <?php_e($hashtag); ?>&src=typd" target="_blank">#<?php _e($hashtag); ?>
            </a> 
            <a href="https://twitter.com/search?q=from%3Atujweb+%23tujinthemedia&src=typd" target="_blank">
              #TUJintheMedia
            </a>
          </p>
        </div>
      <?php } ?>
        <ul class="link-medium">
          <li><i class="li-spr"></i>
            <a href="https://twitter.com/search?q=from%3Atujweb+%23<?php _e($hashtag); ?>" target="_blank">
              View more
            </a>
          </li>
        </ul>
    <?php } else { ?>
      <div class="tweet-result">
        <p class="tweet-text">
          See all of our 
          <a href="https://twitter.com/search?q=from%3Atujweb+%23'<?php _e($hashtag); ?>" target="_blank">
            #<?php _e($hashtag); ?>
          </a>
          tweets on our official 
          <a href="https://twitter.com/tujweb" target="_blank">
            @TUJWeb
          </a>
          Twitter account.
        </p>
      </div>
    <?php } ?>

    </div>
    
    <?php _e($args['after_widget']); 
  }

  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
    $titleClass = ! empty( $instance['title-class'] ) ? $instance['title-class'] : '';
    $hashtag = ! empty( $instance['hashtag'] ) ? $instance['hashtag'] : '';
    $banner = ! empty( $instance['banner'] ) ? $instance['banner'] : '';
  ?>
    <div class="wrap">
      <div>
        <label for="<?php _e($this->get_field_id( 'title' )); ?>">
          Title: 
        </label>
        <input type="text" id="<?php _e($this->get_field_id( 'title' )); ?>" name="<?php _e($this->get_field_name( 'title' )); ?>" value="<?php _e(esc_attr( $title )); ?>" />
      </div>

      <div>
        <label for="_e($this->get_field_id( 'title-class' )); ?>">
          Title Class: 
        </label>
        <input type="text" id="<?php _e($this->get_field_id( 'title-class' )); ?>" name="<?php _e($this->get_field_name( 'title-class' )); ?>" value="<?php _e(esc_attr( $titleClass )); ?>" />
      </div>

      <div>
        <label for="<?php _e($this->get_field_id( 'hashtag' )); ?>'">
          Hashtag: 
        </label>
        <input type="text" id="<?php _e($this->get_field_id( 'hashtag' )); ?>" name="<?php _e($this->get_field_name( 'hashtag' )); ?>" value="<?php _e(esc_attr( $hashtag )); ?>" />
      </div>

      <div>
        <label for="<?php _e($this->get_field_id( 'banner' )); ?>">
          Banner Source: 
        </label>
        <input type="text" id="<?php _e($this->get_field_id( 'banner' )); ?>" name="<?php _e($this->get_field_name( 'banner' )); ?>" value="<?php _e(esc_attr( $banner )); ?>" />
      </div>
    </div>
  <?php
  }

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