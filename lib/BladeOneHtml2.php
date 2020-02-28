<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpUnused */

namespace eftec\bladeone;

/**
 * trait BladeOneHtml
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
 * @package  BladeOneHtml2
 * @version  1.0 2020-02-22 (1)
 * @link     https://github.com/EFTEC/BladeOne
 * @author   Jorge Patricio Castro Castillo <jcastro arroba eftec dot cl>
 */
trait BladeOneHtml2
{
    public $selectattern = "<option {{fieldvalue}} {{name}} {{class}}>{{fielddisplay}}</option>{{end}}\n"; // indicates the type of the current tag. such as select/selectgroup/etc.
    public $selectPatternChecked = "<option {{fieldvalue}} selected=\"selected\" {{name}} {{class}}>{{fielddisplay}}</option>{{end}}\n"; //indicates the id of the current tag.
    public $checkBoxPattern = "<input type=\"checkbox\" {{fieldvalue}} {{name}} {{class}}>{{fielddisplay}}</input>{{end}}\n";
    public $checkBoxPatternChecked = "<input type=\"checkbox\" {{fieldvalue}} checked=\"checked\" {{name}} {{class}}>{{fielddisplay}}</input>{{end}}\n";
    public $radioPattern = "<input type=\"radio\" {{fieldvalue}} {{name}} {{class}}>{{fielddisplay}}</input>{{end}}\n";
    public $radioPatternChecked = "<input type=\"radio\" {{fieldvalue}} checked=\"checked\" {{name}} {{class}}>{{fielddisplay}}</input>{{end}}\n";
    protected $htmlItem = [];
    protected $htmlCurrentId = [];

    public $genPattern = "<{{tag}} {{attrs}} {{fieldvalue}} {{extra}}>{{fielddisplay}}{{inner}}{{content}}</{{tag}}>{{end}}\n"; // indicates the type of the current tag. such as select/selectgroup/etc.
    public $genPatternOpen = "<{{tag}} {{attrs}} {{extra}}>{{inner}}{{content}}{{end}}\n"; // indicates the type of the current tag. such as select/selectgroup/etc.

    //<editor-fold desc="compile function">
    protected $paginationStructure
        = [
            'selHtml' => '<li class="selected" %3s><a href="%1s">%2s</a></li>'
            ,
            'html' => '<li %3s><a href="%1s">%2s</a></li>'
            ,
            'maxItem' => 5
            ,
            'url' => ''
        ];

    public function select($name, $value, $extra = '')
    {
        if (\strpos($extra, 'readonly') === false) {
            return "<select id='" . static::e($name) . "' name='" . static::e($name)
                . "' {$this->convertArg($extra)}>\n";
        } else {
            return "
                <input id='" . static::e($name) . "' name='" . static::e($name) . "' type='hidden' value='"
                . static::e($value) . "' />
                <select id='" . static::e($name) . "_disable' name='" . static::e($name)
                . "_disable' disabled {$this->convertArg($extra)}>\n";
        }
    }
    public function compileInput($expression)
    {
        $args = $this->parseArg2($this->stripParentheses($expression));

        $txt = $this->genericTag($this->genPattern, $args, 'input', true);
        return $txt;
    }
    public function compileTextArea($expression)
    {
        $args = $this->parseArg2($this->stripParentheses($expression));
        //$args['inner']=$this->transform($args['value'],'php');
        $args['content']=$args['value'];
        $txt = $this->genericTag($this->genPattern, $args, 'textarea', true);
        return $txt;
    }

    
    //  ****************************************************************
    
    public function link($url, $label, $extra = '')
    {
        return "<a href='{$url}' {$this->convertArg($extra)}>{$label}</a>";
    }

