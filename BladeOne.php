<?php

/**
 * BladeOne - A Blade Template implementation in a single file
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Part of this code is based in the work of Laravel PHP Components.
 *
 *
 */

/**
 * Class BladeOne
 * @package  BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 * @version 1.5 2016-07-03
 * @link https://github.com/EFTEC/BladeOne
 */

namespace eftec\bladeone;

use Exception;

class BladeOne
{

    //<editor-fold desc="fields">
    /**
     * All of the registered extensions.
     *
     * @var array
     */
    protected $extensions = array();
    /**
     * All of the finished, captured sections.
     *
     * @var array
     */
    protected $sections = array();

    /**
     * The file currently being compiled.
     *
     * @var string
     */
    protected $fileName;


    /**
     * The stack of in-progress sections.
     *
     * @var array
     */
    protected $sectionStack = array();

    /**
     * The stack of in-progress loops.
     *
     * @var array
     */
    protected $loopsStack = [];
    /**
     * @var array
     */
    protected $variables=array();

    /**
     * All of the available compiler functions.
     *
     * @var array
     */

    protected $compilers = [
        'Extensions',
        'Statements',
        'Comments',
        'Echos',
    ];

    /**
     * The stack of in-progress push sections.
     *
     * @var array
     */
    protected $pushStack = [];
    /**
     * All of the finished, captured push sections.
     *
     * @var array
     */
    protected $pushes = [];
    /**
     * The number of active rendering operations.
     *
     * @var int
     */
    protected $renderCount = 0;

    /**
     * Get the template path for the compiled views.
     *
     * @var string
     */
    protected $templatePath;
    /**
     * Get the compiled path for the compiled views.
     *
     * @var string
     */
    protected $compiledPath;

    /**
     * All custom "directive" handlers.
     *
     * This was implemented as a more usable "extend" in 5.1.
     *
     * @var array
     */
    protected $customDirectives = [];

    /**
     * The file currently being compiled.
     *
     * @var string
     */
    protected $path;

    protected $isRunFast=false;



    /**
     * Array of opening and closing tags for raw echos.
     *
     * @var array
     */
    protected $rawTags = ['{!!', '!!}'];

    /**
     * Array of opening and closing tags for regular echos.
     *
     * @var array
     */
    protected $contentTags = ['{{', '}}'];

    /**
     * Array of opening and closing tags for escaped echos.
     *
     * @var array
     */
    protected $escapedTags = ['{{{', '}}}'];

    /**
     * The "regular" / legacy echo string format.
     *
     * @var string
     */
    protected $echoFormat = 'static::e(%s)';

    /**
     * Array of footer lines to be added to template.
     *
     * @var array
     */
    protected $footer = [];

    /**
     * Placeholder to temporary mark the position of verbatim blocks.
     *
     * @var string
     */
    protected $verbatimPlaceholder = '@__verbatim__@';

    /**
     * Array to temporary store the verbatim blocks found in the template.
     *
     * @var array
     */
    protected $verbatimBlocks = [];

    /**
     * Counter to keep track of nested forelse statements.
     *
     * @var int
     */
    protected $forelseCounter = 0;

    public $phpTag='<?php ';


    //</editor-fold>

    //<editor-fold desc="constructor">
    /**
     * Bob the constructor.
     *
     * @param  string $templatePath
     * @param $compiledPath
     */
    public function __construct($templatePath, $compiledPath)
    {
        $this->templatePath = $templatePath;
        $this->compiledPath = $compiledPath;
    }
    //</editor-fold>



    //<editor-fold desc="common">
    /**
     * Macro of function run
     * @param $view
     * @param $variables
     * @return string
     */
    public function runChild($view,$variables=array()) {
        if (is_array($variables)) {
            $newVariables = array_merge($this->variables, $variables);
        } else {
            $this->showError("run/include","Include/run variables should be defined as array ['idx'=>'value']",true);
            return "";
        }
        return $this->runInternal($view,$newVariables,false,false,$this->isRunFast);
    }
    /**
     * Mode of the engine. 1= force recompile, 2=fast (no verify files)
     * @return int
     */
    public function getMode() {
        $mode=0;
        if (defined('BLADEONE_MODE')) {
            $mode=BLADEONE_MODE;
        }
        return $mode;
    }

    /**
     * run the blade engine. It returns the result of the code.
     * @param $view
     * @param array $variables
     * @return string
     */

