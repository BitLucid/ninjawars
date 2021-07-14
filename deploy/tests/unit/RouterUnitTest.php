<?php
use NinjaWars\core\Router;
use Symfony\Component\HttpFoundation\Request;

class RouterUnitTest extends NWTest {
    public function testParseRouteSlash() {
        $request = Request::create('/', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertIsArray($result);
        $this->assertGreaterThan(1, count($result));
        $this->assertContains('homepage', $result);
    }

    public function testParseRouteControllerDefault() {
        $request = Request::create('/work/', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertIsArray($result);
        $this->assertGreaterThan(1, count($result));
    }

    public function testParseRouteControllerCommand() {
        $request = Request::create('/shop/buy', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertIsArray($result);
        $this->assertGreaterThan(1, count($result));
    }

    public function testParseRouteControllerAlternateDefault() {
        $request = Request::create('/clan/', 'GET', []);
        $result = Router::parseRoute($request);
        $this->assertIsArray($result);
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
        $response = Router::serveSimpleRoute($testValue);
        $reflection = new \ReflectionProperty(get_class($response), 'template');
        $reflection->setAccessible(true);
        $response_template = $reflection->getValue($response);
        $this->assertEquals($response_template, $testValue.'.tpl');
    }

    public function testExecuteBadClassname() {
        $this->expectException('\RuntimeException');
        Router::execute('junkola', '', $this->m_dependencies);
    }

    public function testExecuteBadCommand() {
        $this->expectException('\RuntimeException');
        Router::execute('work', 'junkola', $this->m_dependencies);
    }

    public function testRouteBadClassname() {
        $this->expectException('\RuntimeException');
        $request = Request::create('/junkola/', 'GET', []);
        Router::route($request, $this->m_dependencies);
    }
}