    public function listboxes($name, $allvalues, $fieldId, $fieldText, $selectedId, $extra = '')
    {
        $html = "";
        $html .= "<table>\n";
        $html .= "    <tr>\n";
        $html .= "	    <td>\n";
        $html .= "		    <select id='{$name}_noselected' size='6' multiple='multiple' $extra>\n";
        if (\count($allvalues) == 0) {
            $allvalues = [];
        }
        $html2 = "";
        foreach ($allvalues as $v) {
            if (\is_object($v)) {
                $v = (array)$v;
            }
            if (!$this->listboxesFindArray($v[$fieldId], $selectedId, $fieldId)) {
                $html .= "<option value='" . $v[$fieldId] . "'>" . $v[$fieldText] . "</option>\n";
            } else {
                $html2 .= "<option value='" . $v[$fieldId] . "'>" . $v[$fieldText] . "</option>\n";
            }
        }
        $html .= "			</select>\n";
        $html .= "		</td>\n";
        $html .= "		<td style='text-align:center;'>\n";
        $html .= "            <input type='button' value='>' id='{$name}_add'/><br>\n";
        $html .= "            <input type='button' value='>>' id='{$name}_addall'/><br>\n";
        $html .= "            <input type='button' value='<' id='{$name}_delete'/><br>\n";
        $html .= "            <input type='button' value='<<' id='{$name}_deleteall'/><br>\n";
        $html .= "		</td>\n";
        $html .= "		<td>\n";
        $html .= "			<select id='{$name}' name='{$name}' size='6' multiple='multiple'>\n";
        $html .= $html2;
        $html .= "			</select>\n";
        $html .= "		</td>\n";
        $html .= "	</tr>\n";
        $html .= "</table>\n";
        return $html;
    }

    /**
     * Find an element in a array of arrays
     * If the element doesn't exist in the array then it returns false, otherwise returns true
     *
     * @param string $find
     * @param array  $array array of primitives or objects
     * @param string $field field to search
     *
     * @return bool
     */
    private function listboxesFindArray($find, $array, $field)
    {
        if (\count($array) == 0) {
            return false;
        }
        if (!\is_array($array[0])) {
            return \in_array($find, $array);
        } else {
            foreach ($array as $elem) {
                if ($elem[$field] == $find) {
                    return true;
                }
            }
        }
        return false;
    }

