<?php 

/**
 * Plugin Name:   TUJ Twitter Search Widget
 * Plugin URI:    https://www.tuj.ac.jp/news
 * Description:   Adds a widget to display twitter searches by hashtag.
 * Version:       1.0.0
 * Author:        Rasmy Nguyen
 * Author URI:    https://twitter.com/ChickenN00dle
 */

defined( 'ABSPATH' ) or die( 'Forbidden Script' );

require_once('includes/tuj-twitter-search-admin.php');
require_once('includes/tuj-twitter-search-query.php');


class tuj_Twitter_Search_Widget extends WP_Widget {
  public function __construct() {
    $widget_options = array( 
      'classname' => 'twitter_search_widget',
      'description' => 'The unofficial TUJ Twitter Search Widget',
    );
    parent::__construct( 'twitter_search_widget', 'Twitter Search Widget', $widget_options );
  }

  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance[ 'title' ] );
    $titleClass = $instance['title-class'];
    $hashtag = $instance['hashtag'];
    $banner = isset($instance['banner']) ? $instance['banner'] : false;
    $profile = isset($instance['profile']) && $instance['profile'] ? true : false;
    $options = get_option('tujts_options');
    $profileName = $options['tujts_field_account_name'];
    $profileScreen = strtolower($options['tujts_field_account_handle']);
    $profileImg = $options['tujts_field_profile_img'];
    $profileUrl = 'https://twitter.com/' . $profileScreen;
    $data = queryTwitter($hashtag);

    esc_html_e($args['before_widget']);
    ?>
    <div class="tweet-category">
      <h3 class="tweet-title <?php esc_attr_e($instance['title-class']);?>">
        <?php esc_html_e($title);?>
      </h3>
    <?php if ($banner) { ?>
        <figure class="bm-20px">
          <a href="<?php echo esc_url( 'https://twitter.com/search?q=%23' ) . esc_html($hashtag) . '&src=typd'; ?>" target="_blank">
            <img src="<?php echo esc_url($banner); ?>" alt="#<?php esc_html_e($hashtag . ' Tweets'); ?>" />
          </a>
        </figure>
    <?php } if ($profile) { ?>
        <div class="tweet-author">
          <div class="tweet-author-img">
            <a href="<?php echo esc_url( $profileUrl ); ?>" target="_blank">
              <img class="tweet-profile-img" src="<?php echo esc_url($profileImg); ?>"/>
            </a>
          </div>
          <div class="tweet-author-details">
            <p class="tweet-profile-name bm-none"><?php esc_html_e($profileName); ?></p>
            <p class="tweet-profile-screen bm-none"><a href="<?php esc_url( $profileUrl ); ?>" target="_blank">@<?php esc_html_e($profileScreen); ?></a></p>
          </div>
        </div>
    <?php }
      if (isset($data->statuses[0])) {
        foreach ($data->statuses as $status) {
          $date = date('F j, Y', strtotime($status->created_at));
          $fullText = $status->full_text;
          preg_match('/\((.*)\)/m', $fullText, $mediaDate);
          preg_match( '/^(.*)\(.*$/m', $fullText, $text);
          $url = isset($status->entities->urls[0]) ? $status->entities->urls[0]->url : false;
    ?>
        <div class="tweet-result">
          <p class="tweet-media-date"><strong><?php esc_html_e($mediaDate[1]);?></strong></p>
          <p class="tweet-text">
            <?php esc_html_e($text[1]); ?>
            <?php if ($url) : ?>
              <a href="<?php echo esc_url($url); ?>" target="_blank"><?php echo esc_url($url); ?></a>
            <?php endif; ?>
            <a href=<?php echo esc_url( 'https://twitter.com/search?q=from%3A' . esc_html($profileScreen)  . '+%23' . esc_html($hashtag) . '&src=typd'); ?> target="_blank">#<?php esc_html_e($hashtag); ?>
            </a> 
            <a href=<?php echo esc_url( 'https://twitter.com/search?q=from%3A' . esc_html( $profileScreen ) .  '+%23tujinthemedia&src=typd'); ?> target="_blank">
              #<?php esc_html_e('TUJintheMedia'); ?>
            </a>
          </p>
        </div>
      <?php } ?>
        <ul class="link-medium">
          <li><i class="li-spr"></i>
            <a href=<?php echo esc_url( 'https://twitter.com/search?q=from%3A' . esc_html( $profileScreen ) . '+%23' . esc_html($hashtag)); ?> target="_blank">
              View more
            </a>
          </li>
        </ul>
    <?php } else { ?>
        <div class="tweet-result">
          <p class="tweet-text">
            See all of our 
            <a href=<?php esc_url( 'https://twitter.com/search?q=from%3A' . esc_html( $profileScreen ) . '+%23' . esc_html($hashtag)); ?> target="_blank">
              #<?php esc_html_e($hashtag); ?>
            </a>
            tweets on our official 
            <a href=<?php echo esc_url( 'https://twitter.com/' . esc_html( $profileScreen )) ?> target="_blank">
              @<?php esc_html_e( $profileScreen ); ?>
            </a>
            Twitter account.
          </p>
        </div>
    <?php } ?>

    </div>
    
    <?php esc_html_e($args['after_widget']); 
  }

  public function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : '';
    $titleClass = ! empty( $instance['title-class'] ) ? $instance['title-class'] : '';
    $hashtag = ! empty( $instance['hashtag'] ) ? $instance['hashtag'] : '';
    $banner = ! empty( $instance['banner'] ) ? $instance['banner'] : '';
    $profile = ! empty( $instance[ 'profile' ] ) ? $instance['profile'] : '';
  ?>
    <div class="wrap">
      <p>
        <label for="<?php esc_attr_e($this->get_field_id( 'title' )); ?>">
          <?php esc_html_e('Title:'); ?>
          <input 
            class="widefat" 
            type="text" 
            id="<?php esc_attr_e($this->get_field_id( 'title' )); ?>" 
            name="<?php esc_attr_e($this->get_field_name( 'title' )); ?>" 
            value="<?php esc_attr_e( $title ); ?>" 
          />
        </label>
      </p>
      <p>
        <label for="<?php esc_attr_e($this->get_field_id( 'title-class' )); ?>">
          Title Class: 
          <input 
            class="widefat" 
            type="text" 
            id="<?php esc_attr_e($this->get_field_id( 'title-class' )); ?>" 
            name="<?php esc_attr_e($this->get_field_name( 'title-class' )); ?>" 
            value="<?php esc_attr_e( $titleClass ); ?>" 
          />
        </label>
      </p>
      <p>
        <label for="<?php esc_attr_e($this->get_field_id( 'hashtag' )); ?>'">
          Hashtag: 
          <input 
            class="widefat" 
            type="text" 
            id="<?php esc_attr_e($this->get_field_id( 'hashtag' )); ?>" 
            name="<?php esc_attr_e($this->get_field_name( 'hashtag' )); ?>" 
            value="<?php esc_attr_e($hashtag ); ?>" 
          />
        </label>
      </p>
      <p>
        <label for="<?php esc_attr_e($this->get_field_id( 'banner' )); ?>">
          Banner Source: 
          <input 
            class="widefat" 
            type="text" 
            id="<?php esc_attr_e($this->get_field_id( 'banner' )); ?>"
            name="<?php esc_attr_e($this->get_field_name( 'banner' )); ?>"
            value="<?php esc_attr_e( $banner ); ?>" 
          />
        </label>
      </p>
      <p>
        <input 
          class="checkbox"
          type="checkbox" 
          <?php checked( $profile, 'on' ); ?>
          id="<?php esc_attr_e($this->get_field_id( 'profile' )); ?>" 
          name="<?php esc_attr_e($this->get_field_name( 'profile' )); ?>" 
        />
        <label for="<?php esc_attr_e($this->get_field_id( 'profile' )); ?>">Show Profile</label>
      </p>
    </div>
  <?php
  }

  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
    $instance[ 'title-class' ] = strip_tags( $new_instance[ 'title-class' ] );
    $instance[ 'hashtag' ] = strip_tags( $new_instance[ 'hashtag' ] );
    $instance[ 'test' ] = strip_tags( $new_instance[ 'test' ] );
    $instance[ 'banner' ] = esc_url($new_instance[ 'banner' ]);
    $instance[ 'profile' ] = ! empty( $new_instance[ 'profile' ] ) ? strip_tags( $new_instance[ 'profile' ] ) : '';
    return $instance;
  }
}

// Register plugin
function tuj_register_twitter_search_widget() { 
  register_widget( 'tuj_Twitter_Search_Widget' );
}
add_action( 'widgets_init', 'tuj_register_twitter_search_widget' );

// Add plugin CSS
function add_twitter_search_css(){
    wp_enqueue_style( 'twitter-search-stylesheet', plugins_url('/css/style.css', __FILE__), false, '1.0.0', 'all');
}
add_action('wp_enqueue_scripts', "add_twitter_search_css");
?>