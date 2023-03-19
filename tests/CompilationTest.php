<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace eftec\tests;

use eftec\bladeone\BladeOne;
use Exception;

/**
 * @author G.J.W. Oolbekkink <g.j.w.oolbekkink@gmail.com>
 * @since  16/09/2018
 */
class CompilationTest extends AbstractBladeTestCase
{
    /**
     * @throws Exception
     */
    public function testCompilation(): void
    {
        $this->assertEqualsIgnoringWhitespace("Compilation test template", $this->blade->run('compilation.base', []));
    }
    public function testTypeFilename(): void
    {
        global $blade;
        $views = __DIR__ . '/templates';
        $compiledFolder = __DIR__ . '/compiled';
        $blade = new BladeOne($views, $compiledFolder);
        $blade->createFolders();
        // Type MD5
        $blade->setCompileTypeFileName('md5');
        $this->assertEquals("It is a basic template hello world.\n", $blade->run('basic',['variable'=>'hello world']));
        $this->assertEquals('basic_' . \md5($views . '/' . 'basic.blade.php') . '.bladec',basename($blade->getCompiledFile('basic')));

        // Type default fallback (type does not exist)
        $blade->setCompileTypeFileName('nochange');
        $this->assertEquals("It is a basic template hello world.\n", $blade->run('basic',['variable'=>'hello world']));
        $this->assertEquals('basic_' . \sha1($views . '/' . 'basic.blade.php') . '.bladec',basename($blade->getCompiledFile('basic')));
    }
    public function testComment(): void
    {
        global $blade;
        $views = __DIR__ . '/templates';
        $compiledFolder = __DIR__ . '/compiled';
        $blade = new BladeOne($views, $compiledFolder);
        $blade->setCompileTypeFileName('md5');
        $this->assertEquals("Multi line comment in PHP tags\n\nSingle line comment in PHP tags\n\n", $blade->run('comment',[]));
        $this->assertEquals('comment_' . \md5($views . '/' . 'comment.blade.php') . '.bladec',basename($blade->getCompiledFile('comment')));
    }

    public function testToken(): void
    {

        $token = $this->blade->getCsrfToken();
        $this->assertNotEmpty($token);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_token'] = $token;
        $this->assertTrue($this->blade->csrfIsValid(true));

        unset($_SERVER['REQUEST_METHOD'], $_POST['_token']);
    }

    public function testAuth(): void
    {
        global $blade;
        $views = __DIR__ . '/templates';
        $compiledFolder = __DIR__ . '/compiled';
        $blade = new BladeOne($views, $compiledFolder, BladeOne::MODE_SLOW);
        $blade->setCanFunction(function ($action, $subject = null) {
            global $blade;
            if ($subject === 'noallowed') {
                return false;
            }
            return in_array($action, $blade->currentPermission, true);
        });

        $blade->setAnyFunction(function ($array) {
            global $blade;
            foreach ($array as $permission) {
                if (in_array($permission, $blade->currentPermission, true)) {
                    return true;
                }
            }
            return false;
        });
        $blade->setAuth("john", "admin", ['edit', 'delete']);

            $this->assertEqualsIgnoringWhitespace('Title:Testinganuserthatisadministrator.Hecouldedit,deleteandhastheroleofadministrator.Currentuser:johnaccountisadminstrator.Theusercanteditneitherview.Theuserisnotallowedtoedittheelementnotallowed.Theuserisallowedtoedit.Theusercanedit.Theusercaneditoradd.Userisnotanonymous.Userisnotanonymous.'
            ,$blade->run("Test2.auth", ['title' => 'Testing an user that is administrator. He could edit,delete and has the role of administrator']));


        $blade->setAuth("mary", "user", ['view']);

            $this->assertEqualsIgnoringWhitespace('Title:Testinganuserthatisanormaluser.Shecouldonlyviewandhastheroleofuser.Currentuser:maryaccountisuser.Theusercaneditbuthecanview.Theuserisnotallowedtoedittheelementnotallowed.Theuserisnotallowedtoedit.Theusercantedit.Theusercanteditoradd.Userisnotanonymousandisnotadmin.Userisnotanonymous.'
                ,$blade->run("Test2.auth", ['title' => 'Testing an user that is a normal user. She could only view and has the role of user']));


        $blade->setAuth(null);

            $this->assertEqualsIgnoringWhitespace('Title:Testinganuserthatisanonymous..Currentuser:accountisnotadministratorneitheruser.Theusercanteditneitherview.Theuserisnotallowedtoedittheelementnotallowed.Theuserisnotallowedtoedit.Theusercantedit.Theusercanteditoradd.Userisanonymous.Userisanonymous.'
                ,$blade->run("Test2.auth", ['title' => 'Testing an user that is anonymous.']));

    }

