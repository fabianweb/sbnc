<?php
namespace Sbnc\Modules;

/**
 * sbnc gestures module
 *
 * Checks for gestures like keyboard and mouse usage
 *
 * @package    Sbnc
 * @subpackage Modules
 * @author     Fabian Pirklbauer <hi@fabianweb.net>
 * @copyright  2014-2015 Fabian Pirklbauer
 * @license    https://github.com/fabianweb/sbnc/LICENSE.md
 * @version    0.1
 * @link       https://github.com/fabianweb/sbnc/modules/
 */
class gestures {

    /*
     * Options for checking keyboard and mouse usage.
     *
     * auto:     checks mouse and keyboard on first request
     *           and only mouse when errors occurred
     * mouse:    checks mouse usage only
     * keyboard: checks keyboard usage only
     *
     * @var array
     */
    private $options = [
        'mode' => 'auto'
    ];

    /**
     * Adds two fields to sbnc
     *
     * @param $master
     */
    public function __construct(&$master) {
        $master['fields']['mouse']    = false;
        $master['fields']['keyboard'] = false;
    }

    /**
     * Starts module check
     *
     * @param $master
     */
    public function check($master) {

    }

}