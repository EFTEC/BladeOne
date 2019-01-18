<?php

namespace eftec\tests;


use eftec\bladeone\BladeOne;

class MultipleTemplatePathTest extends AbstractBladeTestCase
{
    static $templatePaths = [
        __DIR__ . '/resources/templates_two',
        __DIR__ . '/resources/templates'
    ];

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $this->blade = new BladeOne(self::$templatePaths, self::COMPILED_PATH, BladeOne::MODE_SLOW);
    }
    /**
     * @throws \Exception
     */
    public function testMultiTemplatePath() {
        $content = $this->blade->run('base', []);
        $this->assertEqualsIgnoringWhitespace("Multiple template path test", $content);
    }
    /**
     * @throws \Exception
     */
    public function testTemplateOverride() {
        $content = $this->blade->run('compilation.base', []);
        $this->assertEqualsIgnoringWhitespace("Compilation test template override", $content);
    }
}