<?php
  defined( 'ABSPATH' ) or die( 'Forbidden Script' );
  function tuj_twitter_search_settings_init() {
    register_setting('tujts', 'tujts_options');

    add_settings_section(
      'tujts_section_developers',
      __('Account Settings', 'tujts'),
      'tujts_section_developers_cb',
      'tujts'
    );

    add_settings_field(
      'tujts_field_account_name',
      __('Account Name', 'tujts'),
      'tujts_field_account_cb',
      'tujts',
      'tujts_section_developers',
      [
        'label_for' => 'tujts_field_account_name',
        'class' => 'tujts_row',
        'tujts_custom_data' => 'custom',
        'name' => 'Account Name',
      ]
    );

    add_settings_field(
      'tujts_field_account_handle',
      __('Account Handle', 'tujts'),
      'tujts_field_account_cb',
      'tujts',
      'tujts_section_developers',
      [
        'label_for' => 'tujts_field_account_handle',
        'class' => 'tujts_row',
        'tujts_custom_data' => 'custom',
        'name' => 'Account Name',
      ]
    );

    add_settings_field(
      'tujts_field_profile_img',
      __('Profile Image URL', 'tujts'),
      'tujts_field_account_cb',
      'tujts',
      'tujts_section_developers',
      [
        'label_for' => 'tujts_field_profile_img',
        'class' => 'tujts_row',
        'tujts_custom_data' => 'custom',
        'name' => 'Account Name',
      ]
    );

    add_settings_field(
      'tujts_field_oath_token',
      __('OAuth Access Token', 'tujts'),
      'tujts_field_cb',
      'tujts',
      'tujts_section_developers',
      [
        'label_for' => 'tujts_field_oath_token',
        'class' => 'tujts_row',
        'tujts_custom_data' => 'custom',
        'name' => 'OAuth Token',
      ]
    );
    add_settings_field(
      'tujts_field_oath_secret',
      __('OAuth Access Token Secret', 'tujts'),
      'tujts_field_cb',
      'tujts',
      'tujts_section_developers',
      [
        'label_for' => 'tujts_field_oath_secret',
        'class' => 'tujts_row',
        'tujts_custom_data' => 'custom',
        'name' => 'OAuth Token Secret', 
      ]
    );
    add_settings_field(
      'tujts_field_consumer_key',
      __('Consumer Key', 'tujts'),
      'tujts_field_cb',
      'tujts',
      'tujts_section_developers',
      [
        'label_for' => 'tujts_field_consumer_key',
        'class' => 'tujts_row',
        'tujts_custom_data' => 'custom',
        'name' => 'Consumer Key', 
      ]
    );
    add_settings_field(
      'tujts_field_consumer_secret',
      __('Consumer Secret', 'tujts'),
      'tujts_field_cb',
      'tujts',
      'tujts_section_developers',
      [
        'label_for' => 'tujts_field_consumer_secret',
        'class' => 'tujts_row',
        'tujts_custom_data' => 'custom',
        'name' => 'Consumer Secret',
      ]
    );
  }

  add_action('admin_init', 'tuj_twitter_search_settings_init');

  function tujts_section_developers_cb($args) {
    ?>
    <p id="<?php echo esc_attr( $args[ 'id' ] ); ?>"><?php esc_html_e( 'Enter account information below. Twitter OAUTH and Consumer keys/tokens are generated via' ); ?> <a href="<?php echo esc_url('https://apps.twitter.com/'); ?>" target="blank">Twitter Apps</a>.</p>
    <?php
  }

  function tujts_field_account_cb($args) {
    $options = get_option('tujts_options');
    ?>
    <input id="<?php echo esc_attr( $args['label_for'] ); ?>"
    data-custom="<?php echo esc_attr( $args['tujts_custom_data'] ); ?>"
    name="tujts_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
    <?php if (isset($options[$args['label_for']])) : ?>
      value="<?php esc_html_e( $options[$args['label_for']]); ?>"
    <?php endif; ?>
    />
    <?php if (! isset($options[ $args['label_for'] ])) : ?>
    <p class="description">
      <?php esc_html_e('Required. Please enter your Twitter ' . $args['name']); ?>
    </p>
    <?php endif;
  }

  function tujts_field_cb($args) {
    $options = get_option('tujts_options');
    ?>
    <input id="<?php echo esc_attr( $args[ 'label_for' ] ); ?>"
    data-custom="<?php echo esc_attr( $args[ 'tujts_custom_data' ] ); ?>"
    name="tujts_options[<?php echo esc_attr( $args[ 'label_for' ] ); ?>]"
    />
    <p class="description">
      <?php 
        isset($options[ $args['label_for'] ]) ? 
          esc_html_e($args['name'] . ' is set. To update, re-enter key.') :
          esc_html_e( 'Required. Please enter your Twitter ' . $args['name']); 
      ?>
    </p>
    <?php
  }

  function tujts_options_page() {
    add_menu_page(
      'TUJ Twitter Search',
      'TUJTS',
      'manage_options',
      'tujts',
      'tujts_options_page_html'
    );
  }

  add_action('admin_menu', 'tujts_options_page');

  function tujts_options_page_html() {
    if (! current_user_can( 'manage_options' )) {
      return;
    }
    if ( isset( $_GET['settings-updated'] ) ) {
      add_settings_error( 'tujts_messages', 'tujts_message', __( 'Settings Saved', 'tujts' ), 'updated' );
    }
    settings_errors( 'tujts_messages' );
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
        <?php
          settings_fields( 'tujts' );
          do_settings_sections( 'tujts' );
          submit_button( 'Save Settings' );
        ?>
      </form>
    </div>
    <?php
  }