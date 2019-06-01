<?php

namespace eftec\tests\directives;

use eftec\tests\AbstractBladeTestCase;

/**
 * @author Jake Whiteley <jakebwhiteley@gmail.com>
 * @since  01/06/2019
 *
 * @todo Need @component tests. Previous attempts would just print the component contents to the terminal and exit.
 */
class ComponentTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testComponentCanBeRendered()
    {
        $this->blade->startComponent('components.alert');
        $output = $this->blade->renderComponent();

        $this->assertEqualsIgnoringWhitespace(
            '<div class="alert"></div>',
            $this->blade->runString($output)
        );
    }

    /**
     * @throws \Exception
     */
    public function testComponentParams()
    {
        $this->blade->startComponent('components.alert', ['title' => 'hello']);
        $output = $this->blade->renderComponent();

        $this->assertEqualsIgnoringWhitespace(
            '<div class="alert"><h2>hello</h2></div>',
            $this->blade->runString($output)
        );
    }

    /**
     * @throws \Exception
     */
    public function testComponentDefaultSlots()
    {
        $this->blade->startComponent('components.alert');
        echo 'slot';
        $output = $this->blade->renderComponent();

        $this->assertEqualsIgnoringWhitespace(
            '<div class="alert">slot</div>',
            $this->blade->runString($output)
        );
    }

    /**
     * @throws \Exception
     */
    public function testComponentNamedSlots()
    {
        $this->blade->startComponent('components.alert');
        $this->blade->slot('title');
        echo 'title';
        $this->blade->endSlot();
        $output = $this->blade->renderComponent();

        $this->assertEqualsIgnoringWhitespace(
            '<div class="alert"><h2>title</h2></div>',
            $this->blade->runString($output)
        );
    }

    /**
     * @throws \Exception
     */
    public function testComponentNamedSlotsWithData()
    {
        $this->blade->startComponent('components.alert');
        $this->blade->slot('title', 'hello');
        $output = $this->blade->renderComponent();

        $this->assertEqualsIgnoringWhitespace(
            '<div class="alert"><h2>hello</h2></div>',
            $this->blade->runString($output)
        );
    }
}