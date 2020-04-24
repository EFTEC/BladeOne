<?php /** @noinspection TypeUnsafeComparisonInspection */
/** @noinspection PhpUnused */

/** @noinspection DuplicatedCode */


namespace eftec\bladeone;

use function file_put_contents;
use function filemtime;
use function ob_get_contents;
use function strlen;
use function substr;
use function time;

/**
 * trait BladeOneCache
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
 * @package  BladeOneCache
 * @version  1.5 2020-04-24
 * @link     https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */
trait BladeOneCache
{
    protected $curCacheId = 0;
    protected $curCacheDuration = '';
    protected $curCachePosition = 0;
    protected $cacheRunning = false;
    protected $cachePageRunning=false;
    /**
     * @var array avoids to compare the file different times. It also avoids race conditions.
     */
    private $cacheExpired = [];
    /**
     * @var string=['get','post','getpost','request',null][$i]
     */
    private $cacheStrategy;

    /**
     * @return null|string $cacheStrategy=['get','post','getpost','request',null][$i]
     */
    public function getCacheStrategy()
    {
        return $this->cacheStrategy;
    }

    /**
     * It sets the strategy of the cache page.
     *
     * @param null|string $cacheStrategy=['get','post','getpost','request',null][$i]
     */
    public function setCacheStrategy($cacheStrategy)
    {
        $this->cacheStrategy = $cacheStrategy;
    }

    /**
     * It obtains an unique GUID based in:<br>
     * <b>get</b>= parameters from the url<br>
     * <b>post</b>= parameters sends via post<br>
     * <b>getpost</b> = a mix between get and post<br>
     * <b>request</b> = get, post and cookies (including sessions)<br>
     *
     * @return string
     */
    private function cacheUniqueGUID()
    {
        switch ($this->cacheStrategy) {
            case 'get':
                $r = md5(serialize($_GET));
                break;
            case 'post':
                $r = md5(serialize($_POST));
                break;
            case 'getpost':
                $arr = array_merge($_GET, $_POST);
                $r = md5(serialize($arr));
                break;
            case 'request':
                $r = md5(serialize($_REQUEST));
                break;
            default:
                $r = null;
        }
        return $r;
    }

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

    /**
     * It get the filename of the compiled file (cached). If cache is not enabled, then it
     * returns the regular file.
     *
     * @param string $view
     * @return string The full filename
     */
    private function getCompiledFileCache($view)
    {
        $id=$this->cacheUniqueGUID();
        if ($id!==null) {
            return str_replace($this->compileExtension, '_cache' . $id.$this->compileExtension, $this->getCompiledFile($view));
        }
        return $this->getCompiledFile($view);
    }

    /**
     * run the blade engine. It returns the result of the code.
     *
     * @param string $view The name of the cache. Ex: "folder.folder.view" ("/folder/folder/view.blade")
     * @param array $variables An associative arrays with the values to display.
     * @param int $ttl time to live (in second).
     * @return string
     */
    public function runCache($view, $variables = [], $ttl = 86400)
    {
        $this->cachePageRunning=true;
        if ($this->cachePageExpired($view, $ttl)) {
            $this->cacheStart('_page_', $ttl);
            echo $this->run($view, $variables); // if no cache, then it runs normally.
            $this->cacheEnd(); // and it stores as a cache paged.
            $content='';
        } else {
            $content = $this->getFile($this->getCompiledFileCache($view));
        }
        $this->cachePageRunning=false;
        return $content;
    }

    /**
     * Returns true if the block cache expired (or doesn't exist), otherwise false.
     *
     * @param string $templateName  name of the template to use (such hello for template hello.blade.php)
     * @param string $id            (id of cache, optional, if not id then it adds automatically a number)
     * @param int    $cacheDuration (duration of the cache in seconds)
     * @return bool (return if the cache expired)
     */
    public function cacheExpired($templateName, $id, $cacheDuration)
    {
        if ($this->getMode() & 1) {
            return true; // forced mode, hence it always expires. (fast mode is ignored).
        }
        $compiledFile = $this->getCompiledFile($templateName) . '_cache' . $id;
        return $this->cacheExpiredInt($compiledFile, $cacheDuration);
    }
    
    /**
     * It returns true if the whole page expired.
     *
     * @param string $templateName
     * @param int $cacheDuration
     * @return bool
     */
    public function cachePageExpired($templateName, $cacheDuration)
    {
        if ($this->getMode() & 1) {
            return true; // forced mode, hence it always expires. (fast mode is ignored).
        }
        $compiledFile = $this->getCompiledFileCache($templateName);
        return $this->CacheExpiredInt($compiledFile, $cacheDuration);
    }

    /**
     * This method is used by cacheExpired() and cachePageExpired()
     *
     * @param $compiledFile
     * @param $cacheDuration
     * @return bool|mixed
     */
    private function cacheExpiredInt($compiledFile, $cacheDuration)
    {
        if (isset($this->cacheExpired[$compiledFile])) {
            // if the information is already in the array then returns it.
            return $this->cacheExpired[$compiledFile];
        }
        $date = @filemtime($compiledFile);
        if ($date) {
            if ($date + $cacheDuration < time()) {
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

    public function cacheStart($id = '', $cacheDuration = 86400)
    {
        $this->curCacheId = ($id == '') ? ($this->curCacheId + 1) : $id;
        $this->curCacheDuration = $cacheDuration;
        $this->curCachePosition = strlen(ob_get_contents());
        if ($this->cachePageRunning) {
            $compiledFile = $this->getCompiledFileCache($this->fileName);
        } else {
            $compiledFile = $this->getCompiledFile() . '_cache' . $this->curCacheId;
        }
        
        if ($this->cacheExpired('', $id, $cacheDuration)) {
            $this->cacheRunning = false;
        } else {
            $this->cacheRunning = true;
            $content = $this->getFile($compiledFile);
            echo $content;
        }
    }

    public function cacheEnd()
    {
        if (!$this->cacheRunning) {
            $txt = substr(ob_get_contents(), $this->curCachePosition);
            if ($this->cachePageRunning) {
                $compiledFile = $this->getCompiledFileCache($this->fileName);
            } else {
                $compiledFile = $this->getCompiledFile() . '_cache' . $this->curCacheId;
            }
            file_put_contents($compiledFile, $txt);
        }
        $this->cacheRunning = false;
    }
}
