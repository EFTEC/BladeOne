<?php /** @noinspection UnknownInspectionInspection */



/** @noinspection TypeUnsafeComparisonInspection */





namespace eftec\bladeone;

use Exception;
use JsonException;
use function fclose;
use function file_put_contents;
use function filemtime;
use function filesize;
use function fopen;
use function fwrite;
use function is_array;
use function is_object;
use function ob_get_contents;
use function print_r;
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
 * @version  3.43 2024-03-02
 * @link     https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */
trait BladeOneCache
{
    protected int $curCacheId = 0;
    protected int $curCacheDuration = 0;
    protected int $curCachePosition = 0;
    protected bool $cacheRunning = false;
    protected bool $cachePageRunning = false;
    /** @var string|null where the log file will be stored */
    protected ?string $cacheLog;
    /**
     * @var array avoids comparing the file different times. It also avoids race conditions.
     */
    private array $cacheExpired = [];
    /**
     * @var string=['get','post','getpost','request',null][$i]
     */
    private string $cacheStrategy;
    /** @var array|null  */
    private ?array $cacheStrategyIndex;

    /**
     * @return null|string $cacheStrategy=['get','post','getpost','request',null][$i]
     */
    public function getCacheStrategy(): ?string
    {
        return $this->cacheStrategy;
    }

    /**
     * It sets the cache log. If not cache log then it does not generate a log file<br>
     * The cache log stores each time a template is created or expired.<br>
     *
     * @param string $file
     */
    public function setCacheLog($file): void
    {
        $this->cacheLog=$file;
    }

    /**
     * @throws JsonException
     */
    public function writeCacheLog($txt, $nivel): void
    {
        if (!$this->cacheLog) {
            return; // if there is not a file assigned then it skips saving.
        }
        $fz = @filesize($this->cacheLog);
        if (is_object($txt) || is_array($txt)) {
            $txt = print_r($txt, true);
        }
        // Rewrite file if more than 100000 bytes
        $mode=($fz > 100000) ? 'w':'a';
        $fp = fopen($this->cacheLog, $mode);
        if ($fp === false) {
            return;
        }
        switch ($nivel) {
            case 1:
                $txtNivel='expired';
                break;
            case 2:
                $txtNivel='new';
                break;
            default:
                $txtNivel='other';
        }
        $txtarg= json_encode($this->cacheUniqueGUID(false), JSON_THROW_ON_ERROR);
        fwrite($fp, date('c') . "\t$txt\t$txtNivel\t$txtarg\n");
        fclose($fp);
    }

    /**
     * It sets the strategy of the cache page.
     *
     * @param null|string $cacheStrategy =['get','post','getpost','request',null][$i]
     * @param array|null $index if null then it reads all indexes. If not, it reads an indexes.
     */
    public function setCacheStrategy($cacheStrategy, $index = null): void
    {
        $this->cacheStrategy = $cacheStrategy;
        $this->cacheStrategyIndex = $index;
    }

    /**
     * It obtains a unique GUID based in:<br>
     * **get**= parameters from the url<br>
     * **post**= parameters sends via post<br>
     * **getpost** = a mix between get and post<br>
     * **request** = get, post and cookies (including sessions)<br>
     * MD5 could generate colisions (2^64 = 18,446,744,073,709,551,616) but the end hash is the sum of the hash of
     * the page + this GUID.
     *
     * @param bool $serialize if true then it serializes using md5
     * @return string|null
     */
    private function cacheUniqueGUID($serialize = true): ?string
    {
        switch ($this->cacheStrategy) {
            case 'get':
                $r = $_GET;
                break;
            case 'post':
                $r = $_POST;
                break;
            case 'getpost':
                $arr = array_merge($_GET, $_POST);
                $r = $arr;
                break;
            case 'request':
                $r = $_REQUEST;
                break;
            default:
                $r = null;
        }
        if ($this->cacheStrategyIndex === null || !is_array($r)) {
            $r= serialize($r);
        } else {
            $copy=[];
            foreach ($r as $key => $item) {
                if (in_array($key, $this->cacheStrategyIndex, true)) {
                    $copy[$key]=$item;
                }
            }
            $r=serialize($copy);
        }
        return $serialize===true ? md5($r): $r;
    }

    public function compileCache($expression): string
    {
        // get id of template
        // if the file exists then
        //     compare date.
        //     if the date is too old then re-save.
        // else
        // save for the first time.

        return $this->phpTag . "echo \$this->cacheStart$expression; if(!\$this->cacheRunning) { ?>";
    }

