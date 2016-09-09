<?php
/**
 * Smarty PHPunit tests compilation of {foreach} tag
 *
 * @package PHPunit
 * @author  Uwe Tews
 */

/**
 * class for {foreach} tag tests
 *
 * @runTestsInSeparateProcess
 * @preserveGlobalState    disabled
 * @backupStaticAttributes enabled
 */
class CompileForeachTest extends PHPUnit_Smarty
{
    public function setUp()
    {
        $this->setUpSmarty(dirname(__FILE__));
        $this->smarty->addPluginsDir("../../../__shared/PHPunitplugins/");
        $this->smarty->addTemplateDir("./templates_tmp");
    }

    public function testInit()
    {
        $this->cleanDirs();
    }

    /**
     * Test foreach tags
     *
     *
     * @preserveGlobalState disabled
     * @dataProvider        dataTestForeach
     */
    public function testForeach($code, $foo, $result, $testName, $testNumber)
    {
        $file = "testForeach_{$testNumber}.tpl";
        $this->makeTemplateFile($file, $code);
        $this->smarty->assign('x', 'x');
        $this->smarty->assign('y', 'y');
        if ($foo !== null) {
            $this->smarty->assign('foo', $foo);
        } else {
            // unassigned $from parameter
            $this->smarty->setErrorReporting(error_reporting() & ~(E_NOTICE | E_USER_NOTICE));
        }

        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch($file)), "testForeach - {$code} - {$testName}");
    }

    /*
      * Data provider für testForeach
      */
    public function dataTestForeach()
    {
        $i = 0;
        /*
        * Code
        *  $foo value
        * result
        * test name
        */
        return array(
            array('{foreach item=x from=$foo}{$x}{/foreach}', array(1,2,3), '123', '', $i ++),
            array('{foreach $foo as $x}{$x}{/foreach}', array(1,2,3), '123', '', $i ++),
            array('{foreach item=x from=$foo}{if $x == 2}{break}{/if}{$x}{/foreach}', array(0,1,2,3,4), '01', '', $i ++),
            array('{foreach item=x from=$foo}{if $x == 2}{continue}{/if}{$x}{/foreach}', array(0,1,2,3,4), '0134', '', $i ++),
            array('{foreach item=x from=$foo}{$x}{foreachelse}else{/foreach}', array(1,2,3), '123', '', $i ++),
            array('{foreach item=x from=$foo}{$x}{foreachelse}else{/foreach}', array(), 'else', '', $i ++),
            array('{foreach item=x from=$foo}{$x}{foreachelse}else{/foreach}', null, 'else', '', $i ++),
            array('{foreach item=x key=y from=$foo}{$y}=>{$x},{foreachelse}else{/foreach}', array(1,2,3), '0=>1,1=>2,2=>3,', '', $i ++),
            array('{foreach $foo as $y => $x}{$y}=>{$x},{foreachelse}else{/foreach}', array(1,2,3), '0=>1,1=>2,2=>3,', '', $i ++),
            array('{foreach $foo as $y => $x}{$y}=>{$x},{/foreach}-{$x}-{$y}', array(1,2,3), '0=>1,1=>2,2=>3,-x-y', 'saved loop variables', $i ++),
            array('{foreach $foo as $y => $x}{$y}=>{$x},{foreachelse}else{/foreach}-{$x}-{$y}', array(1,2,3), '0=>1,1=>2,2=>3,-x-y', 'saved loop variables', $i ++),
            array('{foreach $foo as $y => $x}{$y}=>{$x},{foreachelse}else{/foreach}-{$x}-{$y}', array(), 'else-x-y', 'saved loop variables', $i ++),
            array('{foreach $foo as $x}{$x@key}=>{$x},{foreachelse}else{/foreach}', array(1,2,3), '0=>1,1=>2,2=>3,', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{$x}{foreachelse}else{/foreach}total{$smarty.foreach.foo.total}', array(1,2,3), '123total3', '', $i ++),
            array('{foreach item=x from=$foo}{$x}{foreachelse}else{/foreach}total{$x@total}', array(1,2,3), '123total3', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{$smarty.foreach.foo.index}.{$x},{/foreach}', array(9,10,11), '0.9,1.10,2.11,', '', $i ++),
            array('{foreach item=x from=$foo}{$x@index}.{$x},{/foreach}', array(9,10,11), '0.9,1.10,2.11,', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{$smarty.foreach.foo.iteration}.{$x},{/foreach}', array(9,10,11), '1.9,2.10,3.11,', '', $i ++),
            array('{foreach item=x from=$foo}{$x@iteration}.{$x},{/foreach}', array(9,10,11), '1.9,2.10,3.11,', '', $i ++),
            array('{foreach item=x from=$foo}{$x@iteration}.{$x}-{$x=\'foo\'}{$x},{/foreach}', array(9,10,11), '1.9-foo,2.10-foo,3.11-foo,', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{if $smarty.foreach.foo.first}first{/if}{$x},{/foreach}', array(9,10,11), 'first9,10,11,', '', $i ++),
            array('{foreach item=x from=$foo}{if $x@first}first{/if}{$x},{/foreach}', array(9,10,11), 'first9,10,11,', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{if $smarty.foreach.foo.last}last{/if}{$x},{/foreach}', array(9,10,11), '9,10,last11,', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{if $smarty.foreach.foo.last}last{/if}{$smarty.foreach.foo.iteration}.{$x},{/foreach}', array(9,10,11), '1.9,2.10,last3.11,', '', $i ++),
            array('{foreach item=x from=$foo}{if $x@last}last{/if}{$x},{/foreach}', array(9,10,11), '9,10,last11,', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{$x}{foreachelse}else{/foreach}{if $smarty.foreach.foo.show}-show{else}-noshow{/if}', array(9,10,11), '91011-show', '', $i ++),
            array('{foreach item=x name=foo from=$foo}{$x}{foreachelse}else{/foreach}{if $smarty.foreach.foo.show}-show{else}-noshow{/if}', array(), 'else-noshow', '', $i ++),
            array('{foreach item=x from=$foo}{$x}{foreachelse}else{/foreach}{if $x@show}-show{else}-noshow{/if}', array(9,10,11), '91011-show', '', $i ++),
            array('{foreach item=x from=$foo}{$x}{foreachelse}else{/foreach}{if $x@show}-show{else}-noshow{/if}', array(), 'else-noshow', '', $i ++),
            array('{foreach $foo x y foo}{$y}.{$x},{foreachelse}else{/foreach}total{$smarty.foreach.foo.total}', array(9,10,11), '0.9,1.10,2.11,total3', '', $i ++),
            array('{$x = "hallo"}{$bar=[1,2,3]}{foreach $foo as $x}outer={$x@index}.{$x}#{foreach $bar as $x}inner={$x@index}.{$x}{/foreach}##{/foreach}###{$x}', array(9,10,11), 'outer=0.9#inner=0.1inner=1.2inner=2.3##outer=1.10#inner=0.1inner=1.2inner=2.3##outer=2.11#inner=0.1inner=1.2inner=2.3#####hallo', '', $i ++),
        );
    }


    /**
     * Test foreach tags caching
     *
     *
     * @preserveGlobalState disabled
     * @dataProvider        dataTestForeachNocache
     */
    public function testForeachCaching($code, $new, $assignNocache, $foo, $result, $testName, $testNumber)
    {
        $this->smarty->caching = true;
        $file = "testForeachNocache_{$testNumber}.tpl";
        if ($new) {
            $this->makeTemplateFile($file, $code);
        }
        if ($foo !== null) {
            $this->smarty->assign('foo', $foo, $assignNocache);
        } else {
            // unassigned $from parameter
            $this->smarty->setErrorReporting(error_reporting() & ~(E_NOTICE | E_USER_NOTICE));
        }

        $this->assertEquals($this->strip($result), $this->strip($this->smarty->fetch($file)), "testForeach - {$code} - {$testName}");
    }

    /*
   * Data provider für testForeachNocache
   */
    public function dataTestForeachNocache()
    {
        $i = 0;
        /*
        * Code
        * new name new file
        * assign nocache
        *  $foo value
        * result
        * test name
        */
        return array(
            array('{foreach item=x from=$foo}{$x}{/foreach}', true, true, array(1, 2, 3), '123', '', $i),
            array('{foreach item=x from=$foo}{$x}{/foreach}', false, true, array(4, 5, 6), '456', '', $i ++),
            array('{foreach item=x from=$foo}{$x}{/foreach}', true, false, array(1, 2, 3), '123', '', $i),
            array('{foreach item=x from=$foo}{$x}{/foreach}', false, false, array(4, 5, 6), '123', '', $i ++),
            array('{nocache}{foreach item=x from=$foo}{$x}{/foreach}{/nocache}', true, false, array(1, 2, 3), '123', '', $i),
            array('{nocache}{foreach item=x from=$foo}{$x}{/foreach}{/nocache}', false, false, array(4, 5, 6), '456', '', $i ++),
        );
    }
    /*
    *  test foreach and nocache
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled

    */
    public function testForeachNocacheVar1_024()
    {
        $this->smarty->caching = true;
        $this->smarty->assign('foo', array(1, 2), true);
        $this->assertFalse($this->smarty->isCached('024_foreach.tpl'));
        $this->assertEquals("1 2 ", $this->smarty->fetch('024_foreach.tpl'));
    }

    /**
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testForeachNocacheVar2_024()
    {
        $this->smarty->caching = true;
        $this->smarty->assign('foo', array(9, 8), true);
        $this->assertTrue($this->smarty->isCached('024_foreach.tpl'));
        $this->assertEquals("9 8 ", $this->smarty->fetch('024_foreach.tpl'));
    }

    /**
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testForeachNocacheTag1_025()
    {
        $this->smarty->caching = true;
        $this->smarty->assign('foo', array(1, 2));
        $this->assertFalse($this->smarty->isCached('025_foreach.tpl'));
        $this->assertEquals("1 2 ", $this->smarty->fetch('025_foreach.tpl'));
    }

    /**
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testForeachNocacheTag2_25()
    {
        $this->smarty->caching = true;
        $this->smarty->assign('foo', array(9, 8));
        $this->assertTrue($this->smarty->isCached('025_foreach.tpl'));
        $this->assertEquals("9 8 ", $this->smarty->fetch('025_foreach.tpl'));
    }

    /**
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testForeachCache1_26()
    {
        $this->smarty->caching = true;
        $this->smarty->assign('foo', array(1, 2));
        $this->assertFalse($this->smarty->isCached('026_foreach.tpl'));
        $this->assertEquals("1 2 ", $this->smarty->fetch('026_foreach.tpl'));
    }

    /**
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     *
     */
    public function testForeachCache2_26()
    {
        $this->smarty->caching = true;
        $this->smarty->assign('foo', array(9, 8));
        $this->assertTrue($this->smarty->isCached('026_foreach.tpl'));
        $this->assertEquals("1 2 ", $this->smarty->fetch('026_foreach.tpl'));
    }

    public function testForeachNested_27()
    {
        $this->smarty->assign('foo', array(9, 8));
        $this->smarty->assign('bar', array(4, 10));
        $this->assertEquals("outer=0#9inner=0#4inner=1#10##outer=1#8inner=0#4inner=1#10#####hallo",
                            $this->smarty->fetch('027_foreach.tpl'));
    }

    public function testForeachNestedNamed_28()
    {
        $this->smarty->assign('foo', array(9, 8));
        $this->smarty->assign('bar', array(4, 10));
        $this->assertEquals("outer=0#0-9inner=1#0-4inner=2#0-10##outer=1#1-8inner=1#1-4inner=2#1-10#####hallo",
                            $this->smarty->fetch('028_foreach.tpl'));
    }

    public function testForeachBreak_29()
    {
        $this->assertEquals("12",
                            $this->smarty->fetch('029_foreach.tpl'));
    }

    public function testForeachBreak_30()
    {
        $this->assertEquals("a1a2b1b2for20a1a2b1b2for21",
                            $this->smarty->fetch('030_foreach.tpl'));
    }

    public function testForeachBreak_31()
    {
         $this->assertEquals("a1a2for20a1a2for21",
                            $this->smarty->fetch('031_foreach.tpl'));
    }

    public function testForeachContinue_32()
    {
        $this->assertEquals("1245",
                            $this->smarty->fetch('032_foreach.tpl'));
    }

    public function testForeachContinue_33()
    {
        $this->assertEquals("a1a2a4a5b1b2b4b5for20a1a2a4a5b1b2b4b5for21",
                            $this->smarty->fetch('033_foreach.tpl'));
    }

    public function testForeachContinue_34()
    {
        $this->assertEquals("a1a2b1b2for20a1a2b1b2for21",
                            $this->smarty->fetch('034_foreach.tpl'));
    }

    public function testForeachContinue_35()
    {
        $this->assertEquals("a1a2a1a2",
                            $this->smarty->fetch('035_foreach.tpl'));
    }
}
