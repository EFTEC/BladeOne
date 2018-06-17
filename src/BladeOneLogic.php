<?php

/**
 * trait BladeOneLogic
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
 * @version 1.4 2016-06-25
 * @link https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */

namespace eftec\bladeone;

trait BladeOneLogic
{
    /** @var int Indicates the number of open switches */
    private $switchCount=0;
    /** @var bool Indicates if the switch is recently open */
    private $switchFirst=true;
    //<editor-fold desc="compile function">
    public function compileSwitch($expression) {
        $this->switchCount++;
        return $this->phpTag."switch($expression) { ?>";
    }
    public function compileCase($expression) {
        if ($this->switchFirst) {
            $this->switchFirst=false;
            return $this->phpTag."case $expression: ?>";
        }
        return $this->phpTag."break;\n case $expression: ?>";
    }
    public function compileDefaultCase() {
        if ($this->switchFirst) {
            return $this->showError("@defaultcase","@switch without any @case",true);
        }
        return $this->phpTag."break;\n default: ?>";
    }
    public function compileEndSwitch() {
        $this->switchCount=$this->switchCount-1;
        if ($this->switchCount<0) {
            return $this->showError("@endswitch","Missing @switch",true);
        }
        return $this->phpTag."} // end switch ?>";
    }
    //</editor-fold>
}