    public function compileEndCache($expression): string
    {
        return $this->phpTag . "} // if cacheRunning\necho \$this->cacheEnd$expression; ?>";
    }

    /**
     * It gets the filename of the compiled file (cached). If cache is not enabled, then it
     * returns the regular file.
     *
     * @param string $view
     * @return string The full filename
     */
    private function getCompiledFileCache($view): string
    {
        $id = $this->cacheUniqueGUID();
        if ($id !== null) {
            return str_replace($this->compileExtension, '_cache' . $id
                . $this->compileExtension, $this->getCompiledFile($view));
        }
        return $this->getCompiledFile($view);
    }

    /**
     * run the blade engine. It returns the result of the code.
     *
     * @param string $view      The name of the cache. Ex: "folder.folder.view" ("/folder/folder/view.blade")
     * @param array  $variables An associative arrays with the values to display.
     * @param int    $ttl       time to live (in second).
     * @return string
     * @throws Exception
     */
    public function runCache($view, $variables = [], $ttl = 86400): string
    {
        $this->cachePageRunning = true;
        $cacheStatus=$this->cachePageExpired($view, $ttl);
        if ($cacheStatus!==0) {
            $this->writeCacheLog($view, $cacheStatus);
            $this->cacheStart('_page_', $ttl);
            $content = $this->run($view, $variables); // if no cache, then it runs normally.
            $this->fileName = $view; // sometimes the filename is replaced (@include), so we restore it
            $this->cacheEnd($content); // and it stores as a cache paged.
        } else {
            $content = $this->getFile($this->getCompiledFileCache($view));
        }
        $this->cachePageRunning = false;
        return $content;
    }

    /**
     * Returns true if the block cache expired (or doesn't exist), otherwise false.
     *
     * @param string $templateName name of the template to use (such hello for template hello.blade.php)
     * @param string $id (id of cache, optional, if not id then it adds automatically a number)
     * @param int $cacheDuration (duration of the cache in seconds)
     * @return int 0=cache exists, 1= cache expired, 2=not exists, string= the cache file (if any)
     */
    public function cacheExpired($templateName, $id, $cacheDuration): int
    {
        if ($this->getMode() & 1) {
            return 2; // forced mode, hence it always expires. (fast mode is ignored).
        }
        $compiledFile = $this->getCompiledFile($templateName) . '_cache' . $id;
        return $this->cacheExpiredInt($compiledFile, $cacheDuration);
    }

    /**
     * It returns true if the whole page expired.
     *
     * @param string $templateName
     * @param int $cacheDuration is seconds.
     * @return int 0=cache exists, 1= cache expired, 2=not exists, string= the cache content (if any)
     */
    public function cachePageExpired($templateName, $cacheDuration): int
    {
        if ($this->getMode() & 1) {
            return 2; // forced mode, hence it always expires. (fast mode is ignored).
        }
        $compiledFile = $this->getCompiledFileCache($templateName);
        return $this->CacheExpiredInt($compiledFile, $cacheDuration);
    }

    /**
     * This method is used by cacheExpired() and cachePageExpired()
     *
     * @param string $compiledFile
     * @param int $cacheDuration is seconds.
     * @return int|mixed 0=cache exists, 1= cache expired, 2=not exists, string= the cache content (if any)
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
                $this->cacheExpired[$compiledFile] = 1;
                return 2; // time-out.
            }
        } else {
            $this->cacheExpired[$compiledFile] = 2;
            return 1; // no file
        }
        $this->cacheExpired[$compiledFile] = 0;
        return 0; // cache active.
    }

    public function cacheStart($id = '', $cacheDuration = 86400): void
    {
        $this->curCacheId = ($id == '') ? ($this->curCacheId + 1) : $id;
        $this->curCacheDuration = $cacheDuration;
        $this->curCachePosition = strlen(ob_get_contents());
        if ($this->cachePageRunning) {
            $compiledFile = $this->getCompiledFileCache($this->fileName);
        } else {
            $compiledFile = $this->getCompiledFile() . '_cache' . $this->curCacheId;
        }

        if ($this->cacheExpired('', $id, $cacheDuration) !==0) {
            $this->cacheRunning = false;
        } else {
            $this->cacheRunning = true;
            $content = $this->getFile($compiledFile);
            echo $content;
        }
    }

    public function cacheEnd($txt = null): void
    {
        if (!$this->cacheRunning) {
            $txt = $txt ?? substr(ob_get_contents(), $this->curCachePosition);
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