    public function run($view,$variables=array())
    {
        $mode=$this->getMode();
        $forced=$mode & 1; // mode=1 forced:it recompiles no matter if the compiled file exists or not.
        $runFast=$mode & 2; // mode=2 runfast: the code is not compiled neither checked and it runs directly the compiled

        if ($mode==3) {
            $this->showError("run","we can't force and run fast at the same time",true);
        }
        return $this->runInternal($view,$variables,$forced,true,$runFast);
    }

    /**
     * run the blade engine. It returns the result of the code.
     * @param $view
     * @param array $variables
     * @param bool $forced if true then it recompiles no matter if the compiled file exists or not.
     * @param bool $isParent
     * @param bool $runFast  if true then the code is not compiled neither checked and it runs directly the compiled version.
     * @return string
     */
    private function runInternal($view,$variables=array(), $forced=false,$isParent=true,$runFast=false)
    {

        if ($isParent) {
            $this->variables=$variables;
        }
        if (!$runFast) {
            // a) if the compile is forced then we compile the original file, then save the file.
            // b) if the compile is not forced then we read the datetime of both file and we compared.
            // c) in both cases, if the compiled doesn't exist then we compile.
            $this->compile($view, $forced);
        } else {
            // running fast, we don't compile neither we check or read the original template.
            if ($view) {
                $this->fileName = $view;
            }
        }
        $this->isRunFast=$runFast;
        return $this->evaluatePath($this->getCompiledFile(),$variables);
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string $fileName
     * @param bool $forced
     * @throws Exception
     */
    public function compile($fileName = null,$forced=false)
    {
        if ($fileName) {
            $this->fileName = $fileName;
        }
        $compiled = $this->getCompiledFile();
        $template=$this->getTemplateFile();
        if ($this->isExpired() || $forced) {
            // compile the original file
            $contents = $this->compileString($this->getFile($template));

            if (!is_null($this->compiledPath)) {
                $ok=@file_put_contents($compiled, $contents);
                if (!$ok) {
                    $this->showError("Compiling","Unable to save the file [{$fileName}]. Check the compile folder is defined and has the right permission");
                }
            }
        }
    }


    //</editor-fold>


    //<editor-fold desc="compile">

    /**
     * Compile the given Blade template contents.
     *
     * @param  string  $value
     * @return string
     */
    public function compileString($value)
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

        if (! empty($this->verbatimBlocks)) {
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
     *
     * @param  string  $value
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
     *
     * @param  string  $value
     * @return string
     */
    protected function compileComments($value)
    {
        $pattern = sprintf('/%s--(.*?)--%s/s', $this->contentTags[0], $this->contentTags[1]);

        return preg_replace($pattern, $this->phpTag.'/*$1*/ ?>', $value);
    }

    /**
     * Compile Blade echos into valid PHP.
     *
     * @param  string  $value
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
     *
     * @param  string  $value
     * @return mixed
     */
    protected function compileStatements($value)
    {
        $callback = function ($match) {
            if (static::contains($match[1], '@')) {
                $match[0] = isset($match[3]) ? $match[1].$match[3] : $match[1];
            } elseif (isset($this->customDirectives[$match[1]])) {
                $match[0] = call_user_func($this->customDirectives[$match[1]], static::get($match, 3));
            } elseif (method_exists($this, $method = 'compile'.ucfirst($match[1]))) {
                $match[0] = $this->$method(static::get($match, 3));
            } else {
                $this->showError("@compile","Operation not defined:@".$match[1],true);
            }

            return isset($match[3]) ? $match[0] : $match[0].$match[2];
        };

        return preg_replace_callback('/\B@(@?\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $value);
    }

    /**
     * Compile the "raw" echo statements.
     *
     * @param  string  $value
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
     *
     * @param  string  $value
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
     *
     * @param  string  $value
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
     *
     * @param  string  $value
     * @return string
     */
    public function compileEchoDefaults($value)
    {
        return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $value);
    }

    /**
     * Compile the each statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileEach($expression)
    {
        return $this->phpTag."echo \$this->renderEach{$expression}; ?>";
    }

    /**
     * Compile the inject statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileInject($expression)
    {
        $segments = explode(',', preg_replace("/[\(\)\\\"\']/", '', $expression));

        return $this->phpTag.'$'.trim($segments[0])." = app('".trim($segments[1])."'); ?>";
    }

    protected function compileSet($expression)
    {
        $segments = explode('=', preg_replace("/[\(\)\\\"\']/", '', $expression));
        $value=(count($segments)>=2)?' ='.$segments[1]:'++';
        return $this->phpTag.trim($segments[0]).$value."; ?>";
    }
    /**
     * Compile the yield statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileYield($expression)
    {
        return $this->phpTag."echo \$this->yieldContent{$expression}; ?>";
    }


    function generateCallTrace()
    {
        $e = new Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();

        for ($i = 0; $i < $length; $i++)
        {
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }

        return "\t" . implode("\n\t", $result);
    }
    /**
     * Compile the show statements into valid PHP.
     *
     * @return string
     */
    protected function compileShow()
    {
        return $this->phpTag.'echo $this->yieldSection(); ?>';
    }

    /**
     * Compile the section statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileSection($expression)
    {
        return $this->phpTag."\$this->startSection{$expression}; ?>";
    }


    /**
     * Compile the append statements into valid PHP.
     *
     * @return string
     */
    protected function compileAppend()
    {
        return $this->phpTag.'$this->appendSection(); ?>';
    }

    /**
     * Compile the end-section statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndsection()
    {
        return $this->phpTag.'$this->stopSection(); ?>';
    }

    /**
     * Compile the stop statements into valid PHP.
     *
     * @return string
     */
    protected function compileStop()
    {
        return $this->phpTag.'$this->stopSection(); ?>';
    }


    /**
     * Compile the overwrite statements into valid PHP.
     *
     * @return string
     */
    protected function compileOverwrite()
    {
        return $this->phpTag.'$this->stopSection(true); ?>';
    }

    /**
     * Compile the unless statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileUnless($expression)
    {
        return $this->phpTag."if ( ! $expression): ?>";
    }

    /**
     * Compile the end unless statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndunless()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the lang statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileLang($expression)
    {
        return $this->phpTag."echo app('translator')->get$expression; ?>";
    }

    /**
     * Compile the choice statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileChoice($expression)
    {
        return $this->phpTag."echo app('translator')->choice$expression; ?>";
    }

    /**
     * Compile the else statements into valid PHP.
     *
     * @return string
     */
    protected function compileElse()
    {
        return $this->phpTag.'else: ?>';
    }

    /**
     * Compile the for statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileFor($expression)
    {
        return $this->phpTag."for{$expression}: ?>";
    }

    /**
     * Compile the foreach statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileForeach($expression)
    {
        preg_match('/\( *(.*) *as *([^\)]*)/', $expression, $matches);

        $iteratee = trim($matches[1]);

        $iteration = trim($matches[2]);

        $initLoop = "\$__currentLoopData = {$iteratee}; \$this->addLoop(\$__currentLoopData);";

        $iterateLoop = '$this->incrementLoopIndices(); $loop = $this->getFirstLoop();';

        return $this->phpTag."{$initLoop} foreach(\$__currentLoopData as {$iteration}): {$iterateLoop} ?>";
    }

    /**
     * Compile the break statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileBreak($expression)
    {
        return $expression ? $this->phpTag."if{$expression} break; ?>" : $this->phpTag.'break; ?>';
    }

    /**
     * Compile the continue statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileContinue($expression)
    {
        return $expression ? $this->phpTag."if{$expression} continue; ?>" : $this->phpTag.'continue; ?>';
    }

    /**
     * Compile the forelse statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileForelse($expression)
    {
        $empty = '$__empty_'.++$this->forelseCounter;

        return $this->phpTag."{$empty} = true; foreach{$expression}: {$empty} = false; ?>";
    }

    /**
     * Compile the can statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileCan($expression)
    {
        return $this->phpTag."if (app('Illuminate\\Contracts\\Auth\\Access\\Gate')->check{$expression}): ?>";
    }

    /**
     * Compile the else-can statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileElsecan($expression)
    {
        return $this->phpTag."elseif (app('Illuminate\\Contracts\\Auth\\Access\\Gate')->check{$expression}): ?>";
    }

    /**
     * Compile the cannot statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileCannot($expression)
    {
        return $this->phpTag."if (app('Illuminate\\Contracts\\Auth\\Access\\Gate')->denies{$expression}): ?>";
    }

    /**
     * Compile the else-can statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileElsecannot($expression)
    {
        return $this->phpTag."elseif (app('Illuminate\\Contracts\\Auth\\Access\\Gate')->denies{$expression}): ?>";
    }

    /**
     * Compile the if statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIf($expression)
    {
        return $this->phpTag."if{$expression}: ?>";
    }

    /**
     * Compile the else-if statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileElseif($expression)
    {
        return $this->phpTag."elseif{$expression}: ?>";
    }

    /**
     * Compile the forelse statements into valid PHP.
     *
     * @return string
     */
    protected function compileEmpty()
    {
        $empty = '$__empty_'.$this->forelseCounter--;

        return $this->phpTag."endforeach; if ({$empty}): ?>";
    }

    /**
     * Compile the has section statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileHasSection($expression)
    {
        return $this->phpTag."if (! empty(trim(\$this->yieldContent{$expression}))): ?>";
    }


    /**
     * Compile the end-while statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndwhile()
    {
        return $this->phpTag.'endwhile; ?>';
    }

    /**
     * Compile the end-for statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndfor()
    {
        return $this->phpTag.'endfor; ?>';
    }

    /**
     * Compile the end-for-each statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndforeach()
    {
        return $this->phpTag.'endforeach; $this->popLoop(); $loop = $this->getFirstLoop(); ?>';
    }

    /**
     * Compile the end-can statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndcan()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the end-cannot statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndcannot()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the end-if statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndif()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the end-for-else statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndforelse()
    {
        return $this->phpTag.'endif; ?>';
    }

    /**
     * Compile the raw PHP statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compilePhp($expression)
    {
        return $expression ? $this->phpTag."{$expression}; ?>" : $this->phpTag.'';
    }

    /**
     * Compile end-php statement into valid PHP.
     *
     * @return string
     */
    protected function compileEndphp()
    {
        return ' ?>';
    }

    /**
     * Compile the unset statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileUnset($expression)
    {
        return $this->phpTag."unset{$expression}; ?>";
    }

    /**
     * Compile the extends statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileExtends($expression)
    {
        $expression = $this->stripParentheses($expression);
        /*
        $data = $this->phpTag."echo \$__env->make($expression, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
        */
        $data= $this->phpTag.'echo $this->runChild('.$expression.'); ?>';
        $this->footer[] = $data;

        return '';
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileInclude($expression)
    {
        $expression = $this->stripParentheses($expression);

        return $replace = $this->phpTag.'echo $this->runChild('.$expression.'); ?>';
        /* return $this->phpTag."echo \$__env->make($expression, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
        */
    }

    /**
     * Compile the include statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileIncludeIf($expression)
    {
        $expression = $this->stripParentheses($expression);

        return $replace = $this->phpTag.'if (\$this->exists($expression)) echo $this->runChild('.$expression.'); ?>';

        /*return $this->phpTag."if (\$__env->exists($expression)) echo \$__env->make($expression, array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
        */
    }

    /**
     * Compile the stack statements into the content.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileStack($expression)
    {
        return $this->phpTag."echo \$this->yieldPushContent{$expression}; ?>";
    }

    /**
     * Compile the push statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    public function compilePush($expression)
    {
        return $this->phpTag."\$this->startPush{$expression}; ?>";
    }

    /**
     * Compile the endpush statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndpush()
    {
        return $this->phpTag.'$this->stopPush(); ?>';
    }

    //</editor-fold>


    //<editor-fold desc="push">
    /**
     * Start injecting content into a push section.
     *
     * @param  string  $section
     * @param  string  $content
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
     * Stop injecting content into a push section.
     *
     * @return string
     */
    public function stopPush()
    {
        if (empty($this->pushStack)) {
            $this->showError('stopPush','Cannot end a section without first starting one',true);
        }

        $last = array_pop($this->pushStack);
        $this->extendPush($last, ob_get_clean());
        return $last;
    }

    /**
     * Append content to a given push section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendPush($section, $content)
    {
        if (! isset($this->pushes[$section])) {
            $this->pushes[$section] = [];
        }
        if (! isset($this->pushes[$section][$this->renderCount])) {
            $this->pushes[$section][$this->renderCount] = $content;
        } else {
            $this->pushes[$section][$this->renderCount] .= $content;
        }
    }

    /**
     * Get the string contents of a push section.
     *
     * @param  string  $section
     * @param  string  $default
     * @return string
     */
    public function yieldPushContent($section, $default = '')
    {
        if (! isset($this->pushes[$section])) {
            return $default;
        }

        return implode(array_reverse($this->pushes[$section]));
    }

    //</editor-fold>




    //<editor-fold desc="compile extras">
    /**
     * Store the verbatim blocks and replace them with a temporary placeholder.
     *
     * @param  string  $value
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
     * Convert an array such as ["class1"=>"myclass","style="mystyle"] to class1='myclass' style='mystyle' string
     * @param array|string $array array to convert
     * @return string
     */
    public function convertArg($array) {
        if (!is_array($array)) {
            return $array;  // nothing to convert.
        }
        return implode(' ',array_map( 'static::convertArgCallBack', array_keys($array), $array));

    }
    function convertArgCallBack($k, $v) {
        return $k."='{$v}' ";
    }
    /**
     * Replace the raw placeholders with the original code stored in the raw blocks.
     *
     * @param  string  $result
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
     *
     * @param  array  $token
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
     * Get the echo methods in the proper order for compilation.
     *
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
     *
     * @return string
     */
    public function yieldSection()
    {
        return $this->yieldContent($this->stopSection());
    }


    /**
     * Start injecting content into a section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    public function startSection($section, $content = '')
    {
        if ($content === '')
        {
            ob_start() && $this->sectionStack[] = $section;
        }
        else
        {
            $this->extendSection($section, $content);
        }
    }
    /**
     * Append content to a given section.
     *
     * @param  string  $section
     * @param  string  $content
     * @return void
     */
    protected function extendSection($section, $content)
    {
        if (isset($this->sections[$section]))
        {
            $content = str_replace('@parent', $content, $this->sections[$section]);

            $this->sections[$section] = $content;
        }
        else
        {
            $this->sections[$section] = $content;
        }
    }
    /**
     * Stop injecting content into a section.
     *
     * @param  bool  $overwrite
     * @return string
     */
    public function stopSection($overwrite = false)
    {
        $last = array_pop($this->sectionStack);

        if ($overwrite)
        {
            $this->sections[$last] = ob_get_clean();
        }
        else
        {
            $this->extendSection($last, ob_get_clean());
        }

        return $last;
    }
    /**
     * Get the string contents of a section.
     *
     * @param  string  $section
     * @param  string  $default
     * @return string
     */
    public function yieldContent($section, $default = '')
    {
        return isset($this->sections[$section]) ? $this->sections[$section] : $default;
    }

    /**
     * Compile the while statements into valid PHP.
     *
     * @param  string  $expression
     * @return string
     */
    protected function compileWhile($expression)
    {
        return $this->phpTag."while{$expression}: ?>";
    }

    /**
     * Strip the parentheses from the given expression.
     *
     * @param  string  $expression
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
     * Register a custom Blade compiler.
     *
     * @param  callable  $compiler
     * @return void
     */
    public function extend(callable $compiler)
    {
        $this->extensions[] = $compiler;
    }

    /**
     * Register a handler for custom directives.
     *
     * @param  string  $name
     * @param  callable  $handler
     * @return void
     */
    public function directive($name, callable $handler)
    {
        $this->customDirectives[$name] = $handler;
    }



    /**
     * Sets the content tags used for the compiler.
     *
     * @param  string  $openTag
     * @param  string  $closeTag
     * @param  bool    $escaped
     * @return void
     */
    public function setContentTags($openTag, $closeTag, $escaped = false)
    {
        $property = ($escaped === true) ? 'escapedTags' : 'contentTags';

        $this->{$property} = [preg_quote($openTag), preg_quote($closeTag)];
    }

    /**
     * Sets the escaped content tags used for the compiler.
     *
     * @param  string  $openTag
     * @param  string  $closeTag
     * @return void
     */
    public function setEscapedContentTags($openTag, $closeTag)
    {
        $this->setContentTags($openTag, $closeTag, true);
    }

    /**
     * Gets the content tags used for the compiler.
     *
     * @return string
     */
    public function getContentTags()
    {
        return $this->getTags();
    }

    /**
     * Gets the escaped content tags used for the compiler.
     *
     * @return string
     */
    public function getEscapedContentTags()
    {
        return $this->getTags(true);
    }

    /**
     * Gets the tags used for the compiler.
     *
     * @param  bool  $escaped
     * @return array
     */
    protected function getTags($escaped = false)
    {
        $tags = $escaped ? $this->escapedTags : $this->contentTags;

        return array_map('stripcslashes', $tags);
    }


    //</editor-fold>


    //<editor-fold desc="file members">
    /**
     * Get the full path of the compiled file.
     * @param string $fileName
     * @return string
     */
    public function getCompiledFile($fileName='') {
        $fileName=($fileName=='')?$this->fileName:$fileName;
        return $this->compiledPath.'/'.$fileName.' _'.sha1($fileName);
    }
    /**
     * Get the full path of the compiled file.
     * @return string
     */
    public function getTemplateFile() {
        $arr=explode('.',$this->fileName);
        $c=count($arr);
        if ($c==1) {
            return $this->templatePath . '/' . $this->fileName . '.blade.php';
        } else {
            $file=$arr[$c-1];
            array_splice($arr,$c-1,$c-1); // delete the last element
            $path=implode('/',$arr);
            return $this->templatePath . '/' .$path.'/'. $file . '.blade.php';
        }
    }
    /**
     * Determine if the view  is expired.
     *

     * @return bool
     */
    public function isExpired()
    {
        $compiled = $this->getCompiledFile();
        $template=$this->getTemplateFile();
        if (!file_exists($template)) {
            $this->showError("Read file","Template not found :".$this->fileName,true);
        }

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if ( ! $this->compiledPath || ! file_exists($compiled))
        {
            return true;
        }

        return filemtime($compiled) < filemtime($template);
    }

    /**
     * Get the contents of a file.
     *
     * @param $fileName
     * @return string
     */
    public function getFile($fileName)
    {
        if (is_file($fileName)) return file_get_contents($fileName);
        $this->showError('getFile',"File does not exist at path {$fileName}",true);
        return '';
    }

    /**
     * @param $compiledFile
     * @param $variables
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
        try
        {
            /** @noinspection PhpIncludeInspection */
            include $compiledFile;
        }
        catch (\Exception $e)
        {
            $this->handleViewException($e);
        }

