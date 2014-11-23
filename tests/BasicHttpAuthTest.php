<?php

/* 
 * This file is a part of PHP-HTTP-Auth-server library, released under terms 
 * of GPL-3.0 licence. Copyright (c) 2014, UnArt Slavičín, o.s. All rights 
 * reserved.
 */

namespace phphttpauthserver;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-22 at 19:58:21.
 */
class BasicHttpAuthTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var BasicHttpAuth
     */
    protected $object;
    
    protected function setUp() {
        $am = new BasicTestAccountManager();
        $this->object = HttpAuth::factory('basic', $am, 'Test realm');
    }

    /**
     * No login variables provided - should not passed with auth HTTP header.
     * 
     * @covers BasicHttpAuth::auth
     */
    public function testAuthEmpty() {
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertFalse($resp->isPassed());
        $headers = $resp->getHeaders();
        $this->assertNotNull($headers);
        $this->assertTrue(array_key_exists('WWW-Authenticate', $headers));
    }

    /**
     * Unknown user login - should not passed.
     * 
     * @covers BasicHttpAuth::auth
     */
    public function testAuthUnknownUser() {
        $_SERVER['PHP_AUTH_USER'] = 'aaaa';
        $_SERVER['PHP_AUTH_PW'] = 'aaaa';
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertFalse($resp->isPassed());
        $headers = $resp->getHeaders();
        $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
    }

    /**
     * No user password login - should not passed.
     * 
     * @covers BasicHttpAuth::auth
     */
    public function testAuthInvalidNoPassword() {
        $_SERVER['PHP_AUTH_USER'] = 'aa';
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertFalse($resp->isPassed());
        $headers = $resp->getHeaders();
        $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
    }

    /**
     * Invalid user password login - should not passed.
     * 
     * @covers BasicHttpAuth::auth
     */
    public function testAuthInvalidPassword() {
        $_SERVER['PHP_AUTH_USER'] = 'aa';
        $_SERVER['PHP_AUTH_PW'] = 'bbb';
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertFalse($resp->isPassed());
        $headers = $resp->getHeaders();
        $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
    }

    /**
     * Valid login.
     * 
     * @covers BasicHttpAuth::auth
     */
    public function testAuthValid() {
        $_SERVER['PHP_AUTH_USER'] = 'aaa';
        $_SERVER['PHP_AUTH_PW'] = 'bbb';
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertTrue($resp->isPassed());
        $this->assertEquals('aaa', $resp->getUsername());
        $headers = $resp->getHeaders();
        $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
    }

}

/**
 * Test account manager.
 */
class BasicTestAccountManager implements IAccountManager {
    
    private static $users = array(
        'aa' => 'aa',
        'aaa' => 'bbb'
    );
    
    public function getUserPassword($username) {
        return isset(self::$users[$username]) ? self::$users[$username] : FALSE;
    }

}