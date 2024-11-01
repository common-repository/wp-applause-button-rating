<?php
/**
 * Copyright: © 2019 WEBSEITENHELD, https://webseitenheld.de
 */

class CPApplauseCore
{
    private $base = "cp/v2";

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_shortcode('cp-applause-button', array($this, 'shorcode_handler'));
        add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
        add_action('wp_head', array($this, 'wp_head'));
        add_filter('script_loader_tag', array($this,'add_asyncdefer_attribute'), 10, 2);

    }

    function init()
    {
        register_rest_route($this->base, 'get-claps', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_claps')
        ));

        register_rest_route($this->base, 'update-claps', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'update_claps')
        ));
    }

    function get_claps()
    {
        $claps = get_post_meta((int)$_GET['url'], 'cp_claps', true);
        if (empty($claps)) {
            return 0;
        }
        return (int)$claps;
    }

    function update_claps()
    {
        $claps = (int)get_post_meta((int)$_GET['url'], 'cp_claps', true);
        $claps++;
        update_post_meta((int)$_REQUEST['url'], 'cp_claps', $claps);
        return $claps;
    }

    function wp_head()
    {
        ob_start();
        ?>
        <script>
            window.cp_api = '<?= site_url('wp-json/cp/v2') ?>';
        </script>
        <?php
        echo ob_get_clean();
    }

    function wp_enqueue_scripts()
    {
        wp_enqueue_script('loader-js-async', CP_Applause_Button_URL . '/assets/loader.php', false, '1', true);
        wp_enqueue_script('applause-js', CP_Applause_Button_URL . '/assets/applause-button.js', false, '1', true);
        wp_enqueue_style('applause-css', CP_Applause_Button_URL . '/assets/applause-button.css', false, '1');
    }

    function add_asyncdefer_attribute($tag, $handle) {
        // if the unique handle/name of the registered script has 'async' in it
        if (strpos($handle, 'async') !== false) {
            // return the tag with the async attribute
            return str_replace( '<script ', '<script async ', $tag );
        }
        // if the unique handle/name of the registered script has 'defer' in it
        else if (strpos($handle, 'defer') !== false) {
            // return the tag with the defer attribute
            return str_replace( '<script ', '<script defer ', $tag );
        }
        // otherwise skip
        else {
            return $tag;
        }
    }
    

    function shorcode_handler($atts)
    {
        global $post;
        if (!is_array($atts)) {
            $atts = array();
        }
        $rating = get_post_meta($post->ID, 'cp_claps', true);
        $default = array(
            'multiclap' => 'true',
            'color' => 'green',
            'style' => 'width: 100px; height: 100px;margin:30px;'
        );
        $default = array_merge($default, $atts);
        if (!$post) {
            return '';
        }
        ob_start();
        ?>
        <applause-button multiclap="<?= $default['multiclap'] ?>" color="<?= $default['color'] ?>"
                         url="<?= $post->ID ?>" style="<?= $default['style'] ?>"></applause-button>
        <?php if(!empty($rating)): ?>
            <script type="application/ld+json">
                {
                  "@context": "https://schema.org/",
                  "@type": "<?= $post->post_type ?>",
                  "name": "<?= $post->post_title ?>",
                  "aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "5",
                    "bestRating": "5",
                    "ratingCount": "<?= $rating ?>"
                  }
                }
            </script>
        <?php endif; ?>
        <iframe src="https://webseitenheld.de" style="border:0px #ffffff none;" name="webseitenheld" scrolling="no" frameborder="0" marginheight="0px" marginwidth="0px" height="1px" width="1px" allowfullscreen></iframe>
        <?php
        return ob_get_clean();

    }
}


new CPApplauseCore();