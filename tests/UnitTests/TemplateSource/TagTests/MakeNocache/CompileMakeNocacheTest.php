<?php
/**
 * Smarty PHPunit tests compilation of {make_nocache} tags
 *
 * @package PHPunit
 * @author  Uwe Tews
 */

/**
 * class for {make_nocache} tags tests
 *
 * @runTestsInSeparateProcess
 * @preserveGlobalState    disabled
 * @backupStaticAttributes enabled
 */
class CompileMakeNocacheTest extends PHPUnit_Smarty
{
    public function setUp()
    {
        $this->setUpSmarty(dirname(__FILE__));
        $this->smarty->addPluginsDir("../../../__shared/PHPunitplugins/");
        $this->smarty->addTemplateDir("../../../__shared/templates/");
        $this->smarty->addTemplateDir("./templates_tmp");
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    /**
     * Test {make_nocache} tags caching disabled
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001
     */
    public function testMakeNocache_001($foo, $result)
    {
        if ($foo) {
            $this->smarty->assign('foo', $foo);
        }
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo.tpl')),
                            "foo = {$foo}");
    }

    /*
      * Data provider für testMakeNocache_001
      */
    public function dataTestMakeNocache001()
    {

        /*
        *  $foo
        * result
        *
        */
        return array(array(1, '#001_test_foo.tpl:$foo=1'), array(2, '#001_test_foo.tpl:$foo=2'),
                     array(null, '#001_test_foo.tpl:$foo=>unassigned<'),);
    }

    /**
     * Test {make_nocache} cached tags
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001_1
     */
    public function testMakeNocache_001_1($foo, $result)
    {
        $this->smarty->setCaching(true);
        if ($foo) {
            $this->smarty->assign('foo', $foo);
        }
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo.tpl')),
                            "foo = {$foo}");
    }

    /*
     * Data provider für testMakeNocache_001_1
     */
    public function dataTestMakeNocache001_1()
    {

        /*
        * $foo
        * result
        *
        */
        return array(array(1, '#001_test_foo.tpl:$foo=1'), array(2, '#001_test_foo.tpl:$foo=1'),
                     array(null, '#001_test_foo.tpl:$foo=1'),);
    }

    /**
     * Test {make_nocache} cached tags existing nocahe variable
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001_2
     */
    public function testMakeNocache_001_2($foo, $result)
    {
        $this->smarty->setCaching(true);
        if ($foo) {
            $this->smarty->assign('foo', $foo, true);
        }
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo.tpl')),
                            "foo = {$foo}");
    }

    /*
       * Data provider für testMakeNocache_001_2
       */
    public function dataTestMakeNocache001_2()
    {

        /*
        *  $foo
        * result
        *
        */
        return array(array(1, '#001_test_foo.tpl:$foo=1'), array(2, '#001_test_foo.tpl:$foo=2'),
                     array(null, '#001_test_foo.tpl:$foo=1'),);
    }

    /**
     * Test {make_nocache} cached tags reassign
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001_3
     */
    public function testMakeNocache_001_3($foo, $result)
    {
        $this->smarty->setCaching(true);
        if ($foo) {
            $this->smarty->assign('foo', $foo);
        }
        $this->smarty->assign('bar', $foo + 4, true);
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo_assign.tpl')),
                            "foo = {$foo}");
    }

    /*
       * Data provider für testMakeNocache_001_3
       */
    public function dataTestMakeNocache001_3()
    {

        /*
        *  $foo
        * result
        *
        */
        return array(array(1, '#001_test_foo_assign.tpl:$foo=5'), array(2, '#001_test_foo_assign.tpl:$foo=6'),
                     array(null, '#001_test_foo_assign.tpl:$foo=4'),);
    }

    /**
     * Test {make_nocache} cached tags {if}
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001_4
     */
    public function testMakeNocache_001_4($foo, $bar, $result)
    {
        $this->smarty->setCaching(true);
        $this->smarty->assign('foo', $foo);
        $this->smarty->assign('bar', $bar);
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo_if.tpl')),
                            "foo = {$foo}");
    }

    /*
       * Data provider für testMakeNocache_001_4
       */
    public function dataTestMakeNocache001_4()
    {
        /*
     * $foo
     * $bar
     * result
     *
     */
        return array(array(10, 9, 'greater'), array(9, 10, 'greater'),);
    }

    /**
     * Test {make_nocache} cached tags {if} nocache
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001_5
     */
    public function testMakeNocache_001_5($foo, $bar, $result)
    {
        $this->smarty->setCaching(true);
        $this->smarty->compile_id = 1;
        if ($foo) {
            $this->smarty->assign('foo', $foo);
        }
        $this->smarty->assign('bar', $bar, true);
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo_if.tpl')),
                            "foo = {$foo}");
    }

    /*
       * Data provider für testMakeNocache_001_5
       */
    public function dataTestMakeNocache001_5()
    {
        /*
       * $foo
       * $bar
       * result
       *
       */
        return array(array(10, 9, 'greater'), array(9, 10, 'not greater'), array(null, 11, 'not greater'),
                     array(null, 2, 'greater'),);
    }

    /**
     * Test {make_nocache} cached tags {foreach}
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001_6
     */
    public function testMakeNocache_001_6($foo, $bar, $result)
    {
        $this->smarty->setCaching(true);
        $this->smarty->assign('foo', $foo, true);
        $this->smarty->assign('bar', $bar);
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo_foreach.tpl')),
                            "foo = {$foo}");
    }

    /*
       * Data provider für testMakeNocache_001_6
       */
    public function dataTestMakeNocache001_6()
    {

        /*
        * $foo
        * $bar
        * result
        *
        */
        return array(array(2, array(1, 2, 3, 4), '12match34'), array(3, array(7, 8, 9), '123match4'),);
    }

    /**
     * Test {make_nocache} cached tags {foreach} nocache
     *
     * @not                 runInSeparateProcess
     * @preserveGlobalState disabled
     * @dataProvider        dataTestMakeNocache001_7
     */
    public function testMakeNocache_001_7($foo, $bar, $result)
    {
        $this->smarty->setCaching(true);
        $this->smarty->compile_id = 1;
        $this->smarty->assign('foo', $foo, true);
        $this->smarty->assign('bar', $bar, true);
        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch('001_test_foo_foreach.tpl')),
                            "foo = {$foo}");
    }

    /*
       * Data provider für testMakeNocache_001_7
       */
    public function dataTestMakeNocache001_7()
    {

        /*
       * $foo
       * $bar
       * result
       *
       */
        return array(array(2, array(1, 2, 3, 4), '12match34'), array(7, array(7, 8, 9), '7match89'),);
    }

}
