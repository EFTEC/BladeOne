<?php

namespace eftec\bladeone;

use ArrayAccess;
use Closure;
use Countable;
use Exception;
use InvalidArgumentException;
use function count;
use function array_merge;
use function is_array;
use ParseError;

/**
 * Class BladeOne
 * @package  BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 * @version 3.27 2019-05-25
 * @link https://github.com/EFTEC/BladeOne
 */
class BladeOne
{    //<editor-fold desc="fields">

    /** @var array All of the registered extensions. */
    protected $extensions = [];
    
    /** @var array All of the finished, captured sections. */
    protected $sections = [];
    /** @var string The template currently being compiled. For example "folder.template" */
    protected $fileName;
    /** @var string File extension for the template files. */
    protected $fileExtension = '.blade.php';
    /** @var array The stack of in-progress sections. */
    protected $sectionStack = [];
    /** @var array The stack of in-progress loops. */
    protected $loopsStack = [];
    /** @var array Dictionary of variables */
    protected $variables = [];
    /** @var null Dictionary of global variables */
    protected $variablesGlobal=[];
    /** @var array All of the available compiler functions. */
    protected $compilers = [
        'Extensions',
        'Statements',
        'Comments',
        'Echos',
    ];
    /** @var array The stack of in-progress push sections. */
    protected $pushStack = [];
    /** @var array All of the finished, captured push sections. */
    protected $pushes = [];
    /** @var int The number of active rendering operations. */
    protected $renderCount = 0;
    /** @var string|string[] Get the template path for the compiled views. */
    protected $templatePath;
    /** @var string Get the compiled path for the compiled views. If null then it uses the default path */
    protected $compiledPath;
    /** @var string the extension of the compiled file. */
    protected $compileExtension='.bladec';
    /** @var array Custom "directive" dictionary. Those directives run at compile time. */
    protected $customDirectives = [];
    /** @var bool[] Custom directive dictinary. Those directives run at runtime. */
    protected $customDirectivesRT = [];
    /** @var callable Function used for resolving injected classes. */
    protected $injectResolver;
    /** @var array Used for conditional if. */
    protected $conditions=[];
    /** @var int Unique counter. It's usued for extends */
    protected $uidCounter=0;
    /** @var string The main url of the system. Don't use $SERVER['HTTP_HOST'] or $SERVER['SERVER_NAME'] unless the value is sanitized is protected */
    protected $baseUrl = '.';
    /** @var string it is a relative path calculated betweeen baseUrl and the current url. Example ../../ */
    protected $relativePath="";
    /** @var string[] Dictionary of assets */
    protected $assetDict;
	/** @var bool if true then it removes tabs and unneeding spaces  */
	protected $optimize=true;
	/** @var bool if false, then the template is not compiled (but executed on memory).   */
	protected $isCompiled=true;	
    /** @var bool  */
    protected $isRunFast = false;
    /** @var array Array of opening and closing tags for raw echos. */
    protected $rawTags = ['{!!', '!!}'];
    /** @var array Array of opening and closing tags for regular echos. */
    protected $contentTags = ['{{', '}}'];
    /** @var array Array of opening and closing tags for escaped echos. */
    protected $escapedTags = ['{{{', '}}}'];
    /** @var string The "regular" / legacy echo string format. */
    protected $echoFormat = 'static::e(%s)';
    /** @var array Lines that will be added at the footer of the template */
    protected $footer = [];
    /** @var string Placeholder to temporary mark the position of verbatim blocks. */
    protected $verbatimPlaceholder = '$__verbatim__$';
    /** @var array Array to temporary store the verbatim blocks found in the template. */
    protected $verbatimBlocks = [];
    /** @var int Counter to keep track of nested forelse statements. */
    protected $forelseCounter = 0;
    /** @var string PHP tag. You could use <?php or <? (if shorttag is active in php.ini) */
    public $phpTag = '<?php ';
    /** @var array The components being rendered. */
    protected $componentStack = [];
    /** @var array The original data passed to the component. */
    protected $componentData = [];
    /** @var array The slot contents for the component. */
    protected $slots = [];
    /** @var array The names of the slots being rendered. */
    protected $slotStack = [];
    /** @var string tag unique */
    protected $PARENTKEY = '@parentXYZABC';
    /** @var string $currentUser Current user. Example: john */
    public $currentUser = null;
    /** @var string $currentRole Current role. Example: admin */
    public $currentRole = null;
    /** @var string[] $currentPermission Current permission. Example ['edit','add'] */
    public $currentPermission = [];
    /** @var int Indicates the number of open switches */
    private $switchCount = 0;
    /** @var bool Indicates if the switch is recently open */
    private $firstCaseInSwitch = true;
    /** @var callable callback of validation. It is used for @can,@cannot */
    public $authCallBack;
    /** @var callable callback of validation. It is used for @canany */
    public $authAnyCallBack;
    /** @var string security token */
    public $csrf_token="";
    /**
     * Indicates the compile mode.
     * if the constant BLADEONE_MODE is define, then it is used instead of this field.
     * @var int MODE_*
     */
    protected $mode;
    /** @var int BladeOne reads if the compiled file has changed. If has changed,then the file is replaced. */
    const MODE_AUTO=0;
    /** @var int Then compiled file is always replaced. It's slow and it's useful for development. */
    const MODE_SLOW=1;
    /** @var int The compiled file is never replaced. It's fast and it's useful for production. */
    const MODE_FAST=2;
    /** @var int DEBUG MODE, the file is always compiled and the filename is identifiable. */
    const MODE_DEBUG=5;

    //</editor-fold>

    //<editor-fold desc="constructor">
    /**
     * Bob the constructor.
     * The folder at $compiledPath is created in case it doesn't exist.
     * @param string|array $templatePath .If null then it uses (caller_folder)/views
     * @param string $compiledPath .If null then it uses (caller_folder)/compiles
     * @param int $mode=[BladeOne::MODE_AUTO,BladeOne::MODE_DEBUG,BladeOne::MODE_FAST,BladeOne::MODE_SLOW][$i]
     */
    public function __construct($templatePath=null, $compiledPath=null,$mode=0)
    {
        if ($templatePath===null) $templatePath=getcwd(). '/views';
        if ($compiledPath===null) $compiledPath=getcwd() . '/compiles';
        $this->templatePath = $templatePath;
        $this->compiledPath = $compiledPath;
        $this->setMode($mode);
        $this->authCallBack=function($action=null, $subject=null) {
            return in_array($action,$this->currentPermission);
        };
        $this->authAnyCallBack=function($array=[]) {
            foreach($array as $permission) {
                if (in_array($permission,$this->currentPermission)) return true;
            }
            return false;
        };

        if (!file_exists($this->compiledPath)) {
            $ok = @mkdir($this->compiledPath, 0777, true);
            if ($ok===false) {
                $this->showError("Constructing", "Unable to create the compile folder [{$this->compiledPath}]. Check the permissions of it's parent folder.", true);
            }
        }
    }
    //</editor-fold>
    //<editor-fold desc="common">
    /**
     * Macro of function run
     * @param $view
     * @param array $variables
     * @return string
     * @throws Exception
     */
    public function runChild($view, $variables = [])
    {
        if (is_array($variables)) {
			$newVariables = array_merge($this->variables, $variables);
        } else {
            $this->showError("run/include", "Include/run variables should be defined as array ['idx'=>'value']", true);
            return "";
        }
        return $this->runInternal($view, $newVariables, false, false, $this->isRunFast);
    }

	/**
	 * @param string $view example "folder.template"
	 * @param string|null $alias example "mynewop". If null then it uses the name of the template.
	 */
	public function addInclude($view, $alias = null) {
		if (!isset($alias)) {
			$alias=explode('.', $view);
			$alias=end($alias);
		}
		$this->directive($alias, function ($expression) use ($view) {
			$expression = $this->stripParentheses($expression) ?: '[]';
			return "<?php echo \$this->runChild('{$view}', {$expression}); ?>";
		});
	}

