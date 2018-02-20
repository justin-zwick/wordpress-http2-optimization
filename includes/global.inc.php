<?php
namespace O10n;

/**
 * Global functions
 *
 * @package    optimization
 * @subpackage optimization/controllers
 * @author     Optimization.Team <info@optimization.team>
 */

function push($url, $as, $type = false)
{
    Core::get('http2')->push($url, $as, $type);
}
