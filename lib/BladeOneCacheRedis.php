<?php


namespace eftec\bladeone;

use Redis;

/**
 * trait BladeOneCacheRedis
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Extends the tags of the class BladeOne.  Its optional
 * It adds the next tags to the template
 * <code>
 * @ cache([cacheid],[duration=86400]).  The id is optional. The duration of the cache is in seconds
 * // content here
 * @ endcache()
 * </code>
 * It also adds a new function (optional) to the business or logic layer
 * <code>
 * if ($blade->cacheExpired('hellocache',1,5)) {   //'helloonecache' =template, =1 id cache, 5=duration (seconds)
 *    // cache expired, so we should do some stuff (such as read from the database)
 * }
 * </code>
 *
 * @package  BladeOneCacheRedis
 * @version  0.1 2017-12-15 NOT YET IMPLEMENTED, ITS A WIP!!!!!!!!
 * @link     https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */
const CACHEREDIS_SCOPEURL = 1;

trait BladeOneCacheRedis
{
    protected $curCacheId = 0;
    protected $curCacheDuration = "";
    protected $curCachePosition = 0;
    protected $cacheRunning = false;
    /** @var \Redis $redis */
    protected $redis;
    protected $redisIP = '127.0.0.1';
    protected $redisPort = 6379;
    protected $redisTimeOut = 2.5;
    protected $redisConnected = false;
    protected $redisNamespace = 'bladeonecache:';
    protected $redisBase = 0;
    private $cacheExpired = []; // avoids to compare the file different times.

    //<editor-fold desc="compile">
    public function compileCache($expression)
    {
        // get id of template
        // if the file exists then
        //     compare date.
        //     if the date is too old then re-save.
        // else
        // save for the first time.

        return $this->phpTag . "echo \$this->cacheStart{$expression}; if(!\$this->cacheRunning) { ?>";
    }

    public function compileEndCache($expression)
    {
        return $this->phpTag . "} // if cacheRunning\necho \$this->cacheEnd{$expression}; ?>";
    }
    //</editor-fold>

    public function connect($redisIP = null, $redisPort = null, $redisTimeOut = null)
    {
        if ($this->redisConnected) {
            return true;
        }
        if (!\class_exists('Redis')) {
            return false; // it requires redis.
        }
        if ($redisIP !== null) {
            $this->redisIP = $redisIP;
            $this->redisPort = $redisPort;
            $this->redisTimeOut = $redisTimeOut;
        }
        $this->redis = new Redis();
        // 2.5 sec timeout.
        $this->redisConnected = $this->redis->connect($this->redisIP, $this->redisPort, $this->redisTimeOut);

        return $this->redisConnected;
    }

    /**
     * Returns true if the cache expired (or doesn't exist), otherwise false.
     *
     * @param string $templateName  name of the template to use (such hello for template hello.blade.php)
     * @param string $id            (id of cache, optional, if not id then it adds automatically a number)
     * @param int    $scope         scope of the cache.
     * @param int    $cacheDuration (duration of the cache in seconds)
     * @return bool (return if the cache expired)
     */
    public function cacheExpired($templateName, $id, $scope, $cacheDuration)
    {
        if ($this->getMode() & 1) {
            return true; // forced mode, hence it always expires. (fast mode is ignored).
        }
        $compiledFile = $this->getCompiledFile($templateName) . '_cache' . $id;
        if (isset($this->cacheExpired[$compiledFile])) {
            // if the information is already in the array then returns it.
            return $this->cacheExpired[$compiledFile];
        }
        $date = @\filemtime($compiledFile);
        if ($date) {
            if ($date + $cacheDuration < \time()) {
                $this->cacheExpired[$compiledFile] = true;
                return true; // time-out.
            }
        } else {
            $this->cacheExpired[$compiledFile] = true;
            return true; // no file
        }
        $this->cacheExpired[$compiledFile] = false;
        return false; // cache active.
    }

    public function cacheStart($id = "", $cacheDuration = 86400)
    {
        $this->curCacheId = ($id == "") ? ($this->curCacheId + 1) : $id;
        $this->curCacheDuration = $cacheDuration;
        $this->curCachePosition = \strlen(\ob_get_contents());
        $compiledFile = $this->getCompiledFile() . '_cache' . $this->curCacheId;
        if ($this->cacheExpired('', $id, $cacheDuration)) {
            $this->cacheRunning = false;
        } else {
            $this->cacheRunning = true;
            $content = $this->getFile($compiledFile);
            echo $content;
        }
        // getFile($fileName)
    }

    public function cacheEnd()
    {
        if (!$this->cacheRunning) {
            $txt = \substr(\ob_get_contents(), $this->curCachePosition);
            $compiledFile = $this->getCompiledFile() . '_cache' . $this->curCacheId;
            \file_put_contents($compiledFile, $txt);
        }
        $this->cacheRunning = false;
    }

    private function keyByScope($scope)
    {
        $key = '';
        if ($scope && CACHEREDIS_SCOPEURL) {
            $key .= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        }
    }
}