    /**
     * It sets the base url and it also calculates the relative path.<br>
     * The base url is calculated to determine the relativity of the resources.<br>
     * The trailing slash is removed automatically if it's present.
     * @param string $baseUrl Example http://www.web.com/folder  https://www.web.com/folder/anotherfolder
     */
    public function setBaseUrl($baseUrl) {
        $this->baseUrl=rtrim($baseUrl,'/'); // base with the url trimmed
        $currentUrl=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $base=str_replace(['https://','http://'],'',$this->baseUrl);
        if (strpos($currentUrl,$base)===0) {
            $part=str_replace($base,'',$currentUrl);
            $numf=substr_count($part,'/')-1;
            $numf=($numf>10)?10:$numf; // avoid overflow
            $this->relativePath=($numf<0)?"":str_repeat('../',$numf);
        } else {
            $this->relativePath='';
        }
    }

	/**
	 * If true then it optimizes the result (it removes tab and extra spaces).
	 * @param bool $bool
	 * @return BladeOne
	 */
    public function setOptimize($bool=false) {
    	$this->optimize=$bool;
    	return $this;
    }

	/**
	 * If false then the file is not compiled and it is executed directly from the memory.<br>
	 * By default the value is true<br>
	 * It also sets the mode to MODE_SLOW
	 * @param bool $bool
	 * @return BladeOne
	 * @see \eftec\bladeone\BladeOne::setMode
	 */
	public function setIsCompiled($bool=false) {
		
		$this->isCompiled=$bool;
		if (!$bool) {
			$this->setMode(self::MODE_SLOW);
		}
		return $this;
	}

    /**
     * Returns the current base url
     * @return string
     */
    public function getBaseUrl() {
        return $this->baseUrl;
    }

    /**
     * It sets the template and compile path (without trailing slash).
     * <p>Example:setPath("somefolder","otherfolder");
     * @param null|string|string[] $templatePath If null then it uses the current path /views folder
     * @param null|string $compiledPath If null then it uses the current path /views folder
     */
    public function setPath($templatePath,$compiledPath) {
	    if ($templatePath===null) $templatePath=getcwd(). '/views';
	    if ($compiledPath===null) $compiledPath=getcwd() . '/compiles';
	    $this->templatePath=$templatePath;
        $this->compiledPath=$compiledPath;
    }

    /**
     * Authentication. Sets with a user,role and permission
     * @param string $user
     * @param null $role
     * @param array $permission
     */
    public function setAuth($user = '', $role = null,$permission=[])
    {
        $this->currentUser = $user;
        $this->currentRole = $role;
        $this->currentPermission = $permission;
    }

    /**
     * It sets the callback function for authentication. It is used by @can and @cannot
     * @param callable $fn
     */
    public function setCanFunction(callable $fn) {
        $this->authCallBack=$fn;
    }
    /**
     * It sets the callback function for authentication. It is used by @canany
     * @param callable $fn
     */
    public function setAnyFunction(callable $fn) {
        $this->authAnyCallBack=$fn;
    }

    /**
     * Get the mode of the engine.See BladeOne::MODE_* constants
     * @return int=[self::MODE_AUTO,self::MODE_DEBUG,self::MODE_FAST,self::MODE_SLOW][$i]
     */
    public function getMode()
    {
        if (defined('BLADEONE_MODE')) {
            $this->mode = BLADEONE_MODE;
        }
        return $this->mode;
    }

    /**
     * Set the compile mode
     * @param $mode int=[self::MODE_AUTO,self::MODE_DEBUG,self::MODE_FAST,self::MODE_SLOW][$i]
     * @return void
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * run the blade engine. It returns the result of the code.
     * @param $view
     * @param array $variables
     * @return string
     * @throws Exception
     */
    public function run($view, $variables = [])
    {
        $mode = $this->getMode();
        $forced = $mode & 1; // mode=1 forced:it recompiles no matter if the compiled file exists or not.
        $runFast = $mode & 2; // mode=2 runfast: the code is not compiled neither checked and it runs directly the compiled
        if ($mode == 3) {
            $this->showError("run", "we can't force and run fast at the same time", true);
        }
        return $this->runInternal($view, $variables, $forced, true, $runFast);
    }

    /**
     * run the blade engine. It returns the result of the code.
     * @param string HTML to parse
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function runString($string, $data)
    {
        $php = $this->compileString($string);

        $obLevel = ob_get_level();
        ob_start();
        extract($data, EXTR_SKIP);

        $previousError = error_get_last();

        try {
            @eval('?'.'>'.$php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw $e;
        } catch (ParseError $e) { // PHP 7
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw new Exception($e->getMessage(),$e->getCode());
        }

        $lastError = error_get_last(); // PHP 5.6
        if ($previousError != $lastError && $lastError["type"] == E_PARSE) {
            while (ob_get_level() > $obLevel) ob_end_clean();
            throw new Exception($lastError["message"], $lastError["type"]);
        }

        return ob_get_clean();
    }


    /**
     * run the blade engine. It returns the result of the code.
     * @param $view
     * @param array $variables
     * @param bool $forced if true then it recompiles no matter if the compiled file exists or not.
     * @param bool $isParent
     * @param bool $runFast if true then the code is not compiled neither checked and it runs directly the compiled version.
     * @return string
     * @throws Exception
     */
    private function runInternal($view, $variables = [], $forced = false, $isParent = true, $runFast = false)
    {
        if ($isParent) {
	        if (count($this->variablesGlobal)>0) {
		        $this->variables  = array_merge($variables,$this->variablesGlobal);
		        $variables=$this->variables;
	        } else {
		        $this->variables = $variables;	
	        }            
        }
        if (!$runFast) {
            // a) if the compile is forced then we compile the original file, then save the file.
            // b) if the compile is not forced then we read the datetime of both file and we compared.
            // c) in both cases, if the compiled doesn't exist then we compile.
            if ($view) {
                $this->fileName = $view;
            }
            $result=$this->compile($view, $forced);
            if (!$this->isCompiled) {
            	return $this->evaluateText($result,$variables);
            }
        } else {
            // running fast, we don't compile neither we check or read the original template.
            if ($view) {
                $this->fileName = $view;
            }
        }
        $this->isRunFast = $runFast;
        
        return $this->evaluatePath($this->getCompiledFile(), $variables);
    }

    /**
     * Compile the view at the given path.
     * @param string $templateName The name of the template. Example folder.template
     * @param bool $forced If the compilation will be forced (always compile) or not.
     * @return boolean|string True if the operation was correct, or false (if not exception) 
     *          if it fails. It returns a string (the content compiled) if isCompiled=false
     * @throws Exception
     */
    public function compile($templateName = null, $forced = false)
    {
	    $compiled = $this->getCompiledFile($templateName);
	    $template = $this->getTemplateFile($templateName);
    	if (!$this->isCompiled) {
		    $contents = $this->compileString($this->getFile($template));
		    return $contents;
	    }
        if ($forced || $this->isExpired($templateName)) {
            // compile the original file
            $contents = $this->compileString($this->getFile($template));
            $dir = dirname($compiled);
            if (!file_exists($dir)) {
                $ok = @mkdir($dir, 0777, true);
                if ($ok===false) {
                    $this->showError("Compiling", "Unable to create the compile folder [{$dir}]. Check the permissions of it's parent folder.", true);
                    return false;
                }
            }
            if ($this->optimize) {
                // removes space and tabs and replaces by a single space
                $contents = preg_replace('/^ {2,}/m', ' ', $contents);
                $contents = preg_replace('/^\t{2,}/m', ' ', $contents);
            }
            $ok = @file_put_contents($compiled, $contents);
            if ($ok===false) {
                $this->showError("Compiling", "Unable to save the file [{$compiled}]. Check the compile folder is defined and has the right permission");
                return false;
            }
        }
        return true;
    }
    //</editor-fold>
    //<editor-fold desc="compile">
    protected function compileSwitch($expression)
    {
        $this->switchCount++;
        $this->firstCaseInSwitch = true;
        return $this->phpTag."switch $expression {";
    }
    protected function compileDump($expression)
    {
        return $this->phpTag." echo \$this->dump{$expression};?>";
    }

    protected  function compileRelative($expression) {
        return $this->phpTag." echo \$this->relative{$expression};?>";
    }

    /**
     * it calculates the relative path of a web.<br>
     * This function uses the current url and the baseurl
     *
     * @param string $relativeWeb . Example img/images.jpg
     * @return string  Example ../../img/images.jpg
     */
    public function relative($relativeWeb) {
        if (isset($this->assetDict[$relativeWeb])) return $this->assetDict[$relativeWeb];
        // relativepath is calculated when
        return $this->relativePath.$relativeWeb;
    }

