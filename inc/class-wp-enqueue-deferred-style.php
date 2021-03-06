<?php
if(!class_exists('WP_Enqueue_Deferred_Style')){
  class WP_Enqueue_Deferred_Style{

    private $model;

    // Setup singleton pattern
    public static function get_instance(){
      static $instance;

      if(null === $instance){
        $instance = new self();
      }

      return $instance;
    }

    private function __clone(){
      return null;
    }

    private function __wakeup(){
      return null;
    }

    public static function deactivate(){
      self::clear_options();
    }

    private static function clear_options(){
      global $wpdb;
      $options = $wpdb->get_col('SELECT option_name FROM ' . $wpdb->options . ' WHERE option_name LIKE \'%wp_enqueue_deferred_style%\'');
      foreach($options as $option){
        delete_option($option);
      }
    }

    private function __construct(){
      require_once(WP_ENQUEUE_DEFERRED_STYLE_PATH . 'inc/class-model-wp-enqueue-deferred-style.php');

      $this->model = new Model_WP_Enqueue_Deferred_Style();

      add_action('init', array($this, 'add_update_hook'));
      add_action('wp_print_styles', array($this, 'defer_styles'));
      add_action('wp_print_scripts', array($this, 'enqueue_styles'));
    }

    public function add_update_hook(){
      if(get_option('wp_enqueue_deferred_style_version') !== WP_ENQUEUE_DEFERRED_STYLE_VERSION){
        do_action('wp_enqueue_deferred_style_updated');
        update_option('wp_enqueue_deferred_style_update_timestamp', time());
        update_option('wp_enqueue_deferred_style_version', WP_ENQUEUE_DEFERRED_STYLE_VERSION);
      }
    }

    public function register_deferred_style($handle, $src, $deps = array(), $ver = false, $media = 'all'){
      $this->model->register_deferred_style($handle, $src, $deps, $ver, $media);
    }

    public function enqueue_deferred_style($handle, $src = '', $deps = array(), $ver = false, $media = 'all'){
      $this->model->enqueue_deferred_style($handle, $src, $deps, $ver, $media);
    }

    public function defer_styles(){
      $this->model->do_defer();
    }

    public function enqueue_styles(){
      $this->model->do_enqueue();
    }

  }
}
