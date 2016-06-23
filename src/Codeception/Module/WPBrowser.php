<?php

namespace Codeception\Module;

use Symfony\Component\BrowserKit\Cookie;

/**
 * A Codeception module offering specific WordPress browsing methods.
 */
class WPBrowser extends PhpBrowser
{
    use WPBrowserMethods;

    /**
     * The module required fields, to be set in the suite .yml configuration file.
     *
     * @var array
     */
    protected $requiredFields = ['adminUsername', 'adminPassword', 'adminPath'];
    /**
     * The login screen absolute URL
     *
     * @var string
     */
    protected $loginUrl;
    /**
     * The admin absolute URL.
     *
     * @var [type]
     */
    protected $adminPath;
    /**
     * The plugin screen absolute URL
     *
     * @var string
     */
    protected $pluginsPath;

    /**
     * Initializes the module setting the properties values.
     * @return void
     */
    public function _initialize()
    {
        parent::_initialize();
        $adminPath = $this->config['adminPath'];
        $this->loginUrl = str_replace('wp-admin', 'wp-login.php', $adminPath);
        $this->adminPath = rtrim($adminPath, '/');
        $this->pluginsPath = $this->adminPath . '/plugins.php';
    }

    /**
     * Returns all the cookies whose name matches a regex pattern.
     *
     * @param string $cookiePattern
     *
     * @return Cookie|null
     */
    public function grabCookiesWithPattern($cookiePattern)
    {
        /**
         * @var Cookie[]
         */
        $cookies = $this->client->getCookieJar()->all();

        if (!$cookies) {
            return null;
        }
        $matchingCookies = array_filter($cookies, function (Cookie $cookie) use ($cookiePattern) {

            return preg_match($cookiePattern, $cookie->getName());
        });
        $cookieList = array_map(function (Cookie $cookie) {
            return sprintf('{"%s": "%s"}', $cookie->getName(), $cookie->getValue());
        }, $matchingCookies);

        $this->debug('Cookies matching pattern ' . $cookiePattern . ' : ' . implode(', ', $cookieList));

        return is_array($matchingCookies) ? $matchingCookies : null;
    }
}