    /**
     * It add an alias to the link of the resources.<br>
     * addAssetDict('name','url/res.jpg')<br>
     * addAssetDict(['name'=>'url/res.jpg','name2'=>'url/res2.jpg');
     * @param string|array $name example 'css/style.css', you could also add an array
     * @param string $url example https://www.web.com/style.css'
     */
    public function addAssetDict($name,$url="") {
        if (is_array($name)) {
            $this->assetDict=array_merge($this->assetDict,$name);
        } else {
            $this->assetDict[$name]=$url;
        }
    }

    protected function compileMethod($expression)
    {
        $v=$this->stripParentheses($expression);
        return $this->phpTag." echo '<input type=\"hidden\" name=\"_method\" value=\"$v\"/>';?>";
    }
    protected function compilecsrf()
    {
        return $this->phpTag." echo '<input type=\"hidden\" name=\"_token\" value=\"".$this->csrf_token."\"/>';?>";
    }

    protected function compileDd($expression)
    {
        return $this->phpTag." echo '<pre>'; var_dump$expression; echo '</pre>';?>";
    }

    /**
     * Execute the case tag.
     * @param $expression
     * @return string
     */
    protected function compileCase($expression)
    {
        if ($this->firstCaseInSwitch) {
            $this->firstCaseInSwitch = false;
            return "case ".$expression.": ?>";
        }
        return $this->phpTag."case $expression: ?>";
    }

    /**
     * Compile the while statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileWhile($expression)
    {
        return $this->phpTag."while{$expression}: ?>";
    }

    /**
     * default tag used for switch/case
     * @return string
     */
    protected function compileDefault()
    {
        if ($this->firstCaseInSwitch) {
            return $this->showError("@default", "@switch without any @case", true);
        }
        return $this->phpTag."default: ?>";
    }

    /*
     * endswitch tag
     */
    protected function compileEndSwitch()
    {
        $this->switchCount = $this->switchCount - 1;
        if ($this->switchCount < 0) {
            return $this->showError("@endswitch", "Missing @switch", true);
        }
        return $this->phpTag."} // end switch ?>";
    }

