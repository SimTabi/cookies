<?php

/*
 * PHP handles all the serializing and unserializing interally
 * I only test that read/write/destroy work as expected.
 * Mocking $_COOKIE and setcookie(...).
 */

namespace Cookies {
    /* Some mocking things */
    $_COOKIE = array();

    /* Normally returns true/false. */
    function setcookie($name, $value, $expire, $path) {
        $_COOKIE[$name] = $value;
        return $value;
    }


    require_once('src/Cookies.php');

    class CookiesTest extends \PHPUnit_Framework_TestCase {

        public function setUp() {
            $this->handler = new Cookies('secretkey');
            $this->handler->open('unusedOnlyHereForTheInterface', 'cookiename');

        }
        public function tearDown() {
            $this->handler = null;
        }
        public function testCanSetData() {
            $data = array(
                'a' => 'b',
                'b' => 0,
            );
            $data = $this->handler->write('a', serialize($data));
            $this->assertEquals($data, 'a:2:{s:1:"a";s:1:"b";s:1:"b";i:0;}5fc2e831459825af2bca601b83e4c9eabbe990c50fa9af59ef820be5efacbe71');

        }
        public function testCanReadData() {
            $data = array(
                'a' => 'b',
                'b' => 0,
            );
            $this->handler->write('a', serialize($data));
            $read = unserialize($this->handler->read('asdf'));
            $this->assertEquals(serialize($read), serialize($data));

        }
        public function testCanDestroy() {
            $data = array(
                'a' => 'b',
                'b' => 0,
            );
            $data = $this->handler->write('a', serialize($data));

            $rc = $this->handler->destroy('a');
            $this->assertEquals($rc, '');

        }
        public function testCanNotSpoof() {
            $data = array(
                'a' => 'b',
                'b' => 0,
            );
            $data = $this->handler->write('a', serialize($data));
            // Change single thing in the data. like the a index from value b to c.
            $data[17] = 'c';

            // Need to set in the "superglobal" too. 
            $_COOKIE['cookiename'] = $data;

            $read = $this->handler->read('a');
            $this->assertEquals($read, '');
        }
    }
}
