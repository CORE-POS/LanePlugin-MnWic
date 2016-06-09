<?php

use COREPOS\pos\lib\FormLib;

class Test extends PHPUnit_Framework_TestCase
{
    public function testPlugin()
    {
        $obj = new WicPlugin();
        $obj->plugin_transaction_reset();
    }

    public function testParser()
    {
        $p = new WicParser();
        $this->assertEquals(false, $p->check('foo'));
        $this->assertEquals(true, $p->check('WIC'));
        $json = $p->parse('WIC');
        $this->assertNotEquals(false, strstr($json['main_frame'], 'WicMenuPage'));
        CoreLocal::set('WicMode', 1);
        $this->assertEquals(true, $p->check('100DP10'));
        $json = $p->parse('100DP10');
        $this->assertEquals(true, $p->check('1234'));
        $json = $p->parse('1234');
        $this->assertNotEquals(false, strstr($json['main_frame'], 'WicOverridePage'));
    }

    public function testPages()
    {
        $pages = array('WicMenuPage', 'WicOverridePage', 'WicTenderPage');
        foreach ($pages as $class) {
            $page = new $class();
            $this->assertEquals(true, $page->preprocess());
            ob_start();
            $page->body_content();
            $this->assertInternalType('string', ob_get_clean());
        }
    }

    public function testNotifier()
    {
        $n = new WicNotifier();
        CoreLocal::set('WicMode', 0);
        $this->assertEquals('', $n->draw());
        CoreLocal::set('WicMode', 1);
        $this->assertNotEquals('', $n->draw());
    }

    public function testTotalAction()
    {
        $w = new WicTotalAction();
        CoreLocal::set('WicMode', 1);
        $w->apply();
    }
}

