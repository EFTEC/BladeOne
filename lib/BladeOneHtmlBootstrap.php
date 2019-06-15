<?php

namespace eftec\bladeone;

/**
 * trait BladeOneHtmlBootstrap
 * Copyright (c) 2016 Jorge Patricio Castro Castillo MIT License. Don't delete this comment, its part of the license.
 * Extends the tags of the class BladeOne.  Its optional
 * It adds the next tags
 * <code>
 * select:
 * @ select('idCountry','value',[,$extra])
 * @ item('0','--select a country'[,$extra])
 * @ items($countries,'id','name',$currentCountry[,$extra])
 * @ endselect()
 * input:
 * @ input('iduser',$currentUser,'text'[,$extra])
 * button:
 * @ commandbutton('idbutton','value','text'[,$extra])
 *
 * </code>
 * Note. The names of the tags are based in Java Server Faces (JSF)
 *
 * @package  BladeOneHtmlBootstrap
 * @version  1.9.1 2018-06-11 (1)
 * @link     https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */
trait BladeOneHtmlBootstrap
{
    use BladeOneHtml {
        BladeOneHtml::select as selectParent;
        BladeOneHtml::input as inputParent;
        BladeOneHtml::commandButton as commandButtonParent;
        BladeOneHtml::textArea as textAreaParent;
        BladeOneHtml::item as itemParent;
        BladeOneHtml::checkbox as checkboxParent;
        BladeOneHtml::compileEndCheckbox as compileEndCheckboxParent;
        BladeOneHtml::radio as radioParent;
        BladeOneHtml::compileEndRadio as compileEndRadioParent;
    }

    //<editor-fold desc="Override methods">
    public function select($name, $value, $extra = '')
    {
        $extra = $this->addClass($extra, 'form-control');
        return $this->selectParent($name, $value, $extra);
    }

    public function input($id, $value = '', $type = 'text', $extra = '')
    {
        $extra = $this->addClass($extra, 'form-control');
        return $this->inputParent($id, $value, $type, $extra);
    }

    public function commandButton($id, $value = '', $text = 'Button', $type = 'submit', $extra = '')
    {
        $extra = $this->addClass($extra, 'btn');
        return $this->commandButtonParent($id, $value, $text, $type, $extra);
    }

    public function textArea($id, $value = '', $extra = '')
    {
        $extra = $this->addClass($extra, 'form-control');
        return $this->textAreaParent($id, $value, $extra);
    }


    public function file($id, $fullfilepath = '', $file = '', $extra = '')
    {
        return "<input id='" . static::e($id) . "_file' name='" . static::e($id) . "_file' type='hidden' value='" . static::e($file) . "' />
        <div class='input-group'>
        <label class='input-group-btn'>
            <span class='btn btn-primary'>
            Browse&hellip;<a href='$fullfilepath' class='afile' ><i class='fa fa-paperclip'></i></a> 
            <input type='file' style='display: none;' id='" . static::e($id) . "' name='" . static::e($id) . "' $extra />
            </span>
        </label>
        <input type='text' class='form-control' readonly></div>";
        // return "<a href='$fullfilepath'>$file</a>
        //<input id='".static::e($id)."_file' name='".static::e($id)."_file' type='hidden' value='".static::e($file)."' />
        // <input id='".static::e($id)."' name='".static::e($id)."' type='file' ".$this->convertArg($extra)." value='".static::e($fullfilepath)."' />\n";
    }

    /**
     * @param string $id           of the field
     * @param string $fullfilepath full file path of the image
     * @param string $file         filename of the file
     * @param string $extra        extra field of the input file
     * @return string html
     */
    public function image($id, $fullfilepath = '', $file = '', $extra = '')
    {
        return "<input id='" . static::e($id) . "_file' name='" . static::e($id) . "_file' type='hidden' value='" . static::e($file) . "' />
        <img src='$fullfilepath' class='img-thumbnail' />
        <div class='input-group'>
        
        <label class='input-group-btn'>
            <span class='btn btn-primary'>
            Browse&hellip; 
            <input type='file' style='display: none;' id='" . static::e($id) . "' name='" . static::e($id) . "' $extra />
            </span>
        </label>
        <input type='text' class='form-control' readonly></div>";
        // return "<a href='$fullfilepath'>$file</a>
        //<input id='".static::e($id)."_file' name='".static::e($id)."_file' type='hidden' value='".static::e($file)."' />
        // <input id='".static::e($id)."' name='".static::e($id)."' type='file' ".$this->convertArg($extra)." value='".static::e($fullfilepath)."' />\n";
    }

