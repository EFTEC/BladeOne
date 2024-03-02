<?php /** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpMissingReturnTypeInspection */
/** @noinspection UnknownInspectionInspection */

/** @noinspection PhpUnused */

namespace eftec\bladeone;

/*
 * It's an example of a custom set of functions for bladeone.
 * in examples/TestCustom.php there is a working example
 */

use function array_pop;

trait BladeOneCustom
{
    private array $customItem = []; // indicates the type of the current tag. such as select/selectgroup/etc.

    //<editor-fold desc="compile function">
    /**
     * Usage @panel('title',true,true).....@endpanel()
     *
     * @param $expression
     * @return string
     */
    protected function compilePanel($expression)
    {
        $this->customItem[] = 'Panel';
        return $this->phpTag . "echo \$this->panel$expression; ?>";
    }

    protected function compileEndPanel()
    {
        $r = @array_pop($this->customItem);
        if ($r === null) {
            $this->showError('@endpanel', 'Missing @compilepanel or so many @compilepanel', true);
        }
        return ' </div></section><!-- end panel -->'; // we don't need to create a function for this.
    }

    //</editor-fold>

    //<editor-fold desc="used function">
    protected function panel($title = '', $toggle = true, $dismiss = true)
    {
        return "<section class='panel'>
                <header class='panel-heading'>
                    <div class='panel-actions'>
                        " . (($toggle) ? "<a href='#' class='panel-action panel-action-toggle' data-panel-toggle></a>" : '') . '
                        ' . (($dismiss) ? "<a href='#' class='panel-action panel-action-dismiss' data-panel-dismiss></a>" : '') . "
                    </div>
    
                    <h2 class='panel-title'>$title</h2>
                </header>
                <div class='panel-body'>";
    }
    //</editor-fold>
}