    public function selectGroup($name, $extra = '')
    {
        return $this->selectGroup($name, $extra);
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
            return $this->input($id, $value, 'radio', $extra) . ' ' . $text;
        } else {
            \array_push($this->htmlCurrentId, $id);
            return '';
        }
    }



    /**
     * @param             $id
     * @param string      $value
     * @param string      $text
     * @param string|null $valueSelected
     * @param string      $extra
     *
     * @return string
     */
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
            return $this->input($id, $value, 'checkbox', $extra) . ' ' . $text;
        } else {
            \array_push($this->htmlCurrentId, $id);
            return '';
        }
    }

    /**
     * @param string       $type            type of the current open tag
     * @param array        $arrValues       Array of objects/arrays to show.
     * @param string       $fieldId         Field of the id (for arrValues)
     * @param string       $fieldText       Field of the id of selectedItem
     * @param array|string $selectedItem    Item selected (optional)
     * @param string       $selectedFieldId field of the selected item.
     * @param string       $wrapper         Wrapper of the element.  For example, <li>%s</li>
     * @param string       $extra           (optional) is used for add additional information for the html object (such
     *                                      as class)
     *
     * @return string
     * @version 1.1 2017
     */
    public function items(
        $type,
        $arrValues,
        $fieldId,
        $fieldText,
        $selectedItem = '',
        $selectedFieldId = '',
        $wrapper = '',
        $extra = ''
    ) {
        if (\count($arrValues) == 0) {
            return "";
        }

        if (\is_object(@$arrValues[0])) {
            $arrValues = (array)$arrValues;
        }
        if (\is_array($selectedItem)) {
            if (\is_object(@$selectedItem[0])) {
                $primitiveArray = [];
                foreach ($selectedItem as $v) {
                    $primitiveArray[] = $v->{$selectedFieldId};
                }
                $selectedItem = $primitiveArray;
            }
        }
        $result = '';
        if (\is_object($selectedItem)) {
            $selectedItem = (array)$selectedItem;
        }
        foreach ($arrValues as $v) {
            if (\is_object($v)) {
                $v = (array)$v;
            }
            $result .= $this->item($type, $v[$fieldId], $v[$fieldText], $selectedItem, $wrapper, $extra);
        }
        return $result;
    }

    /**
     * @param string       $type         type of the current open tag
     * @param array|string $valueId      if is an array then the first value is used as value, the second is used as
     *                                   extra
     * @param              $valueText
     * @param array|string $selectedItem Item selected (optional)
     * @param string       $wrapper      Wrapper of the element.  For example, <li>%s</li>
     * @param string       $extra
     *
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
            $found = $valueId == $selectedItem;
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
                return \sprintf($wrapper, "<input type='radio' id='" . static::e($id)
                    . "' name='" . static::e($id) . "' $valueHtml $selected "
                    . $this->convertArg($extra) . "> {$valueText}\n");
                break;
            case 'checkbox':
                $selected = ($found) ? 'checked' : '';
                return \sprintf($wrapper, "<input type='checkbox' id='" . static::e($id)
                    . "' name='" . static::e($id) . "' $valueHtml $selected "
                    . $this->convertArg($extra) . "> {$valueText}\n");
                break;

            default:
                return '???? type undefined: [$type] on @item<br>';
        }
    }

    /**
     * @param string       $type         type of the current open tag
     * @param array        $arrValues    Array of objects/arrays to show.
     * @param string       $fieldId      Field of the id
     * @param string       $fieldText    Field of the value visible
     * @param string       $fieldThird
     * @param array|string $selectedItem Item selected (optional)
     * @param string       $wrapper      Wrapper of the element.  For example, <li>%s</li>
     * @param string       $extra        (optional) is used for add additional information for the html object (such as
     *                                   class)
     *
     * @return string
     * @version 1.0
     */
    public function trios(
        $type,
        $arrValues,
        $fieldId,
        $fieldText,
        $fieldThird,
        $selectedItem = '',
        $wrapper = '',
        $extra = ''
    ) {
        if (\count($arrValues) == 0) {
            return "";
        }
        if (\is_object($arrValues[0])) {
            $arrValues = (array)$arrValues;
        }
        $result = '';
        $oldV3 = "";
        foreach ($arrValues as $v) {
            if (\is_object($v)) {
                $v = (array)$v;
            }
            $v3 = $v[$fieldThird];
            if ($type == 'selectgroup') {
                if ($v3 != $oldV3) {
                    if ($oldV3 != "") {
                        $result .= "</optgroup>";
                    }
                    $oldV3 = $v3;
                    $result .= "<optgroup label='{$v3}'>";
                }
            }
            if ($result) {
                $result .= $this->trio($type, $v[$fieldId], $v[$fieldText], $v3, $selectedItem, $wrapper, $extra);
            }
        }
        if ($type == 'selectgroup' && $oldV3 != "") {
            $result .= "</optgroup>";
        }
        return $result;
    }

    /**
     * @param string       $type         type of the current open tag
     * @param string       $valueId      value of the trio
     * @param string       $valueText    visible value of the trio.
     * @param string       $value3       extra third value for select value or visual
     * @param array|string $selectedItem Item selected (optional)
     * @param string       $wrapper      Wrapper of the element.  For example, <li>%s</li>
     * @param string       $extra
     *
     * @return string
     * @internal param string $fieldId Field of the id
     * @internal param string $fieldText Field of the value visible
     */
    public function trio($type, $valueId, $valueText, $value3 = '', $selectedItem = '', $wrapper = '', $extra = '')
    {
        $id = @\end($this->htmlCurrentId);
        $wrapper = ($wrapper == '') ? '%s' : $wrapper;
        if (\is_array($selectedItem)) {
            $found = \in_array($valueId, $selectedItem);
        } else {
            $found = $valueId == $selectedItem;
        }
        switch ($type) {
            case 'selectgroup':
                $selected = ($found) ? 'selected' : '';
                return \sprintf($wrapper, "<option value='{$valueId}' $selected " .
                    $this->convertArg($extra) . ">{$valueText}</option>\n");
                break;
            default:
                return '???? type undefined: [$type] on @item<br>';
        }
    }

    public function pagination($id, $curPage, $maxPage, $baseUrl, $extra = '')
    {
        $r = "<ul $extra>";

        $r .= "</ul>";
        return $r;
    }

    public function file($id, $fullfilepath = '', $file = '', $extra = '')
    {
        return "<a href='$fullfilepath'>$file</a>
        <input id='" . static::e($id) . "_file' name='" . static::e($id) . "_file' type='hidden' value='"
            . static::e($file) . "' />
        <input id='" . static::e($id) . "' name='" . static::e($id) . "' type='file' " . $this->convertArg($extra)
            . " value='" . static::e($fullfilepath) . "' />\n";
    }

    public function textArea($id, $value = '', $extra = '')
    {
        $value = \str_replace('\n', "\n", $value);
        return "<textarea id='" . static::e($id) . "' name='" . static::e($id) . "' " . $this->convertArg($extra)
            . " >$value</textarea>\n";
    }

    public function hidden($id, $value = '', $extra = '')
    {
        return $this->input($id, $value, 'hidden', $extra);
    }



    public function commandButton($id, $value = '', $text = 'Button', $type = 'submit', $extra = '')
    {
        return "<button type='{$type}' id='" . static::e($id) . "' name='" . static::e($id) . "' value='"
            . static::e($value) . "' {$this->convertArg($extra)}>{$text}</button>\n";
    }

    public function form($action, $method = 'post', $extra = '')
    {
        return "<form $action='{$action}' method='{$method}' {$this->convertArg($extra)}>";
    }

    protected function compileSelect($expression)
    {
        $args = $this->parseArg2($this->stripParentheses($expression));

        \array_push($this->htmlItem, [
            'select'
            ,
            $args['name']
            ,
            (isset($args['value'])) ? $args['value'] : 'null'
        ]);

        $txt = $this->genericTag($this->genPatternOpen, $args, 'select', false);
        return $txt;
    }

    /**
     * It transforms a text = 'a1=1,a2=2' into an associative array<br/>
     * It uses the method parse_str() to do the conversion<br/>
     *
     * @param string $text The input string with the initial values
     *
     * @return array An associative array
     */
    private function parseArg($text)
    {
        $tmpToken = '¶|¶';
        $output = [];

        $parsR = str_replace(['&', ','], [$tmpToken, '&'], $text);
        parse_str($parsR, $output);
        foreach ($output as $id => &$k) {
            $k = trim(str_replace($tmpToken, '&', $k));
        }
        return $output;
    }
    /**
     * It's the same than parseArg() but it's x3 times slower.<br>
     * It also considers quotes and doubles quotes.<br>
     * Example:
     * <pre>
     * Text::parseArg2("a1=1,a2=2,a3="aa,bb"); // ["a1"=>1,"a2"=>2,"a3"=>""aa,bb""]
     * Text::parseArg("a1=1,a2=2,a3="aa,bb"); // ["a1"=>1,"a2"=>2,"a3"=>""aa","bb""=>""]
     * </pre>
     *
     * @param string $text      The input string with the initial values
     * @param string $separator The separator. It does not separates text inside quotes or double-quotes.
     *
     * @return array An associative array
     */
    public function parseArg2($text, $separator = ',')
    {
        $chars = str_split($text);
        $parts = [];
        $nextpart = "";
        $strL = count($chars);
        for ($i = 0; $i < $strL; $i++) {
            $char = $chars[$i];
            if ($char == '"' || $char == "'") {
                $inext = strpos($text, $char, $i + 1);
                $inext = $inext === false ? $strL : $inext;
                $nextpart .= substr($text, $i, $inext - $i + 1);
                $i = $inext;
            } else {
                $nextpart .= $char;
            }
            if ($char == $separator) {
                $parts[] = substr($nextpart, 0, -1);
                $nextpart = "";
            }
        }
        if (strlen($nextpart) > 0) {
            $parts[] = $nextpart;
        }
        $result = [];
        foreach ($parts as $part) {
            $r = explode('=', $part, 2);
            if (count($r) == 2) {
                $result[trim($r[0])] = trim($r[1]);
            }
        }
        return $result;
    }
    public $attrsKnow=['id'=>'echo','name'=>'echo','for'=>'echo'
                ,'placeholder'=>'echo'
                ,'type'=>'echo','value'=>'echo','class'=>'echo'
                ,'fieldvalue'=>'echo','fielddisplay'=>'echo'];

    /**
     *
     * @param       $pattern "<{{tag}} {{attrs}} {{extra}}>{{inner}}{{content}}</{{tag}}>{{end}}\n"
     * @param       $args
     * @param       $type
     * @param bool  $selfClose
     * @param array $required
     *
     * @return string
     */
    private function genericTag($pattern, $args, $type, $selfClose = false, $required = [])
    {
        $argF['tag']=$type;
        if (isset($args['idname'])) {
            $args['id'] = $args['idname'];
            $args['name'] = $args['idname'];
        }
        $php="";
        foreach ($args as $key => $arg) {
            if (key_exists($key, $this->attrsKnow) !==false) {
                $php .= $key.'=' . $this->transform($arg, $this->attrsKnow[$key]) . ' ';
            } else {
                //todo: $php .= $key.'=' . $this->transform($arg, 'echonoescape') . ' ';
            }
        }
        $argF['attrs']=$php;

        // extra is also special because it is parsed as if (inside the tag)
        if (isset($args['extra'])) {
            $argF['extra']=addslashes($this->stripQuotes($args['extra'])). ' ';
        }
        // inner and content are special but inner is never escaped while content is escaped.
        // for textarea, value is displayed in content
        if (isset($args['inner'])) {
            //$inner =$this->transform($args['inner'], 'inner') . ' ';
            $argF['inner']=$this->transform($args['inner'], 'inner');
        }
        if (isset($args['content'])) {
            //$inner =$this->transform($args['inner'], 'inner') . ' ';
            $argF['content']= $this->transform($args['content'], 'innerescape') . ' ';
        }
        // end is never escaped
        if (isset($args['end'])) {
            $argF['end']=$this->transform($this->stripQuotes($args['end']), 'end');
        }
        $php=$this->phpTag.' echo "'.$this->replaceCurlyVariable($pattern, $argF).'";?>';
       

        return $php;
    }

    /**
     * @param mixed  $variable
     * @param string $type =['echo','php','none','inner','innerescape','echonoescape'][$i]
     *
     * @return string
     */
    public function transform($variable, $type)
    {
        if (empty($variable)) {
            return $variable;
        }
        if ($this->hasQuotes($variable)) {
            // "HELLO",'HELLO'
            switch ($type) {
                case 'inner':
                    //return '".static::e(' . $variable . ')."';
                    return addslashes($this->stripQuotes($variable));
                    //return '"'.$variable .'"';
                case 'innerescape':
                    return '".static::e(' . $variable . ')."';
                case 'echo':
                    return '\"".static::e(' . $variable . ')."\"';
                case 'echonoescape':
                    return '\"' . htmlspecialchars($this->stripQuotes($variable)) . '\"';
                    //return '\"".' . $variable . '."\"';
                //return '\"' .$this->stripQuotes($variable) . '\"';
                case 'php':
                    return $variable;
            }
            return addslashes($variable);
        } else {
            if (substr($variable, 0, 1) == '$') {
                // $HELLO
                switch ($type) {
                    case 'inner':
                    case 'innerescape':
                        return '".static::e(' . $variable . ')."';
                    case 'echo':
                        return '\"".static::e(' . $variable . ')."\"';
                    case 'echonoescape':
                        return '\"".(' . addslashes($variable) . ')."\"';
                    case 'php':
                        return $variable;
                }
                return addslashes($variable);
            } else {
                // HELLO
                switch ($type) {
                    case 'echo':
                        return $variable;
                    case 'php':
                        return "'" . addslashes($variable) . "'";
                }
                return addslashes($variable);
            }
        }
    }

    public function hasQuotes($text)
    {
        if (substr($text, 0, 1) === '"' && substr($text, -1) === '"') {
            return true;
        }
        if (substr($text, 0, 1) === "'" && substr($text, -1) === "'") {
            return true;
        }
        return false;
    }

    protected function compileCheckboxes($expression)
    {
        $args = $this->parseArg2($this->stripParentheses($expression));
        \array_push($this->htmlItem, [
            'checkbox'
            ,
            $args['name']
            ,
            (isset($args['value'])) ? $args['value'] : 'null'
        ]);
        $txt = "\n"; //$this->genericTag($args, 'select', false);
        return $txt;
    }
 
    protected function compileRadios($expression)
    {
        $args = $this->parseArg2($this->stripParentheses($expression));
        \array_push($this->htmlItem, [
            'radio'
            ,
            $args['name']
            ,
            (isset($args['value'])) ? $args['value'] : 'null'
        ]);
        $txt = "\n"; //$this->genericTag($args, 'select', false);
        return $txt;
    }

    protected function compileEndSelect()
    {
        $r = @\array_pop($this->htmlItem);
        if (\is_null($r) || $r[0] !== 'select') {
            return $this->showError("@EndSelect", "Missing initial @select", true);
        }
        return $this->phpTag . "echo '</select>'; ?>";
    }

    protected function compileEndCheckbox()
    {
        $r = @\array_pop($this->htmlItem);
        if (\is_null($r) || $r[0] !== 'checkbox') {
            return $this->showError("@EndCheckbox", "Missing initial @checkbox", true);
        }
        return "";
    }
    //</editor-fold>

    //<editor-fold desc="used function">

    protected function compileEndRadio()
    {
        $r = @\array_pop($this->htmlItem);
        if (\is_null($r) || $r[0] !== 'radio') {
            return $this->showError("@EndRadio", "Missing initial @radio", true);
        }
        return "";
    }

    /**
     * @param mixed       $value
     * @param mixed|array $toCompare
     * @param string      $field
     *
     * @return bool
     */
    public static function inArray($value, $toCompare, $field='')
    {
        if (is_array($toCompare)) {
            if (is_array(end($toCompare))) {
            }
            if (is_object(end($toCompare))) {
            }
            return in_array($value, $toCompare);
        }
        return $value==$toCompare;
    }

    protected function compileItem($expression)
    {
        $parent = end($this->htmlItem);
        if ($parent === null) {
            return $this->showError("@items", "Missing @select,@checkbox,@radio", true);
        }
        $args = $this->parseArg2($this->stripParentheses($expression));
        //$args['end'] = $this->stripQuotes(@$args['end']);
        //$args['fielddisplay'] =$this->transform($args['display'], 'inner');
        //$args['fieldvalue'] = "value=" .$this->transform($args['value'], 'echo') . "";
        //$args['class'] = (isset($args['class'])) ? "class=" . $this->transform($args['class'], 'echo') . "" : '';
        $args['name'] =($parent[1])?$parent[1] : '';
        //$args['fieldvalue'] =$args['value'] ;
        $args['inner']=$args['display'];
        $args['display']='';
        //$args['value'] ='';
        //$txt = $this->genericTag($args, $tag, true);
        $type=$parent[0];
        $selection=$parent[2];

        switch ($type) {
            case 'select':
                $ntype='option';
                $args['type']='';
                break;
            case 'checkbox':
                $ntype='input';
                $args['type']='checkbox';

                break;
            case 'radio':
                $ntype='input';
                $args['type']='radio';

                break;
        }
        $php = $this->phpTag;
        $php .= "if(" .$this->transform($args['value'], 'php') . "==$selection) {\n ?>";
        $php.= $this->genericTag(($this->genPattern),$args,$ntype);
        $php.= "<?php } else { ?>\n";
        $php.= $this->genericTag(($this->genPattern),$args,$ntype);
        $php.="<?php } // if ?>\n";
        return $php;
    }

    public function compileLabel($expression)
    {
        $args = $this->parseArg2($this->stripParentheses($expression));

        $txt = $this->genericTag($this->genPattern, $args, 'label', true);


        return $txt;
    }

    public function stripQuotes($text)
    {
        if (substr($text, 0, 1) === '"' && substr($text, -1) === '"') {
            return substr($text, 1, strlen($text) - 2);
        }
        if (substr($text, 0, 1) === "'" && substr($text, -1) === "'") {
            return substr($text, 1, strlen($text) - 2);
        }
        return $text;
    }


    protected function compileItems($expression)
    {
        $parent = end($this->htmlItem);
        if ($parent === null) {
            return $this->showError("@items", "Missing @select,@checkbox,@radio", true);
        }
        $type = $parent[0];
        $name = $parent[1];
        $selection = $this->transform($parent[2], 'php');
        switch ($type) {
            case 'select':
                $pattern = addslashes($this->selectattern);
                $patternChecked = addslashes($this->selectPatternChecked);
                break;
            case 'checkbox':
                $pattern = addslashes($this->checkBoxPattern);
                $patternChecked = addslashes($this->checkBoxPatternChecked);
                break;
            case 'radio':
                $pattern = addslashes($this->radioPatternChecked);
                $patternChecked = addslashes($this->radioPattern);
                break;
        }
        $c = count($this->htmlItem);

        $argsOrig = $this->parseArg2($this->stripParentheses($expression));
        $args = $argsOrig;
        $args['end'] = $this->stripQuotes(@$args['end']);
        $args['name'] = "name=\\\"$name\\\"";
        $args['fieldgroup']=(isset($argsOrig['fieldgroup']))?$argsOrig['fieldgroup']:null;
        $php = $this->phpTag . "\$optgroup{$c}='';\n";
        $php .= "foreach(" . $args['values'] . " as \$k{$c}=>\$v{$c}) {\n";
        if ($args['fieldgroup']) {
            $php .=" if(\$optgroup{$c}!=\$v{$c}->" . $argsOrig['fieldgroup'] . ") { \n";
            $php .=" echo \"<optgroup label=\\\"\$v{$c}->" . $argsOrig['fieldgroup'] . "\\\">\";\n";
            $php .=" \$optgroup{$c}=\$v{$c}->" . $argsOrig['fieldgroup'] . ";\n";
            $php .="}";
        }
         

        $args['fielddisplay'] = "\".static::e(\$v{$c}->" . $args['fielddisplay'] . ").\"";
        $args['fieldvalue'] = "value=\".static::e(\$v{$c}->" . $args['fieldvalue'] . ").\"";
        
        
        $args['class'] = (isset($args['class'])) ? "class=\"." . $args['class'] . ".\"" : '';

        $php .= "if(is_object(\$v{$c})) {\n";
        $php .= "\tif(static::inArray(\$v{$c}->" . $argsOrig['fieldvalue'] . ",$selection)) {\n";
        $php .= "\t\techo \"" . $this->replaceCurlyVariable($patternChecked, $args) . "\";\n";
        $php .= "\t} else {\n";
        $php .= "\t\techo \"" . $this->replaceCurlyVariable($pattern, $args) . "\";\n";
        $php .= "\t}\n";

        $args['fielddisplay'] = "\".static::e(\$v{$c}['" . $argsOrig['fielddisplay'] . "']).\"";
        $php .= "} elseif(is_array(\$v{$c})) { \n";
        $php .= "\tif(static::inArray(\$v{$c}['" . $argsOrig['fieldvalue'] . "'],$selection)) {\n";
        $php .= "\t\techo \"" . $this->replaceCurlyVariable($patternChecked, $args) . "\";\n";
        $php .= "\t} else {\n";
        $php .= "\t\techo \"" . $this->replaceCurlyVariable($pattern, $args) . "\";\n";
        $php .= "\t}\n";

        $args['fielddisplay'] = "\".static::e(\$v{$c}).\"";
        $php .= "} else {\n";
        $php .= "\tif(static::inArray(\$k{$c},$selection)) {\n";
        $php .= "\t\techo \"" . $this->replaceCurlyVariable($patternChecked, $args) . "\";\n";
        $php .= "\t} else {\n";
        $php .= "\t\techo \"" . $this->replaceCurlyVariable($pattern, $args) . "\";\n";
        $php .= "\t}\n";
        $php .= "} // end if\n";
        $php .= "}\n ?>";
        return $php;
    }

    public function replaceCurlyVariable($string, $values, $notFoundThenKeep = false)
    {
        if (strpos($string, '{{') === false) {
            return $string;
        } // nothing to replace.
        return preg_replace_callback('/{{\s?(\w+)\s?}}/u', function ($matches) use ($values, $notFoundThenKeep) {
            if (is_array($matches)) {
                $item = substr($matches[0], 2, strlen($matches[0]) - 4); // removes {{ and }}
                return isset($values[$item]) ? $values[$item] : ($notFoundThenKeep ? $matches[0] : '');
            } else {
                $item = substr($matches, 2, strlen($matches) - 4); // removes {{ and }}
                return isset($values[$item]) ? $values[$item] : ($notFoundThenKeep ? $matches : '');
            }
        }, $string);
    }

    protected function compileListBoxes($expression)
    {
        return $this->phpTag . "echo \$this->listboxes{$expression}; ?>";
    }

    protected function compileLink($expression)
    {
        return $this->phpTag . "echo \$this->link{$expression}; ?>";
    }
    

    protected function compileSelectGroup($expression)
    {
        \array_push($this->htmlItem, 'selectgroup');
        $this->compilePush('');
        return $this->phpTag . "echo \$this->select{$expression}; ?>";
    }

    protected function compileTrio($expression)
    {
        // we add a new attribute with the type of the current open tag
        $r = \end($this->htmlItem);
        $x = \trim($expression);
        $x = "('{$r}'," . \substr($x, 1);
        return $this->phpTag . "echo \$this->trio{$x}; ?>";
    }

    protected function compileTrios($expression)
    {
        // we add a new attribute with the type of the current open tag
        $r = \end($this->htmlItem);
        $x = \trim($expression);
        $x = "('{$r}'," . \substr($x, 1);
        return $this->phpTag . "echo \$this->trios{$x}; ?>";
    }


    protected function compileFile($expression)
    {
        return $this->phpTag . "echo \$this->file{$expression}; ?>";
    }

    protected function compileImage($expression)
    {
        return $this->phpTag . "echo \$this->image{$expression}; ?>";
    }


    protected function compileHidden($expression)
    {
        return $this->phpTag . "echo \$this->hidden{$expression}; ?>";
    }



    protected function compileCommandButton($expression)
    {
        return $this->phpTag . "echo \$this->commandButton{$expression}; ?>";
    }

    protected function compileForm($expression)
    {
        return $this->phpTag . "echo \$this->form{$expression}; ?>";
    }

    protected function compileEndForm()
    {
        return $this->phpTag . "echo '</form>'; ?>";
    }

    //</editor-fold>
}
