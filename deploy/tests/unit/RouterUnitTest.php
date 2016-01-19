<?php
use NinjaWars\core\Router;
use Symfony\Component\HttpFoundation\Request;

class RouterUnitTest extends PHPUnit_Framework_TestCase {
    public function testParseRouteSlash() {
        $request = Request::create('/', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertInternalType('array', $result);
        $this->assertGreaterThan(1, count($result));
        $this->assertContains('index', $result[0]);
    }

    public function testParseRouteControllerDefault() {
        $request = Request::create('/work/', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertInternalType('array', $result);
        $this->assertGreaterThan(1, count($result));
    }

    public function testParseRouteControllerCommand() {
        $request = Request::create('/shop/buy', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertInternalType('array', $result);
        $this->assertGreaterThan(1, count($result));
    }

    public function testParseRouteControllerAlternateDefault() {
        $request = Request::create('/clan/', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertInternalType('array', $result);
        $this->assertGreaterThan(1, count($result));
    }

    public function testBuildClassname() {
        $this->assertEquals(
            Router::buildClassname('junk'),
            'NinjaWars\core\control\JunkController'
        );
    }

    public function testSanitizeRoutePHP() {
        $this->assertEquals(Router::sanitizeRoute('work.php'), 'work');
    }

    public function testIsServableFilePositive() {
        $dir = getcwd();
        chdir(ROOT.'/www/');
        $this->assertTrue(Router::isServableFile('front-controller.php'));
        chdir($dir);
    }

    public function testIsServableFileNegative() {
        $dir = getcwd();
        chdir(ROOT.'/www/');
        $this->assertFalse(Router::isServableFile('back-controller.php'));
        chdir($dir);
    }

    public function testServeSimpleRoute() {
        $testValue = 'staff';
        $response = Router::serveSImpleRoute($testValue);
        $this->assertArrayHasKey('template', $response);
        $this->assertEquals($response['template'], $testValue.'.tpl');
    }

    public function testExecuteBadClassname() {
        $this->setExpectedException('\RuntimeException');
        Router::execute('junkola', '');
    }

    public function testExecuteBadCommand() {
        $this->setExpectedException('\RuntimeException');
        Router::execute('work', 'junkola');
    }

    public function testRouteBadClassname() {
        $this->setExpectedException('\RuntimeException');
        $request = Request::create('/junkola/', 'GET', []);
        Router::route($request);
    }
}
