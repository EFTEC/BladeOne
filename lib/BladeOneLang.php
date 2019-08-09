<?php

namespace eftec\bladeone;

/**
 * Trait BladeOneLang
 * It adds the next tags
 * <code>
 * select:
 * @ _e('hello')
 * @ _n('Product','Products',$n)
 * @ _ef('hello %s',$user)
 * </code>
 *
 * @package    eftec\bladeone
 * @version    1.1 2019-08-09
 * @link       https://github.com/EFTEC/BladeOne
 * @author     Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 * @copyright  2017 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * @deprecated Note: It is not needing anymore (BladeOne already includes the same functionalities). It is keep for compatibility purpose.
 */
trait BladeOneLang
{
    /** @var string The path to the missing translations log file. If empty then every missing key is not saved. */
    public $missingLog = '';

    /** @var array Hold dictionary of translations */
    public static $dictionary = [];

    /**
     * Tries to translate the word if its in the array defined by BladeOneLang::$dictionary
     * If the operation fails then, it returns the original expression without translation.
     *
     * @param $phrase
     *
     * @return string
     */
    public function _e($phrase)
    {
        if ((!\array_key_exists($phrase, static::$dictionary))) {
            $this->missingTranslation($phrase);
            return $phrase;
        } else {
            return static::$dictionary[$phrase];
        }
    }

    /**
     * Its the same than @_e, however it parses the text (using sprintf).
     * If the operation fails then, it returns the original expression without translation.
     *
     * @param $phrase
     *
     * @return string
     */
    public function _ef($phrase)
    {
        $argv = \func_get_args();
        $r = $this->_e($phrase);
        $argv[0] = $r; // replace the first argument with the translation.
        $result = @\call_user_func_array("sprintf", $argv);
        $result = ($result === false) ? $r : $result;
        return $result;
    }

    /**
     * if num is more than one then it returns the phrase in plural, otherwise the phrase in singular.
     * Note: the translation should be as follow: $msg['Person']='Person' $msg=['Person']['p']='People'
     *
     * @param string $phrase
     * @param string $phrases
     * @param int    $num
     *
     * @return string
     */
    public function _n($phrase, $phrases, $num = 0)
    {
        if ((!\array_key_exists($phrase, static::$dictionary))) {
            $this->missingTranslation($phrase);
            return ($num <= 1) ? $phrase : $phrases;
        } else {
            return ($num <= 1) ? $this->_e($phrase) : $this->_e($phrases);
        }
    }

    //<editor-fold desc="compile">

    /**
     * Used for @_e directive.
     *
     * @param $expression
     *
     * @return string
     */
    protected function compile_e($expression)
    {
        return $this->phpTag . "echo \$this->_e{$expression}; ?>";
    }

    /**
     * Used for @_ef directive.
     *
     * @param $expression
     *
     * @return string
     */
    protected function compile_ef($expression)
    {
        return $this->phpTag . "echo \$this->_ef{$expression}; ?>";
    }

    /**
     * Used for @_n directive.
     *
     * @param $expression
     *
     * @return string
     */
    protected function compile_n($expression)
    {
        return $this->phpTag . "echo \$this->_n{$expression}; ?>";
    }

    //</editor-fold>

    /**
     * Log a missing translation into the file $this->missingLog.<br>
     * If the file is not defined, then it doesn't write the log.
     *
     * @param string $txt Message to write on.
     */
    private function missingTranslation($txt)
    {
        if (!$this->missingLog) {
            return; // if there is not a file assigned then it skips saving.
        }

        $fz = @\filesize($this->missingLog);
        $mode = 'a';

        if (\is_object($txt) || \is_array($txt)) {
            $txt = \print_r($txt, true);
        }

        // Rewrite file if more than 100000 bytes
        if ($fz > 100000) {
            $mode = 'w';
        }

        $fp = \fopen($this->missingLog, 'w');
        \fwrite($fp, $txt . "\n");
        \fclose($fp);
    }
}
