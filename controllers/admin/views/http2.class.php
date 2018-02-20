<?php
namespace O10n;

/**
 * HTTP/2 Optimization Admin View Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers/admin
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH')) {
    exit;
}

class AdminViewHttp2 extends AdminViewBase
{
    protected static $view_key = 'http2'; // reference key for view
    protected $module_key = 'http2';

    // default tab view
    private $default_tab_view = 'intro';

    /**
     * Load controller
     *
     * @param  Core       $Core   Core controller instance.
     * @param  false      $module Module parameter not used for core view controllers
     * @return Controller Controller instance.
     */
    public static function &load(Core $Core)
    {
        // instantiate controller
        return parent::construct($Core, array(
            'json',
            'file',
            'AdminClient'
        ));
    }
    
    /**
     * Setup controller
     */
    protected function setup()
    {
        // WPO plugin
        if (defined('O10N_WPO_VERSION')) {
            $this->default_tab_view = 'optimization';
        }
        // set view etc
        parent::setup();
    }

    /**
     * Setup view
     */
    public function setup_view()
    {
        // process form submissions
        add_action('o10n_save_settings_verify_input', array( $this, 'verify_input' ), 10, 1);

        // enqueue scripts
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), $this->first_priority);
    }

    /**
     * Return help tab data
     */
    final public function help_tab()
    {
        $data = array(
            'name' => __('HTTP/2 Optimization', 'o10n'),
            'github' => 'https://github.com/o10n-x/wordpress-http2-optimization',
            'wordpress' => 'https://wordpress.org/support/plugin/http2-optimization',
            'docs' => 'https://github.com/o10n-x/wordpress-http2-optimization/tree/master/docs'
        );

        return $data;
    }

    /**
     * Enqueue scripts and styles
     */
    final public function enqueue_scripts()
    {
        // skip if user is not logged in
        if (!is_admin() || !is_user_logged_in()) {
            return;
        }

        // set module path
        $this->AdminClient->set_config('module_url', $this->module->dir_url());
    }


    /**
     * Return view template
     */
    public function template($view_key = false)
    {
        // template view key
        $view_key = false;

        $tab = (isset($_REQUEST['tab'])) ? trim($_REQUEST['tab']) : $this->default_tab_view;
        switch ($tab) {
            case "optimization":
                $view_key = 'http2';
            break;
            case "intro":
                $view_key = 'http2-' . $tab;
            break;
            default:
                throw new Exception('Invalid view ' . esc_html($view_key), 'core');
            break;
        }

        return parent::template($view_key);
    }
    
    /**
     * Verify settings input
     *
     * @param  object   Form input controller object
     */
    final public function verify_input($forminput)
    {
        // HTTP/2 Optimization

        $forminput->type_verify(array(
            'http2.push.enabled' => 'bool',
            'http2.push.list' => 'json-array',
            'http2.push.meta' => 'bool',
            'pwa.cache_digest' => 'bool'
        ));
    }
}
