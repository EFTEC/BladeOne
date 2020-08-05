<?php

namespace eftec\tests;

class ComposerTest extends AbstractBladeTestCase
{
    /**
     * @throws \Exception
     */
    public function testIf()
    {
        $this->blade->composer(); // reset
        
        $this->blade->composer('composer.layout', function ($view) {
            $view->with([
                'header' => "IT IS THE HEADER",
                'footer' => "IT IS THE FOOTER",
            ]);
        });
        $this->blade->composer('composer.example2', function ($view) {
            $view->with([
                'content' => "IT IS THE CONTENT"
            ]);
        });




        $html= $this->blade->run('composer.example2');
        
        $this->assertEqualsIgnoringWhitespace(
            '*example2.blade.php**layout.blade.php*ITISTHEHEADER*content*ITISTHECONTENTITISTHEFOOTER', $html);
        $this->blade->composer(); // reset
    }
}
