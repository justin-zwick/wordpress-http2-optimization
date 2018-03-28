<?php
namespace O10n;

/**
 * HTTP/2 Optimization Controller
 *
 * @package    optimization
 * @subpackage optimization/controllers
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH')) {
    exit;
}

class Http2 extends Controller implements Controller_Interface
{
    private $push_enabled = false;
    private $push_list = array(); // list with resources to push
    private $push_headers = array();

    /**
     * Load controller
     *
     * @param  Core       $Core Core controller instance.
     * @return Controller Controller instance.
     */
    public static function &load(Core $Core)
    {
        // instantiate controller
        return parent::construct($Core, array(
            'tools',
            'url',
            'http',
            'options',
            'env'
        ));
    }

    /**
     * Setup controller
     */
    protected function setup()
    {
        if (!$this->env->is_optimization()) {
            return;
        }

        // HTTP/2 Server Push requires SSL
        $this->push_enabled = ($this->options->bool('http2.push.enabled') && $this->env->is_ssl());
        if ($this->push_enabled) {

            // get push list
            $this->push_list = $this->options->get('http2.push.list');

            // add filter for HTTP headers output
            add_filter('o10n_headers', array( $this, 'push_headers' ), 10, 1);

            // add HTTP/2 meta
            if ($this->options->get('http2.push.meta')) {
                add_action('wp_head', array($this, 'add_meta'));
            }
        }
    }

    /**
     * Output HTTP/2 server push headers
     *
     * @param string $buffer HTML buffer
     */
    public function push_headers($buffer)
    {
        if (!$this->env->is_optimization()) {
            return;
        }
        
        if (!$this->push_enabled || empty($this->push_list) || headers_sent()) {
            return;
        }

        // create HTTP/2 Push Headers
        $push_headers = array();
        $header_index = 0;
        $header_count = 0;
        $resources_per_header = 50;

        foreach ($this->push_list as $resource) {
            if (!is_array($resource) || !isset($resource['url']) || !isset($resource['as'])) {
                continue;
            }

            // push header
            $push_header = sprintf(
                '<%s>; rel=preload; as=%s',
                $resource['url'],
                sanitize_html_class($resource['as'])
            );

            // detect if URL is local
            if (!isset($resource['local'])) {
                $resource['local'] = $this->url->is_local($resource['url'], false, false);
            }
            if (!$resource['local']) {
                $push_header .= '; crossorigin';
            }
            if (isset($resource['type']) && $resource['type']) {
                $push_header .= sprintf('; type=\'%s\'', str_replace('\'', '\\\'', $resource['type']));
            }

            // add header to push
            if (!isset($push_headers[$header_index])) {
                $push_headers[$header_index] = array();
            }

            $push_headers[$header_index][] = $push_header;

            $header_count++;

            if ($header_count === $resources_per_header) {
                $header_count = 0;
                $header_index++;
            }
        }

        // output HTTP/2 Push Headers
        foreach ($push_headers as $links) {
            header('Link: ' . implode(',', $links), false);
        }
    }

    /**
     * Include HTTP/2 push meta
     */
    final public function add_meta()
    {
        if (!$this->env->is_optimization()) {
            return;
        }
        
        if (empty($this->push_list)) {
            return;
        }
        foreach ($this->push_list as $resource) {
            if (!is_array($resource) || !isset($resource['url']) || !isset($resource['as'])) {
                continue;
            }
            $link = sprintf(
                '<link rel="preload" href="%s" as="%s"',
                str_replace('"', '&quot;', $resource['url']),
                str_replace('"', '&quot;', sanitize_html_class($resource['as']))
            );

            // detect if URL is local
            if (!isset($resource['local'])) {
                $resource['local'] = $this->url->is_local($resource['url'], false, false);
            }
            if (!$resource['local']) {
                $link .= ' crossorigin';
            }
            if (isset($resource['type']) && $resource['type']) {
                $link .= sprintf(' type="%s"', str_replace('"', '&quot;', $resource['type']));
            }
            print $link .'>';
        }
    }

    /**
     * Push resource
     *
     * @param string $url     URL to push
     * @param string $as      as=""
     * @param string $type    type=""
     * @param array  $filter  Apply include/exclude filter to URL
     * @param bool   $isLocal Identify if URL is cross origin
     */
    final public function push($url, $as, $type = false, $filter = false, $isLocal = null)
    {
        if (!$this->env->is_optimization()) {
            return;
        }
        
        // push disabled
        if (!$this->push_enabled) {
            return false;
        }

        // apply filter
        if ($filter && is_array($filter)) {
            $filterType = $filter[0];
            $filterList = $filter[1];

            // verify filter config
            if (in_array($filterType, array('include','exclude')) && !empty($filterList)) {

                // match filter list against url
                if (!$this->tools->filter_list_match($url, $filterType, $filterList)) {
                    return false;
                }
            }
        }

        // detect if resource is local
        if (is_null($isLocal)) {
            $isLocal = $this->url->is_local($url, false, false);
        }

        // push resource
        $this->push_list[] = array(
            'url' => $url,
            'local' => $isLocal,
            'as' => $as,
            'type' => $type
        );

        return true;
    }
}
