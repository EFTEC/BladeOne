<?php

/**
 * Class BladeOneLogic
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Extends the tags of the class BladeOne.  Its optional
 * It adds the next tags
 * <code>
 * @ switch($variable)
 * @ case(option1)
 *  ...
 * @ case(option2)
 *  ...
 * @ defaultcase()
 *  ...
 * @ endswitch()
 * </code>
 * NOTE: Its not compatible with nested switches
 * @package  BladeOneLogic
 * @version 1.2 2016-06-10
 * @link https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */

namespace eftec\bladeone;

class BladeOneLogic extends BladeOneHtml
{
    private $switchCount=0;
    //<editor-fold desc="compile function">
    public function compileSwitch($expression) {
        $this->switchCount=0;
        return $this->phpTag."switch($expression) { ?>";
    }
    public function compileCase($expression) {
        if ($this->switchCount!=0) {
            return $this->phpTag."break;\ncase $expression: ?>";
        }
        $this->switchCount++;
        return $this->phpTag."case $expression: ?>";
    }
    public function compileDefaultCase() {
        if ($this->switchCount!=0) {
            return $this->phpTag."break;\ndefault: ?>";
        }
        $this->switchCount++;
        return $this->phpTag."default: ?>";
    }
    public function compileEndSwitch() {
        $this->switchCount=0;
        return $this->phpTag."} // end switch ?>";
    }
    //</editor-fold>
}