    /**
     * @param string       $type         type of the current open tag
     * @param array|string $valueId      if is an array then the first value is used as value, the second is used as
     *                                   extra
     * @param              $valueText
     * @param array|string $selectedItem Item selected (optional)
     * @param string       $wrapper      Wrapper of the element.  For example, <li>%s</li>
     * @param string       $extra
     * @return string
     * @internal param string $fieldId Field of the id
     * @internal param string $fieldText Field of the value visible
     */
    public function item($type, $valueId, $valueText, $selectedItem = '', $wrapper = '', $extra = '')
    {
        $id = @\end($this->htmlCurrentId);
        $wrapper = ($wrapper == '') ? '%s' : $wrapper;

        if (\is_array($selectedItem)) {
            $found = \in_array($valueId, $selectedItem);
        } else {
            if (\is_null($selectedItem)) {
                // diferentiate null = '' != 0
                $found = $valueId === '' || $valueId === null;
            } else {
                $found = $selectedItem == $valueId;
            }
        }
        $valueHtml = (!\is_array($valueId)) ? "value='{$valueId}'" : "value='{$valueId[0]}' data='{$valueId[1]}'";
        switch ($type) {
            case 'select':
                $selected = ($found) ? 'selected' : '';
                return \sprintf($wrapper, "<option $valueHtml $selected " .
                    $this->convertArg($extra) . ">{$valueText}</option>\n");
                break;
            case 'radio':
                $selected = ($found) ? 'checked' : '';
                return \sprintf($wrapper, "<label><input type='radio' id='" . static::e($id)
                    . "' name='" . static::e($id) . "' $valueHtml $selected "
                    . $this->convertArg($extra) . "> {$valueText}</label>\n");
                break;
            case 'checkbox':
                $selected = ($found) ? 'checked' : '';
                return \sprintf($wrapper, "<label><input type='checkbox' id='" . static::e($id)
                    . "' name='" . static::e($id) . "' $valueHtml $selected "
                    . $this->convertArg($extra) . "> {$valueText}</label>\n");
                break;

            default:
                return '???? type undefined: [$type] on @item<br>';
        }
    }

    public function checkbox($id, $value = '', $text = '', $valueSelected = '', $extra = '')
    {
        $num = \func_num_args();
        if ($num > 2) {
            if ($value == $valueSelected) {
                if (\is_array($extra)) {
                    $extra['checked'] = 'checked';
                } else {
                    $extra .= ' checked="checked"';
                }
            }
            //return '<div class="checkbox"><label>'.$this->inputParent($id, $value, 'checkbox', $extra) . ' ' . $text.'</label></div>';
            return '<div><label>' . $this->inputParent($id, $value, 'checkbox', $extra) . ' ' . $text . '</label></div>';
        } else {
            \array_push($this->htmlCurrentId, $id);
            return '<div>';
            //return '<div class="checkbox">';
        }
    }

    public function radio($id, $value = '', $text = '', $valueSelected = '', $extra = '')
    {
        $num = \func_num_args();
        if ($num > 2) {
            if ($value == $valueSelected) {
                if (\is_array($extra)) {
                    $extra['checked'] = 'checked';
                } else {
                    $extra .= ' checked="checked"';
                }
            }
            return '<div class="radio"><label>' . $this->inputParent($id, $value, 'radio', $extra) . ' ' . $text . '</label></div>';
        } else {
            \array_push($this->htmlCurrentId, $id);
            return '<div class="radio">';
        }
    }

    public function compileEndCheckbox()
    {
        $r = $this->compileEndCheckboxParent();
        $r .= '</div>';
        return $r;
    }

    public function compileEndRadio()
    {
        $r = $this->compileEndRadioParent();
        $r .= '</div>';
        return $r;
    }
    //</editor-fold>

    //<editor-fold desc="Misc members">

    /**
     * It adds a class to a html tag parameter
     *
     * @example addClass('type="text" class="btn","btn-standard")
     * @param string|array $txt
     * @param string       $newclass The class(es) to add, example "class1" or "class1 class"
     * @return string|array
     */
    protected function addClass($txt, $newclass)
    {
        if (\is_array($txt)) {
            $txt = \array_change_key_case($txt);
            @$txt['class'] = ' ' . $newclass;
            return $txt;
        }
        $p0 = \stripos(' ' . $txt, ' class');
        if ($p0 === false) {
            // if the content of the tag doesn't contain a class then it adds one.
            return $txt . ' class="' . $newclass . '"';
        }
        // the class tag exists so we found the closes character ' or " and we add the class (or classes) inside it
        // may be it could duplicates the tag.
        $p1 = \strpos($txt, "'", $p0);
        $p2 = \strpos($txt, '"', $p0);
        $p1 = ($p1 === false) ? 99999 : $p1;
        $p2 = ($p2 === false) ? 99999 : $p2;

        if ($p1 < $p2) {
            return \substr_replace($txt, $newclass . ' ', $p1 + 1, 0);
        } else {
            echo $p2 . "#";
            return \substr_replace($txt, $newclass . ' ', $p2 + 1, 0);
        }
    }

    protected function separatesParam($txt)
    {
        $result = [];
        \preg_match_all("~\"[^\"]++\"|'[^']++'|\([^)]++\)|[^,]++~", $txt, $result);
        return $result;
    }
    //</editor-fold>
}
