<?php
namespace sbnc\addons;

use sbnc\Sbnc;
use sbnc\core\Addon;

/**
 * Class Flasher
 *
 * Uses the util FlashMessages to flash errors and the request to the session.
 *
 * @package sbnc\addons
 */
class Flasher extends Addon
{

    ######################################################################################
    #########################           CONFIGURATION            #########################
    ######################################################################################

    /**
     * May be disabled if any inconsistencies occur
     *
     * @var bool Enable or disable module
     */
    protected $enabled = true;

    /**
     * set if there should be a redirect on error/success
     * add a url to be redirected at the second value like this:
     *
     * 'redirect:error' => 'http://example.com/about/contact'
     * 'redirect:success' => 'http://example.com/about/contact'
     *
     * set to false to disable redirecting or leave empty to redirect back to
     * the requesting url.
     *
     * SET AN EXPLICIT REDIRECT FOR SECURITY REASONS!
     *
     * @var array Options
     */
    protected $options = [
        'redirect:error' => '',
        'redirect:success' => ''
    ];

    ######################################################################################
    ######################################################################################

    private $flash;

    public function init()
    {
        if (Sbnc::utilExists('FlashMessages')) {
            $this->enabled = Sbnc::util('FlashMessages')->isEnabled();
            $this->flash = Sbnc::util('FlashMessages');
        } else {
            $this->disable();
        }
    }

    public function __destruct()
    {
        $errors = Sbnc::errors();
        if ((!empty($errors) && $this->options['redirect:error'] === false) ||
            (empty($errors) && $this->options['redirect:success'] === false)
        ) {
            $this->flash->flush('flasher');
        }
    }

    public function after()
    {
        $errors = Sbnc::errors();

        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0) {
            if (count(Sbnc::errors()) > 0) {
                $this->flash->flash('flasher', 'request', Sbnc::request());
                $this->flash->flash('flasher', 'errors', $errors);
            }
            if (!empty($errors) && $this->options['redirect:error'] !== false) {
                if (empty($this->options['redirect:error'])) {
                    if (!headers_sent()) {
                        Sbnc::redirect(Sbnc::request('url'));
                        exit;
                    } else {
                        $this->flash->flush('flasher');
                    }

                } else {
                    $this->flash->flash('core', 'redirected', true);
                    if (!headers_sent()) {
                        Sbnc::redirect($this->options['redirect:error']);
                        exit;
                    } else {
                        $this->flash->flush('flasher');
                    }
                }
            } elseif (empty($errors) && $this->options['redirect:success'] !== false) {
                if (empty($this->options['redirect:success'])) {
                    if (!headers_sent()) {
                        Sbnc::redirect(Sbnc::request('url'));
                        exit;
                    } else {
                        $this->flash->flush('flasher');
                    }
                } else {
                    if (!headers_sent()) {
                        Sbnc::redirect($this->options['redirect:success']);
                        exit;
                    } else {
                        $this->flash->flush('flasher');
                    }
                    exit;
                }
            } else {
                return;
            }
        }
        // remove messages from session, retrieved from cache
        $this->flash->flush('flasher');
    }

    /**
     * returns array with all errors from session
     *
     * @return mixed
     */
    public function getErrors()
    {
        if (!$this->isEnabled()) return Sbnc::errors();
        $response = $this->flash->get('flasher', 'errors');
        if (!empty($response)) {
            return $response;
        } else {
            $cached = Sbnc::util('FlashMessages')->getCached('flasher', 'errors');
            if ($cached != null) {
                return $cached;
            }
        }
        return [];
    }

    public function countErrors()
    {
        if (!$this->enabled) return count(Sbnc::errors());
        return $this->flash->count('flasher', 'errors');
    }

    public function getRequest($key)
    {
        if (!$this->isEnabled()) return Sbnc::request($key) !== null ? Sbnc::request($key) : '';
        $request = $this->flash->get('flasher', 'request');
        if (isset($request[$key])) {
            $response = $request[$key];
        } else {
            $request = $this->flash->getCached('flasher', 'request');
            if (isset($request[$key])) {
                $response = $request[$key];
            } else {
                $response = '';
            }
        }
        return $response;
    }

    public function wasSubmitted()
    {
        if (!$this->isEnabled()) return strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') === 0;
        if ($this->flash->exists('flasher', 'submitted')) {
            return true;
        }
        return false;
    }

}