        return ltrim(ob_get_clean());
    }

    /**
     * Handle a view exception.
     *
     * @param  \Exception  $e
     * @return void
     *
     * @throws $e
     */
    protected function handleViewException($e)
    {

        ob_get_clean(); throw $e;
    }
    //</editor-fold>




    //<editor-fold desc="Array Functions">
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        $accesible=is_array($array) || $array instanceof ArrayAccess;
        if (! $accesible) {
            return self::value($default);
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
                return self::value($default);
            }
        }

        return $array;
    }
    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
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
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? self::value($default) : reset($array);
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return self::value($default);
    }
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
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
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }


    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * Escape HTML entities in a string.
     *
     * @param  string  $value
     * @return string
     */
    public static function e($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }
    //</editor-fold>

    //<editor-fold desc="loop functions">
    /**
     * Add new loop to the stack.
     *
     * @param  array|\Countable  $data
     * @return void
     */
    public function addLoop($data)
    {
        $length = is_array($data) || $data instanceof Countable ? count($data) : null;

        $parent = static::last($this->loopsStack);

        $this->loopsStack[] = [
            'index' => 0,
            'remaining' => isset($length) ? $length + 1 : null,
            'count' => $length,
            'first' => true,
            'last' => isset($length) ? $length == 1 : null,
            'depth' => count($this->loopsStack) + 1,
            'parent' => $parent ? (object) $parent : null,
        ];
    }

    /**
     * Increment the top loop's indices.
     *
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
     *
     * @return void
     */
    public function popLoop()
    {
        array_pop($this->loopsStack);
    }

    /**
     * Get an instance of the first loop in the stack.
     *
     * @return array
     */
    public function getFirstLoop()
    {
        return ($last = static::last($this->loopsStack)) ? (object) $last : null;
    }

    /**
     * Get the entire loop stack.
     *
     * @return array
     */
    public function getLoopStack()
    {
        return $this->loopsStack;
    }
    /**
     * Get the rendered contents of a partial from a loop.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  string  $iterator
     * @param  string  $empty
     * @return string
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

                $result .= $this->runChild($view,$data);
            }
        }

        // If there is no data in the array, we will render the contents of the empty
        // view. Alternatively, the "empty view" could be a raw string that begins
        // with "raw|" for convenience and to let this know that it is a string.
        else {
            //todo: pendiente
            if (static::startsWith($empty, 'raw|')) {
                $result = substr($empty, 4);
            } else {
                $result = $this->run($empty,array());
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
    public function showError($id,$text,$critic=false) {
        ob_get_clean();
        echo "<div style='background-color: red; color: black; padding: 3px; border: solid 1px black;'>";
        echo "BladeOne Error [{$id}]:<br>";
        echo "<span style='color:white'>$text</span><br></div>\n";
        if ($critic) {
            die(1);
        }
        return "";
    }

    //</editor-fold>



}