    /**
     * Compile while statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileInject($expression)
    {
        $ex = $this->stripParentheses($expression);
        $p0 = strpos($ex, ',');
        if ($p0 == false) {
            $var = $this->stripQuotes($ex);
            $namespace = '';
        } else {
            $var = $this->stripQuotes(substr($ex, 0, $p0));
            $namespace = $this->stripQuotes(substr($ex, $p0 + 1));
        }
        return $this->phpTag."\$$var = \$this->injectClass('$namespace', '$var'); ?>";
    }

    /**
     * Compile the given Blade template contents.
     * @param  string $value
     * @return string
     */
    protected function compileString($value)
    {
        $result = '';
        if (strpos($value, '@verbatim') !== false) {
            $value = $this->storeVerbatimBlocks($value);
        }
        $this->footer = [];
        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }
        if (!empty($this->verbatimBlocks)) {
            $result = $this->restoreVerbatimBlocks($result);
        }
        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($this->footer) > 0) {
            $result = ltrim($result, PHP_EOL)
                .PHP_EOL.implode(PHP_EOL, array_reverse($this->footer));
        }
        return $result;
    }

    /**
     * Execute the user defined extensions.
     * @param  string $value
     * @return string
     */
    protected function compileExtensions($value)
    {
        foreach ($this->extensions as $compiler) {
            echo "<hr><hr>extensions $compiler<hr><hr>";
            $value = call_user_func($compiler, $value, $this);
        }
        return $value;
    }

    /**
     * Compile Blade comments into valid PHP.
     * @param  string $value
     * @return string
     */
    protected function compileComments($value)
    {
        $pattern = sprintf('/%s--(.*?)--%s/s', $this->contentTags[0], $this->contentTags[1]);
        return preg_replace($pattern, $this->phpTag.'/*$1*/ ?>', $value);
    }

    /**
     * Compile Blade echos into valid PHP.
     * @param  string $value
     * @return string
     */
    protected function compileEchos($value)
    {
        foreach ($this->getEchoMethods() as $method => $length) {
            $value = $this->$method($value);
        }
        return $value;
    }

    /**
     * Compile Blade statements that start with "@".
     * @param  string $value
     * @return mixed
     */
    protected function compileStatements($value)
    {
        $callback = function ($match) {
            if (static::contains($match[1], '@')) {
                $match[0] = isset($match[3]) ? $match[1].$match[3] : $match[1];
            } elseif (isset($this->customDirectivesRT[$match[1]])) {
                if ($this->customDirectivesRT[$match[1]]==true) {
                    $match[0]=$this->compileStatementCustom($match);
                } else {
                    $match[0] = call_user_func($this->customDirectives[$match[1]], static::get($match, 3));
                }
            } elseif (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
                $match[0] = $this->$method(static::get($match, 3));
            } else {
                return $match[0];
                //$this->showError("@compile", "Operation not defined:@".$match[1], true);
            }
            return isset($match[3]) ? $match[0] : $match[0].$match[2];
        };
        return preg_replace_callback('/\B@(@?\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $value);
    }

    /**
     * For compile custom directive at runtime.
     * @param $match
     * @return string
     */
    protected function compileStatementCustom($match)
    {
        $v=$this->stripParentheses(static::get($match, 3));
        $v=($v=='')?'':','.$v;
        return $this->phpTag.'call_user_func($this->customDirectives[\''.$match[1].'\']'.$v.'); ?>';
    }

    /**
     * Compile the "raw" echo statements.
     * @param  string $value
     * @return string
     */
    protected function compileRawEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->rawTags[0], $this->rawTags[1]);
        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];
            return $matches[1] ? substr($matches[0], 1) : $this->phpTag.'echo '.$this->compileEchoDefaults($matches[2]).'; ?>'.$whitespace;
        };
        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the "regular" echo statements.
     * @param  string $value
     * @return string
     */
    protected function compileRegularEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->contentTags[0], $this->contentTags[1]);
        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];
            $wrapped = sprintf($this->echoFormat, $this->compileEchoDefaults($matches[2]));
            return $matches[1] ? substr($matches[0], 1) : $this->phpTag.'echo '.$wrapped.'; ?>'.$whitespace;
        };
        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the escaped echo statements.
     * @param  string $value
     * @return string
     */
    protected function compileEscapedEchos($value)
    {
        $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->escapedTags[0], $this->escapedTags[1]);
        $callback = function ($matches) {
            $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];
            return $matches[1] ? $matches[0] : $this->phpTag.'echo static::e('.$this->compileEchoDefaults($matches[2]).'); ?>'.$whitespace;
        };
        return preg_replace_callback($pattern, $callback, $value);
    }

    /**
     * Compile the default values for the echo statement.
     * @param  string $value
     * @return string
     */
    protected function compileEchoDefaults($value)
    {
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $value);
    }

    /**
     * Compile the each statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileEach($expression)
    {
        return $this->phpTag."echo \$this->renderEach{$expression}; ?>";
    }

    protected function compileSet($expression)
    {
        //$segments = explode('=', preg_replace("/[\(\)\\\"\']/", '', $expression));
        $segments = explode('=', preg_replace("/[\(\)\\\']/", '', $expression));
        $value = (count($segments) >= 2) ? ' =@'.$segments[1] : '++';
        return $this->phpTag.trim($segments[0]).$value."; ?>";
    }

    /**
     * Compile the yield statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileYield($expression)
    {
        return $this->phpTag."echo \$this->yieldContent{$expression}; ?>";
    }

    /**
     * Compile the show statements into valid PHP.
     * @return string
     */
    protected function compileShow()
    {
        return $this->phpTag.'echo $this->yieldSection(); ?>';
    }

    /**
     * Compile the section statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileSection($expression)
    {
        return $this->phpTag."\$this->startSection{$expression}; ?>";
    }

    /**
     * Compile the append statements into valid PHP.
     * @return string
     */
    protected function compileAppend()
    {
        return $this->phpTag.'$this->appendSection(); ?>';
    }

    /**
     * Compile the auth statements into valid PHP.
     * @param string $expression
     * @return string
     */
    protected function compileAuth($expression = '')
    {
        $role = $this->stripParentheses($expression);
        if ($role=='') {
            return $this->phpTag."if(isset(\$this->currentUser)): ?>";
        } else {
            return $this->phpTag."if(isset(\$this->currentUser) && \$this->currentRole=={$role}): ?>";
        }
    }
    /**
     * Compile the elseauth statements into valid PHP.
     * @param string $expression
     * @return string
     */
    protected function compileElseAuth($expression='')
    {
        $role = $this->stripParentheses($expression);
        if ($role=='') {
            return $this->phpTag."else: ?>";
        } else {
            return $this->phpTag."elseif(isset(\$this->currentUser) && \$this->currentRole=={$role}): ?>";
        }
    }
    /**
     * Compile the end-auth statements into valid PHP.
     * @return string
     */
    protected function compileEndAuth()
    {
        return $this->phpTag.'endif; ?>';
    }

    protected function compileCan($expression) {
        $v=$this->stripParentheses($expression);
        return $this->phpTag.'if (call_user_func($this->authCallBack,'.$v.')): ?>';
    }
    /**
     * Compile the else statements into valid PHP.
     * @param string $expression
     * @return string
     */
    protected function compileElseCan($expression='')
    {
        $v=$this->stripParentheses($expression);
        if ($v) {
            return $this->phpTag.'elseif (call_user_func($this->authCallBack,'.$v.')): ?>';
        } else {
            return $this->phpTag.'else: ?>';
        }
    }


    protected function compileCannot($expression) {
        $v=$this->stripParentheses($expression);
        return $this->phpTag.'if (!call_user_func($this->authCallBack,'.$v.')): ?>';
    }
    /**
     * Compile the elsecannot statements into valid PHP.
     * @param string $expression
     * @return string
     */
    protected function compileElseCannot($expression='')
    {
        $v=$this->stripParentheses($expression);
        if ($v) {
            return $this->phpTag.'elseif (!call_user_func($this->authCallBack,'.$v.')): ?>';
        } else {
            return $this->phpTag.'else: ?>';
        }
    }
    /**
     * canany(['edit','write'])
     * @param $expression
     * @return string
     */
    protected function compileCanAny($expression) {
        $role=$this->stripParentheses($expression);
        return $this->phpTag.'if (call_user_func($this->authAnyCallBack,'.$role.')): ?>';
    }
    /**
     * Compile the else statements into valid PHP.
     * @param $expression
     * @return string
     */
    protected function compileElseCanAny($expression)
    {
        $role=$this->stripParentheses($expression);
        if ($role =='') {
            return $this->phpTag."else: ?>";
        } else {
            return $this->phpTag.'elseif (call_user_func($this->authAnyCallBack,'.$role.')): ?>';
        }
    }


    /**
     * Compile the guest statements into valid PHP.
     * @param null $expression
     * @return string
     */
    protected function compileGuest($expression = null)
    {
        if ($expression === null) {
            return $this->phpTag."if(!isset(\$this->currentUser)): ?>";
        } else {
            $role = $this->stripParentheses($expression);
            if ($role=="") {
                return $this->phpTag."if(!isset(\$this->currentUser)): ?>";
            } else {
                return $this->phpTag."if(!isset(\$this->currentUser) || \$this->currentRole!={$role}): ?>";
            }
        }
    }
    /**
     * Compile the else statements into valid PHP.
     * @param $expression
     * @return string
     */
    protected function compileElseGuest($expression)
    {
        $role=$this->stripParentheses($expression);
        if ($role =='') {
            return $this->phpTag."else: ?>";
        } else {
            return $this->phpTag."elseif(!isset(\$this->currentUser) || \$this->currentRole!={$role}): ?>";
        }
    }



    /**
     * /**
     * Compile the end-auth statements into valid PHP.
     * @return string
     */
    protected function compileEndGuest()
    {
        return $this->phpTag.'endif; ?>';
    }


    /**
     * Compile the end-section statements into valid PHP.
     * @return string
     */
    protected function compileEndsection()
    {
        return $this->phpTag.'$this->stopSection(); ?>';
    }

    /**
     * Compile the stop statements into valid PHP.
     * @return string
     */
    protected function compileStop()
    {
        return $this->phpTag.'$this->stopSection(); ?>';
    }

    /**
     * Compile the overwrite statements into valid PHP.
     * @return string
     */
    protected function compileOverwrite()
    {
        return $this->phpTag.'$this->stopSection(true); ?>';
    }

    /**
     * Compile the unless statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileUnless($expression)
    {
        return $this->phpTag."if ( ! $expression): ?>";
    }

	/**
	 * Compile the User statements into valid PHP.
	 * @return string
	 */
    protected function compileUser()
    {
        return $this->phpTag ."echo '".$this->currentUser."'; ?>";
    }

    /**
     * Compile the endunless statements into valid PHP.
     * @return string
     */
    protected function compileEndunless()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the else statements into valid PHP.
     * @return string
     */
    protected function compileElse()
    {
        return $this->phpTag.'else: ?>';
    }




    /**
     * Compile the for statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileFor($expression)
    {
        return $this->phpTag."for{$expression}: ?>";
    }

    /**
     * Compile the foreach statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileForeach($expression)
    {
        preg_match('/\( *(.*) * as *([^\)]*)/', $expression, $matches);
        $iteratee = trim($matches[1]);
        $iteration = trim($matches[2]);
        $initLoop = "\$__currentLoopData = {$iteratee}; \$this->addLoop(\$__currentLoopData);";
        $iterateLoop = '$this->incrementLoopIndices(); $loop = $this->getFirstLoop();';
        return $this->phpTag."{$initLoop} foreach(\$__currentLoopData as {$iteration}): {$iterateLoop} ?>";
    }

    /**
     * Compile a split of a foreach cycle. Used for example when we want to separate limites each "n" elements.
     * @param  string $expression
     * @return string
     */
    protected function compileSplitForeach($expression)
    {
        return $this->phpTag.'echo $this::splitForeach'.$expression.'; ?>';
    }

    /**
     * Compile the break statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileBreak($expression)
    {
        return $expression ? $this->phpTag."if{$expression} break; ?>" : $this->phpTag.'break; ?>';
    }

    /**
     * Compile the continue statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileContinue($expression)
    {
        return $expression ? $this->phpTag."if{$expression} continue; ?>" : $this->phpTag.'continue; ?>';
    }

    /**
     * Compile the forelse statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileForelse($expression)
    {
        $empty = '$__empty_'.++$this->forelseCounter;
        return $this->phpTag."{$empty} = true; foreach{$expression}: {$empty} = false; ?>";
    }

    /**
     * Compile the if statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileIf($expression)
    {
        return $this->phpTag."if{$expression}: ?>";
    }

    /**
     * Compile the else-if statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileElseif($expression)
    {
        return $this->phpTag."elseif{$expression}: ?>";
    }

    /**
     * Compile the forelse statements into valid PHP.
     * @param string $expression empty if it's inside a for loop.
     * @return string
     */
    protected function compileEmpty($expression = '')
    {

        if (($expression == '')) {
            $empty = '$__empty_'.$this->forelseCounter--;
            return $this->phpTag."endforeach; if ({$empty}): ?>";
        } else {
            return $this->phpTag."if (empty{$expression}): ?>";
        }

    }

    /**
     * Compile the has section statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileHasSection($expression)
    {
        return $this->phpTag."if (! empty(trim(\$this->yieldContent{$expression}))): ?>";
    }

    /**
     * Compile the end-while statements into valid PHP.
     * @return string
     */
    protected function compileEndwhile()
    {
        return $this->phpTag.'endwhile; ?>';
    }

    /**
     * Compile the end-for statements into valid PHP.
     * @return string
     */
    protected function compileEndfor()
    {
        return $this->phpTag.'endfor; ?>';
    }

    /**
     * Compile the end-for-each statements into valid PHP.
     * @return string
     */
    protected function compileEndforeach()
    {
        return $this->phpTag.'endforeach; $this->popLoop(); $loop = $this->getFirstLoop(); ?>';
    }

    /**
     * Compile the end-can statements into valid PHP.
     * @return string
     */
    protected function compileEndcan()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the end-can statements into valid PHP.
     * @return string
     */
    protected function compileEndcanany()
    {
        return $this->phpTag.'endif; ?>';
    }


    /**
     * Compile the end-cannot statements into valid PHP.
     * @return string
     */
    protected function compileEndcannot()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the end-if statements into valid PHP.
     * @return string
     */
    protected function compileEndif()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the end-for-else statements into valid PHP.
     * @return string
     */
    protected function compileEndforelse()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the raw PHP statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compilePhp($expression)
    {
        return $expression ? $this->phpTag."{$expression}; ?>" : $this->phpTag.'';
    }

    /**
     * Compile end-php statement into valid PHP.
     * @return string
     */
    protected function compileEndphp()
    {
        return ' ?>';
    }

    /**
     * Compile the unset statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileUnset($expression)
    {
        return $this->phpTag."unset{$expression}; ?>";
    }

    /**
     * Compile the extends statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        $expression = $this->stripParentheses($expression);
        // $_shouldextend avoids to runchild if it's not evaluated.
        // For example @if(something) @extends('aaa.bb') @endif()
        // If something is false then it's not rendered at the end (footer) of the script.
        $this->uidCounter++;
        $data = $this->phpTag.'if (@$_shouldextend['.$this->uidCounter.']) { echo $this->runChild('.$expression.'); } ?>';
        $this->footer[] = $data;
        return $this->phpTag.'$_shouldextend['.$this->uidCounter.']=1; ?>';
    }

    /**
     * Execute the @parent command. This operation works in tandem with extendSection
     * @return string
     * @see extendSection
     */
    protected function compileParent()
    {
        return $this->PARENTKEY;
    }

    /**
     * Compile the include statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        $expression = $this->stripParentheses($expression);
        return $replace = $this->phpTag.'echo $this->runChild('.$expression.'); ?>';
    }

    /**
     * It loads an compiled template and paste inside the code.<br>
     * It uses more disk space but it decreases the number of includes<br>
     * @param $expression
     * @return string
     * @throws Exception
     */
    protected function compileIncludeFast($expression)
    {
        $expression = $this->stripParentheses($expression);
        $ex=$this->stripParentheses($expression);
        $exp=explode(',',$ex);
        $file=$this->stripQuotes(@$exp[0]);
        $fileC=$this->getCompiledFile($file);
        if (!@file_exists($fileC)) {
            // if the file doesn't exist then it's created
            $this->compile($file,true);
        }
        $txt=$this->getFile($fileC);

        //eval("\$x=\$this->run($expression);")."123";
        return $txt;
    }


    /**
     * Compile the include statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileIncludeIf($expression)
    {
        return $replace = $this->phpTag.'if ($this->templateExist'.$expression.') echo $this->runChild'.$expression.'; ?>';
    }

    /**
     * Compile the include statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileIncludeWhen($expression)
    {
        $expression = $this->stripParentheses($expression);
        return $replace = $this->phpTag.'echo $this->includeWhen('.$expression.'); ?>';
    }

    /**
     * Compile the includefirst statement
     * @param  string $expression
     * @return string
     */
    protected function compileIncludeFirst($expression)
    {
        $expression = $this->stripParentheses($expression);
        return $replace = $this->phpTag.'echo $this->includeFirst('.$expression.'); ?>';
    }


    /**
     * Compile the stack statements into the content.
     * @param  string $expression
     * @return string
     */
    protected function compileStack($expression)
    {
        return $this->phpTag."echo \$this->yieldPushContent{$expression}; ?>";
    }

    /**
     * Compile the push statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    public function compilePush($expression)
    {
        return $this->phpTag."\$this->startPush{$expression}; ?>";
    }
	/**
	 * Compile the push statements into valid PHP.
	 * @param  string $expression
	 * @return string
	 */
	public function compilePushOnce($expression)
	{
		$key = '$__pushonce__'.trim(substr($expression, 2, -2));
		//die(1);
		return $this->phpTag."if(!isset($key)): $key=1;  \$this->startPush{$expression}; ?>";
	}
    /**
     * Compile the push statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    public function compilePrepend($expression)
    {
        return $this->phpTag."\$this->startPush{$expression}; ?>";
    }

    /**
     * Compile the endpush statements into valid PHP.
     * @return string
     */
    protected function compileEndpush()
    {
        return $this->phpTag.'$this->stopPush(); ?>';
    }
	/**
	 * Compile the endpushonce statements into valid PHP.
	 * @return string
	 */
	protected function compileEndpushOnce()
	{
		return $this->phpTag.'$this->stopPush(); endif; ?>';
	}

    /**
     * Compile the endpush statements into valid PHP.
     * @return string
     */
    protected function compileEndPrepend()
    {
        return $this->phpTag.'$this->stopPrepend(); ?>';
    }

    /**
     * Compile the component statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileComponent($expression)
    {
        return $this->phpTag." \$this->startComponent{$expression}; ?>";
    }

    /**
     * Compile the end-component statements into valid PHP.
     * @return string
     */
    protected function compileEndComponent()
    {
        return $this->phpTag.' echo $this->renderComponent(); ?>';
    }

    /**
     * Compile the slot statements into valid PHP.
     * @param  string $expression
     * @return string
     */
    protected function compileSlot($expression)
    {
        return $this->phpTag." \$this->slot{$expression}; ?>";
    }

    /**
     * Compile the end-slot statements into valid PHP.
     * @return string
     */
    protected function compileEndSlot()
    {
        return $this->phpTag.' $this->endSlot(); ?>';
    }


    protected function compileAsset($expression)
    {
        return $this->phpTag." echo (isset(\$this->assetDict[$expression]))?\$this->assetDict[$expression]:\$this->baseUrl.'/'.{$expression}; ?>";
    }

    protected function compileJSon($expression)
    {
	    $parts = explode(',', $this->stripParentheses($expression));
	    $options = isset($parts[1]) ? trim($parts[1]) : JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
	    $depth = isset($parts[2]) ? trim($parts[2]) : 512;
        return $this->phpTag." echo json_encode($parts[0], $options, $depth); ?>";
    }
  
    
    protected function compileIsset($expression)
    {
        return $this->phpTag."if(isset{$expression}): ?>";
    }

    protected function compileEndIsset()
    {
        return $this->phpTag.'endif; ?>';
    }

    protected function compileEndEmpty()
    {
        return $this->phpTag.'endif; ?>';
    }

    //</editor-fold>
    //<editor-fold desc="push">
    /**
     * Start injecting content into a push section.
     * @param  string $section
     * @param  string $content
     * @return void
     */
    public function startPush($section, $content = '')
    {
        if ($content === '') {
            if (ob_start()) {
                $this->pushStack[] = $section;
            }
        } else {
            $this->extendPush($section, $content);
        }
    }

    /**
     * Start injecting content into a push section.
     * @param  string $section
     * @param  string $content
     * @return void
     */
    public function startPrepend($section, $content = '')
    {
        if ($content === '') {
            if (ob_start()) {
                array_unshift($this->pushStack[], $section);
            }
        } else {
            $this->extendPush($section, $content);
        }
    }

    /**
     * Stop injecting content into a push section.
     * @return string
     */
    public function stopPush()
    {
        if (empty($this->pushStack)) {
            $this->showError('stopPush', 'Cannot end a section without first starting one', true);
        }
        $last = array_pop($this->pushStack);
        $this->extendPush($last, ob_get_clean());
        return $last;
    }

    /**
     * Stop injecting content into a push section.
     * @return string
     */
    public function stopPrepend()
    {
        if (empty($this->pushStack)) {
            $this->showError('stopPrepend', 'Cannot end a section without first starting one', true);
        }
        $last = array_shift($this->pushStack);
        $this->extendStartPush($last, ob_get_clean());
        return $last;
    }

    /**
     * Append content to a given push section.
     * @param  string $section
     * @param  string $content
     * @return void
     */
    protected function extendPush($section, $content)
    {
        if (!isset($this->pushes[$section])) {
            $this->pushes[$section] = []; // start an empty section
        }
        if (!isset($this->pushes[$section][$this->renderCount])) {
            $this->pushes[$section][$this->renderCount] = $content;
        } else {
            $this->pushes[$section][$this->renderCount] .= $content;
        }
    }

    /**
     * Append content to a given push section.
     * @param  string $section
     * @param  string $content
     * @return void
     */
    protected function extendStartPush($section, $content)
    {
        if (!isset($this->pushes[$section])) {
            $this->pushes[$section] = []; // start an empty section
        }
        if (!isset($this->pushes[$section][$this->renderCount])) {
            $this->pushes[$section][$this->renderCount] = $content;
        } else {
            $this->pushes[$section][$this->renderCount] = $content.$this->pushes[$section][$this->renderCount];
        }
    }

    /**
     * Get the string contents of a push section.
     * @param  string $section
     * @param  string $default
     * @return string
     */
    public function yieldPushContent($section, $default = '')
    {
        if (!isset($this->pushes[$section])) {
            return $default;
        }
        return implode(array_reverse($this->pushes[$section]));
    }

    /**
     * Get the string contents of a push section.
     * @param int $each
     * @param string $splitText
     * @param string $splitEnd
     * @return string
     */
    public function splitForeach($each = 1, $splitText=',', $splitEnd = '')
    {
        $loopStack = static::last($this->loopsStack); // array(7) { ["index"]=> int(0) ["remaining"]=> int(6) ["count"]=> int(5) ["first"]=> bool(true) ["last"]=> bool(false) ["depth"]=> int(1) ["parent"]=> NULL }
        if ($loopStack['index'] == $loopStack['count']) {
            return $splitEnd;
        }
        if ($loopStack['index'] % $each == 0) {

            return $splitText;
        }
        return "";
    }
    //</editor-fold>
    //<editor-fold desc="compile extras">
    /**
     * Store the verbatim blocks and replace them with a temporary placeholder.
     * @param  string $value
     * @return string
     */
    protected function storeVerbatimBlocks($value)
    {
        return preg_replace_callback('/(?<!@)@verbatim(.*?)@endverbatim/s', function ($matches) {
            $this->verbatimBlocks[] = $matches[1];
            return $this->verbatimPlaceholder;
        }, $value);
    }


    /**
     * Register an "if" statement directive.
     * @param  string $name
     * @param  callable $callback
     * @return string
     */
    public function _if($name, callable $callback)
    {
        $this->conditions[$name] = $callback;

        $this->directive($name, function ($expression) use ($name) {
            $tmp = $this->stripParentheses($expression);
            return $expression !== ''
                ? $this->phpTag." if (\$this->check('{$name}', {$tmp})): ?>"
                : $this->phpTag." if (\$this->check('{$name}')): ?>";
        });

        $this->directive('else'.$name, function ($expression) use ($name) {
            $tmp = $this->stripParentheses($expression);
            return $expression !== ''
                ? $this->phpTag." elseif (\$this->check('{$name}', {$tmp})): ?>"
                : $this->phpTag." elseif (\$this->check('{$name}')): ?>";
        });

        $this->directive('end'.$name, function () {
            return $this->phpTag.' endif; ?>';
        });
        return "";
    }

    /**
     * @param string $name
     * @param $args[]
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $args)
    {
        if ($name=='if') {
            return $this->_if(@$args[0], @$args[1]);
        }
        throw new Exception("function $name is not defined<br>");
    }

    /**
     * Check the result of a condition.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return bool
     */
    public function check($name, ...$parameters)
    {
        return call_user_func($this->conditions[$name], ...$parameters);
    }

    /**
     * @param bool $bool
     * @param string $view name of the view
     * @param array $value arrays of values
     * @return string
     * @throws Exception
     */
    public function includeWhen($bool = false, $view = '', $value = [])
    {
        if ($bool) {
            return $this->runChild($view, $value);
        } else {
            return "";
        }
    }

    /**
     * @param array $views array of views
     * @param array $value
     * @return string
     * @throws Exception
     */
    public function includeFirst($views = [], $value = [])
    {
        foreach ($views as $view) {
            if ($this->templateExist($view)) {
                return $this->runChild($view, $value);
            }
        };
        return '';
    }

    /**
     * Convert an array such as ["class1"=>"myclass","style="mystyle"] to class1='myclass' style='mystyle' string
     * @param array|string $array array to convert
     * @return string
     */
    public function convertArg($array)
    {
        if (!is_array($array)) {
            return $array;  // nothing to convert.
        }
        return implode(' ', array_map('static::convertArgCallBack', array_keys($array), $array));
    }

    function convertArgCallBack($k, $v)
    {
        return $k."='{$v}' ";
    }

    /**
     * Replace the raw placeholders with the original code stored in the raw blocks.
     * @param  string $result
     * @return string
     */
    protected function restoreVerbatimBlocks($result)
    {
        $result = preg_replace_callback('/'.preg_quote($this->verbatimPlaceholder).'/', function () {
            return array_shift($this->verbatimBlocks);
        }, $result);
        $this->verbatimBlocks = [];
        return $result;
    }

    /**
     * Parse the tokens from the template.
     * @param  array $token
     * @return string
     */
    protected function parseToken($token)
    {
        list($id, $content) = $token;
        if ($id == T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $content = $this->{"compile{$type}"}($content);
            }
        }
        return $content;
    }

    /**
     * Regenerates the csrf token and stores in the session.
     * It requires an open session.
     */
    public function regenerateToken() {
        try {
            $this->csrf_token = bin2hex(random_bytes(10));
        } catch (Exception $e) {
            $this->csrf_token="123456789012345678901234567890"; // unable to generates a random token.
        }
        @$_SESSION["_token"]=$this->csrf_token."|".$this->ipClient();
    }

    /**
     * Returns the current token. if there is not a token then it generates a new one.
     * It could require an open session.
     * @param bool $fullToken  It returns a token with the current ip.
     * @return string
     */
    public function csrf_token($fullToken=false) {
        if ($this->csrf_token=="") {
            $this->regenerateToken();
        }
        if ($fullToken) {
            return $this->csrf_token."|".$this->ipClient();
        }
        return $this->csrf_token;
    }

    /**
     * Validates if the csrf token is valid or not.
     * It could require an open session.
     * @return bool
     */
    public function csrfIsValid() {

        if (@$_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->csrf_token= @$_POST['_token'];
            return $this->csrf_token."|".$this->ipClient()==@$_SESSION["_token"];
        } else {
            if ($this->csrf_token=="") {
                // if not token then we generate a new one
                $this->regenerateToken();
            }
            return true;
        }
    }
    public function ipClient() {

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (preg_match( "/^([d]{1,3}).([d]{1,3}).([d]{1,3}).([d]{1,3})$/", $_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }
    /**
     * Get the echo methods in the proper order for compilation.
     * @return array
     */
    protected function getEchoMethods()
    {
        $methods = [
            'compileRawEchos' => strlen(stripcslashes($this->rawTags[0])),
            'compileEscapedEchos' => strlen(stripcslashes($this->escapedTags[0])),
            'compileRegularEchos' => strlen(stripcslashes($this->contentTags[0])),
        ];
        uksort($methods, function ($method1, $method2) use ($methods) {
            // Ensure the longest tags are processed first
            if ($methods[$method1] > $methods[$method2]) {
                return -1;
            }
            if ($methods[$method1] < $methods[$method2]) {
                return 1;
            }
            // Otherwise give preference to raw tags (assuming they've overridden)
            if ($method1 === 'compileRawEchos') {
                return -1;
            }
            if ($method2 === 'compileRawEchos') {
                return 1;
            }
            if ($method1 === 'compileEscapedEchos') {
                return -1;
            }
            if ($method2 === 'compileEscapedEchos') {
                return 1;
            }
            throw new Exception('Method not defined');
        });
        return $methods;
    }

    /**
     * Stop injecting content into a section and return its contents.
     * @return string
     */
    public function yieldSection()
    {
        $r = @$this->sections[$this->stopSection()];
        return $r;
    }

    public function dump($object,$jsconsole=false) {
        if (!$jsconsole) {
            echo '<pre>';
            var_dump($object);
            echo '</pre>';
        } else {
            echo "<script>console.log(".json_encode($object).")</script>";
        }
    }

    /**
     * Start injecting content into a section.
     * @param  string $section
     * @param  string $content
     * @return void
     */
    public function startSection($section, $content = '')
    {
        if ($content === '') {
            ob_start() && $this->sectionStack[] = $section;
        } else {
            $this->extendSection($section, $content);
        }
    }

    /**
     * Append content to a given section.
     * @param  string $section
     * @param  string $content
     * @return void
     */
    protected function extendSection($section, $content)
    {
        if (isset($this->sections[$section])) {
            $content = str_replace($this->PARENTKEY, $content, $this->sections[$section]);
            $this->sections[$section] = $content;
        } else {
            $this->sections[$section] = $content;
        }
    }

    /**
     * Stop injecting content into a section.
     * @param  bool $overwrite
     * @return string
     */
    public function stopSection($overwrite = false)
    {
        if (empty($this->sectionStack)) {
            throw new InvalidArgumentException('Cannot end a section without first starting one.');
        }
        $last = array_pop($this->sectionStack);
        if ($overwrite) {
            $this->sections[$last] = ob_get_clean();
        } else {
            $this->extendSection($last, ob_get_clean());
        }
        return $last;
    }

    /**
     * Stop injecting content into a section and append it.
     * @return string
     * @throws \InvalidArgumentException
     */
    public function appendSection()
    {
        if (empty($this->sectionStack)) {
            throw new InvalidArgumentException('Cannot end a section without first starting one.');
        }
        $last = array_pop($this->sectionStack);
        if (isset($this->sections[$last])) {
            $this->sections[$last] .= ob_get_clean();
        } else {
            $this->sections[$last] = ob_get_clean();
        }
        return $last;
    }

	/**
	 * Adds a global variable
	 * @param $varname
	 * @param $value
	 */
    public function share($varname,$value) {
    	$this->variablesGlobal[$varname]=$value;
    }

    /**
     * Get the string contents of a section.
     * @param  string $section
     * @param  string $default
     * @return string
     */
    public function yieldContent($section, $default = '')
    {
        if (isset($this->sections[$section])) {
            $content = str_replace($this->PARENTKEY, $default, $this->sections[$section]);
            return $content;
        } else {
            return $default;
        }
    }

    /**
     * Register a custom Blade compiler.
     * @param  callable $compiler
     * @return void
     */
    public function extend(callable $compiler)
    {
        $this->extensions[] = $compiler;
    }

    /**
     * Register a handler for custom directives.
     * @param  string $name
     * @param  callable $handler
     * @return void
     */
    public function directive($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;
        $this->customDirectivesRT[$name]=false;
    }
    /**
     * Register a handler for custom directives for run at runtime
     * @param  string $name
     * @param  callable $handler
     * @return void
     */
    public function directiveRT($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;
        $this->customDirectivesRT[$name]=true;
    }

    /**
     * Sets the content tags used for the compiler.
     * @param  string $openTag
     * @param  string $closeTag
     * @param  bool $escaped
     * @return void
     */
    public function setContentTags($openTag, $closeTag, $escaped = false)
    {
        $property = ($escaped === true) ? 'escapedTags' : 'contentTags';
        $this->{$property} = [preg_quote($openTag), preg_quote($closeTag)];
    }

    /**
     * Sets the escaped content tags used for the compiler.
     * @param  string $openTag
     * @param  string $closeTag
     * @return void
     */
    public function setEscapedContentTags($openTag, $closeTag)
    {
        $this->setContentTags($openTag, $closeTag, true);
    }

    /**
     * Gets the content tags used for the compiler.
     * @return array
     */
    public function getContentTags()
    {
        return $this->getTags();
    }

    /**
     * Gets the escaped content tags used for the compiler.
     * @return array
     */
    public function getEscapedContentTags()
    {
        return $this->getTags(true);
    }

    /**
     * Gets the tags used for the compiler.
     * @param  bool $escaped
     * @return array
     */
    protected function getTags($escaped = false)
    {
        $tags = $escaped ? $this->escapedTags : $this->contentTags;
        return array_map('stripcslashes', $tags);
    }

    /**
     * Sets the function used for resolving classes with inject.
     * @param callable $function
     */
    public function setInjectResolver(callable $function)
    {
        $this->injectResolver = $function;
    }

    /**
     * Resolve a given class using the injectResolver callable.
     * @param string $className
     * @param string|null $variableName
     * @return mixed
     */
    protected function injectClass($className, $variableName = null)
    {
        if (isset($this->injectResolver)) {
            return call_user_func_array($this->injectResolver, [$className, $variableName]);
        } else {
            $fullClassName = $className."\\".$variableName;
            return new $fullClassName();
        }
    }
    //</editor-fold>
    //<editor-fold desc="file members">
    /**
     * Get the full path of the compiled file.
     * @param string $templateName
     * @return string
     */
    public function getCompiledFile($templateName = '')
    {
        $templateName = (empty($templateName)) ? $this->fileName : $templateName;
        if ($this->getMode()  == self::MODE_DEBUG) {
            return $this->compiledPath.'/'.$templateName.$this->compileExtension;
        } else {
            return $this->compiledPath.'/'.sha1($templateName).$this->compileExtension;
        }
    }

    /**
     * Get the full path of the template file.
     * <p>Example: getTemplateFile('.abc.def')</p> 
     * @param string $templateName template name. If not template is set then it uses the base template.
     * @return string
     */
    public function getTemplateFile($templateName = '')
    {
        $templateName = (empty($templateName)) ? $this->fileName : $templateName;
        if (strpos($templateName,'/')!==false) return $this->locateTemplate($templateName); // it's a literal
        $arr = explode('.', $templateName);
        $c = count($arr);
        if ($c == 1) {
        	// its in the root of the template folder.
            return $this->locateTemplate($templateName.$this->fileExtension);
        } else {
            $file = $arr[$c - 1];
            array_splice($arr, $c - 1, $c - 1); // delete the last element
            $path = implode('/', $arr);
            return $this->locateTemplate($path.'/'.$file.$this->fileExtension);
        }
    }

    /**
     * Find template file with the given name in all template paths in the order the paths were written
     * @param string $name Filename
     * @return string template file
     */
    private function locateTemplate($name){
        if(is_array($this->templatePath)) {
            $path = '';
            foreach ($this->templatePath as $dir){
                $path = $dir.'/'.$name;
                if(file_exists($path)){
                    break;
                }
            }
            return $path;
        }else{
            return $this->templatePath.'/'.$name;
        }
    }

    /**
     * Determine if the view  is expired.
     * @param string|null $fileName
     * @return bool
     */
    public function isExpired($fileName)
    {
        $compiled = $this->getCompiledFile($fileName);
        $template = $this->getTemplateFile($fileName);
        if (!file_exists($template)) {
            if ($this->mode==self::MODE_DEBUG) {
                $this->showError("Read file", "Template not found :".$this->fileName." on file: $template", true);
            } else {
                $this->showError("Read file", "Template not found :".$this->fileName, true);
            }
        }
        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (!$this->compiledPath || !file_exists($compiled)) {
            return true;
        }
        return filemtime($compiled) < filemtime($template);
    }

    /**
     * Returns true if the template exists. Otherwise it returns false
     * @param $templateName
     * @return bool
     */
    private function templateExist($templateName)
    {
        $file = $this->getTemplateFile($templateName);
        return file_exists($file);
    }

    /**
     * Get the contents of a file.
     * @param $fileName
     * @return string
     */
    public function getFile($fileName)
    {
        if (is_file($fileName)) return file_get_contents($fileName);
        $this->showError('getFile', "File does not exist at path {$fileName}", true);
        return '';
    }

    /**
     * Get the file extension for template files.
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Set the file extension for the template files.
     * It must includes the leading dot e.g. .blade.php
     * @param string $fileExtension Example: .prefix.ext
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * Get the file extension for template files.
     * @return string
     */
    public function getCompiledExtension()
    {
        return $this->compileExtension;
    }

    /**
     * Set the file extension for the compiled files.
     * Including the leading dot for the extension is required, e.g. .bladec
     * @param $fileExtension
     */
    public function setCompiledExtension($fileExtension)
    {
        $this->compileExtension = $fileExtension;
    }

    /**
     * Evaluates a compiled file using the current variables
     * @param string $compiledFile full path of the compile file.
     * @param array $variables
     * @return string
     * @throws Exception
     */
    protected function evaluatePath($compiledFile, $variables)
    {
        ob_start();
        extract($variables);
        // We'll evaluate the contents of the view inside a try/catch block so we can
        // flush out any stray output that might get out before an error occurs or
        // an exception is thrown. This prevents any partial views from leaking.
        try {
            /** @noinspection PhpIncludeInspection */
            include $compiledFile;
        } catch (\Exception $e) {
            $this->handleViewException($e);
        }
        return ltrim(ob_get_clean());
    }

	/**
	 * Evaluates a text (string) using the current variables
	 * @param string $content
	 * @param array $variables
	 * @return string
	 * @throws Exception
	 */
    protected function evaluateText($content,$variables) {
	    ob_start();
	    extract($variables);
	    // We'll evaluate the contents of the view inside a try/catch block so we can
	    // flush out any stray output that might get out before an error occurs or
	    // an exception is thrown. This prevents any partial views from leaking.
	    try {
		    eval(' ?>'.$content.'<?php ');
	    } catch (\Exception $e) {
		    $this->handleViewException($e);
	    }
	    return ltrim(ob_get_clean());
    }

    /**
     * Handle a view exception.
     * @param  \Exception $e
     * @return void
     * @throws $e
     */
    protected function handleViewException($e)
    {
        ob_get_clean();
        throw $e;
    }
    //</editor-fold>
    //<editor-fold desc="Array Functions">
    /**
     * Get an item from an array using "dot" notation.
     * @param  \ArrayAccess|array $array
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        $accesible = is_array($array) || $array instanceof ArrayAccess;
        if (!$accesible) {
            return static::value($default);
        }
        if (is_null($key)) {
            return $array;
        }
        if (static::exists($array, $key)) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if ($accesible && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return static::value($default);
            }
        }
        return $array;
    }

    /**
     * Determine if the given key exists in the provided array.
     * @param  \ArrayAccess|array $array
     * @param  string|int $key
     * @return bool
     */
    public static function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }

    /**
     * Return the first element in an array passing a given truth test.
     * @param  array $array
     * @param  callable|null $callback
     * @param  mixed $default
     * @return mixed
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? static::value($default) : reset($array);
        }
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }
        return static::value($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     * @param  array $array
     * @param  callable|null $callback
     * @param  mixed $default
     * @return mixed
     */
    public static function last($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? static::value($default) : end($array);
        }
        return static::first(array_reverse($array), $callback, $default);
    }
    //</editor-fold>
    //<editor-fold desc="string functions">
    /**
     * Determine if a given string contains a given substring.
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (function_exists('mb_strpos')) {
                if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                    return true;
                }
            } else {
                if ($needle != '' && strpos($haystack, $needle) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Strip the parentheses from the given expression.
     * @param  string $expression
     * @return string
     */
    protected function stripParentheses($expression)
    {
        if (static::startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }
        return $expression;
    }

    /**
     * Remove first and end quote from a quoted string of text
     * @param mixed $text
     * @return null|string|string[]
     */
    protected function stripQuotes($text)
    {
        $unquoted = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', trim($text));
        return $unquoted;
    }

    /**
     * Determine if a given string starts with a given substring.
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (function_exists('mb_strpos')) {
                if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                    return true;
                }
            } else {
                if ($needle != '' && strpos($haystack, $needle) === 0) {
                    return true;
                }

            }
        }

        return false;
    }

    /**
     * Return the default value of the given value.
     * @param  mixed $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * Escape HTML entities in a string.
     * @param  string $value
     * @return string
     */
    public static function e($value)
    {
        if (is_array($value) || is_object($value)) {
            return htmlentities(print_r($value,true), ENT_QUOTES, 'UTF-8', false);
        }
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
    //</editor-fold>
    //<editor-fold desc="loop functions">
    /**
     * Add new loop to the stack.
     * @param  array|\Countable $data
     * @return void
     */
    public function addLoop($data)
    {
        $length = is_array($data) || $data instanceof Countable ? \count($data) : null;
        $parent = static::last($this->loopsStack);
        $this->loopsStack[] = [
            'index' => 0,
            'remaining' => isset($length) ? $length + 1 : null,
            'count' => $length,
            'first' => true,
            'last' => isset($length) ? $length == 1 : null,
            'depth' => count($this->loopsStack) + 1,
            'parent' => $parent ? (object)$parent : null,
        ];
    }

    /**
     * Increment the top loop's indices.
     * @return void
     */
    public function incrementLoopIndices()
    {
        $loop = &$this->loopsStack[count($this->loopsStack) - 1];
        $loop['index']++;
        $loop['first'] = $loop['index'] == 1;
        if (isset($loop['count'])) {
            $loop['remaining']--;
            $loop['last'] = $loop['index'] == $loop['count'];
        }
    }

    /**
     * Pop a loop from the top of the loop stack.
     * @return void
     */
    public function popLoop()
    {
        array_pop($this->loopsStack);
    }

    /**
     * Get an instance of the first loop in the stack.
     * @return array|object
     */
    public function getFirstLoop()
    {
        return ($last = static::last($this->loopsStack)) ? (object)$last : null;
    }

    /**
     * Get the entire loop stack.
     * @return array
     */
    public function getLoopStack()
    {
        return $this->loopsStack;
    }

    /**
     * Get the rendered contents of a partial from a loop.
     * @param  string $view
     * @param  array $data
     * @param  string $iterator
     * @param  string $empty
     * @return string
     * @throws Exception
     */
    public function renderEach($view, $data, $iterator, $empty = 'raw|')
    {
        $result = '';
        // If is actually data in the array, we will loop through the data and append
        // an instance of the partial view to the final result HTML passing in the
        // iterated value of this data array, allowing the views to access them.
        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $data = ['key' => $key, $iterator => $value];
                $result .= $this->runChild($view, $data);
            }
        }
        // If there is no data in the array, we will render the contents of the empty
        // view. Alternatively, the "empty view" could be a raw string that begins
        // with "raw|" for convenience and to let this know that it is a string.
        else {
            if (static::startsWith($empty, 'raw|')) {
                $result = substr($empty, 4);
            } else {
                $result = $this->run($empty, []);
            }
        }
        return $result;
    }

    /**
     * Show an error in the web.
     * @param string $id Title of the error
     * @param string $text Message of the error
     * @param bool $critic if true then the compilation is ended, otherwise it continues
     * @return string
     */
    public function showError($id, $text, $critic = false)
    {
        ob_get_clean();
        echo "<div style='background-color: red; color: black; padding: 3px; border: solid 1px black;'>";
        echo "BladeOne Error [{$id}]:<br>";
        echo "<span style='color:white'>$text</span><br></div>\n";
        if ($critic) {
            die(1);
        }
        return "";
    }

    /**
     * Start a component rendering process.
     * @param  string $name
     * @param  array $data
     * @return void
     */
    public function startComponent($name, array $data = [])
    {
        if (ob_start()) {
            $this->componentStack[] = $name;

            $this->componentData[$this->currentComponent()] = $data;

            $this->slots[$this->currentComponent()] = [];
        }
    }

    /**
     * Render the current component.
     * @return string
     * @throws Exception
     */
    public function renderComponent()
    {
        $name = array_pop($this->componentStack);
        return $this->runChild($name, $this->componentData());
    }

    /**
     * Get the data for the given component.
     * @return array
     */
    protected function componentData()
    {
        return array_merge(
            $this->componentData[count($this->componentStack)],
            ['slot' => trim(ob_get_clean())],
            $this->slots[count($this->componentStack)]
        );
    }

    /**
     * Start the slot rendering process.
     * @param  string $name
     * @param  string|null $content
     * @return void
     */
    public function slot($name, $content = null)
    {
        if (count(func_get_args()) == 2) {
            $this->slots[$this->currentComponent()][$name] = $content;
        } else {
            if (ob_start()) {
                $this->slots[$this->currentComponent()][$name] = '';

                $this->slotStack[$this->currentComponent()][] = $name;
            }
        }
    }

    /**
     * Save the slot content for rendering.
     * @return void
     */
    public function endSlot()
    {
        static::last($this->componentStack);

        $currentSlot = array_pop(
            $this->slotStack[$this->currentComponent()]
        );

        $this->slots[$this->currentComponent()]
        [$currentSlot] = trim(ob_get_clean());
    }

    /**
     * Get the index for the current component.
     * @return int
     */
    protected function currentComponent()
    {
        return count($this->componentStack) - 1;
    }


    //</editor-fold>


}
/**
 * BladeOne - A Blade Template implementation in a single file
 * Copyright (c) 2016-2019 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Part of this code is based in the work of Laravel PHP Components.
 */
