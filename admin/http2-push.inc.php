<?php
namespace O10n;

/**
 * HTTP/2 optimization admin template
 *
 * @package    optimization
 * @subpackage optimization/admin
 * @author     Optimization.Team <info@optimization.team>
 */
if (!defined('ABSPATH') || !defined('O10N_ADMIN')) {
    exit;
}

// print form header
$this->form_start(__('HTTP/2 Optimization', 'o10n'), 'http2');

?>

<table class="form-table">
    <tr valign="top">
        <th scope="row">HTTP/2 Server Push</th>
        <td>
            
            <label><input type="checkbox" name="o10n[http2.push.enabled]" data-json-ns="1" value="1"<?php $checked('http2.push.enabled'); ?>> Enable
</label>
            <p class="description" style="margin-bottom:1em;">When enabled, resources such as scripts, stylesheets and images can be pushed to visitors together with the HTML (<a href="https://developers.google.com/web/fundamentals/performance/http2/#server_push" target="_blank">documentation</a>).</p>
</td></tr>

    <tr valign="top" data-ns="http2.push"<?php $visible('http2.push');  ?>>
        <th scope="row">&nbsp;</th>
        <td style="padding-top:0px;">

            <h5 class="h">&nbsp;HTTP/2 Push List <a href="https://github.com/o10n-x/wordpress-http2-optimization/tree/master/docs#http2-configuration" target="_blank" title="HTTP/2 Server Push Configuration" style="top: -4px;position: relative;display: inline-block;text-decoration: none;"><span class="dashicons dashicons-editor-help"></span></a></h5>
            <div id="http2-push-list"><div class="loading-json-editor"><?php print __('Loading JSON editor...', 'o10n'); ?></div></div>
            <input type="hidden" class="json" name="o10n[http2.push.list]" data-json-editor-height="auto" data-json-type="json-array" data-json-editor-height="auto" data-json-editor-init="1" value="<?php print esc_attr($json('http2.push.list')); ?>" />

            <div class="info_yellow"><strong>Example:</strong> <code class="clickselect" title="<?php print esc_attr('Click to select', 'optimization'); ?>" style="cursor:copy;">{"url": "/wp-content/themes/theme-x/images/logo.png", "as": "image", "type": "image/png"}</code></div>
            
            <div class="suboption" data-ns="http2.push"<?php $visible('http2.push'); ?>>
                <label><input type="checkbox" name="o10n[http2.push.meta]" data-json-ns="1" value="1"<?php $checked('http2.push.meta'); ?>> Add <code>&lt;link rel="preload" as="" ...&gt;</code> meta</label>
            </div>
        </td>
    </tr>
    <tr valign="top" data-ns="http2.push"<?php $visible('http2.push');  ?>>
        <th scope="row">Cache-Digest</th>
        <td>
        <?php if (!defined('O10N_PWA_VERSION')) {
    ?>
<p class="description">Install the <a href="#">PWA Optimization</a> plugin to use this feature.</p>
<?php
} else {
        ?>
             <label><input type="checkbox" name="o10n[pwa.cache_digest]" data-json-ns="1" value="1"<?php $checked('pwa.cache_digest'); ?>> Enable
            </label>
            <p class="description" style="margin-bottom:1em;">When enabled, the PWA Service Worker calculates a <a href="https://calendar.perfplanet.com/2016/cache-digests-http2-server-push/" target="_blank" rel="noopener">Cache Digest</a> based on previously pushed resources.</p>
<?php
    }
?>
        </td>
    </tr>
    </table>
<hr />
<?php
    submit_button(__('Save'), 'primary large', 'is_submit', false);

// print form header
$this->form_end();