    public function testComponent(): void
    {
        $this->blade->pipeEnable = true;
        $this->blade->setPath(__DIR__ . '/templates', __DIR__ . '/compiles');
        $this->blade->createFolders();
        $this->assertEqualsIgnoringWhitespace('paramlessmyglobal:helloCONTENTSLOTcolor:red,title:notitle,myglobal:helloCONTENTSLOT2,endtitle:notitle'
            , $this->blade->run("TestComponent.component", ['myglobal' => 'hello']));
        $this->blade->pipeEnable = false;
    }

    public function testCompilationCallBack(): void
    {
        $this->blade->setMode(BladeOne::MODE_DEBUG);
        $this->blade->compileCallbacks[] = static function (&$content, $templatename = null) {
            $content = strtoupper($content);
        };
        $this->blade->compileCallbacks[] = static function (&$content, $templatename = null) {
            $content = '**' . $content . '**';
        };

        $this->assertEqualsIgnoringWhitespace("**COMPILATIONTESTTEMPLATE**", $this->blade->run('compilation.base', []));
        $this->blade->compileCallbacks = [];
    }

    /**
     * @throws Exception
     */
    public function testCompilationCreatesCompiledFile(): void
    {
        $this->blade->run('compilation.base', []);
        // we don't need to re-create the name manually, the function already exists.
        $this->assertFileExists($this->blade->getCompiledFile('compilation.base'));
    }

    /**
     * @throws Exception
     */
    public function testCompilationDebugCreatesCompiledFile(): void
    {
        $this->blade->setMode(BladeOne::MODE_DEBUG);
        $this->blade->run('compilation.base', []);

        $this->assertFileExists(__DIR__ . '/resources/compiled/compilation.base_' . \sha1(__DIR__ . '/resources/templates/compilation/base.blade.php') . '.bladec');

        $this->blade->setMode(BladeOne::MODE_SLOW);
    }

    /**
     * @throws Exception
     */
    public function testCompilationCustomFileExtension(): void
    {
        $this->blade->setFileExtension('.blade');

        $this->assertEqualsIgnoringWhitespace("Custom extension blade file", $this->blade->run('compilation.base', []));

        $this->blade->setFileExtension('.blade.php');
    }

    /**
     * For the issue #57. Version 3.16
     * @throws Exception
     */
    public function testCompilationTemplateExist(): void
    {
        $this->blade->setFileExtension('.blade');

        $this->assertEquals(true, $this->blade->compile('compilation.base'), "Running compile method");

        $this->blade->setFileExtension('.blade.php');
    }


    /**
     * @throws Exception
     */
    public function testCompilationCustomCompileExtension(): void
    {
        $this->blade->setCompiledExtension('.bladeD');
        $this->blade->run('compilation.base', []);

        $this->assertFileExists(__DIR__ . '/resources/compiled/compilation.base_' . sha1(__DIR__ . '/resources/templates/compilation/base.blade.php') . '.bladeD');

        $this->blade->setCompiledExtension('.bladec');
    }
}
