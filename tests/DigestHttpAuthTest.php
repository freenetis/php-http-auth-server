<?php

/* 
 * This file is a part of PHP-HTTP-Auth-server library, released under terms 
 * of GPL-3.0 licence. Copyright (c) 2014, UnArt Slavičín, o.s. All rights 
 * reserved.
 */

namespace phphttpauthserver;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-11-22 at 19:58:46.
 */
class DigestHttpAuthTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var DigestHttpAuth
     */
    protected $object;
    
    protected function setUp() {
        $am = new DigestTestAccountManager();
        $this->object = HttpAuth::factory('digest', $am, 'Test realm');
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * No login variables provided - should not passed with auth HTTP header.
     * 
     * @covers DigestHttpAuth::auth
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
     * Mallformed auth header - should not passed.
     * 
     * @covers DigestHttpAuth::auth
     */
    public function testAuthMalformedHeader() {
        $invalids = array(
            // missing needed fields
            'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"',
            'username="aaa",'
                . 'realm="Test realm",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"',
            'username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"',
            'username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"',
            'username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'cnonce="0a4f113b",'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"',
            'username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"',
            'username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"'
        );$i = 0;
        foreach ($invalids as $invalid) {
            $_SERVER['PHP_AUTH_DIGEST'] = $invalid;
            $resp = $this->object->auth();
            $this->assertNotNull($resp);
            $this->assertFalse($resp->isPassed(), $i++);
            $headers = $resp->getHeaders();
            $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
        }
    }

    /**
     * Unknown user login - should not passed.
     * 
     * @covers DigestHttpAuth::auth
     */
    public function testAuthUnknownUser() {
        $_SERVER['PHP_AUTH_DIGEST'] = 'username="aaaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="f6d39061f56419f9f2a0237da0ee649f",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"';
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertFalse($resp->isPassed());
        $headers = $resp->getHeaders();
        $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
    }

    /**
     * Invalid user password login - should not passed.
     * 
     * @covers DigestHttpAuth::auth
     */
    public function testAuthInvalidPassword() {
        $_SERVER['PHP_AUTH_DIGEST'] = 'username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="d237b6f3ff5f204284174675d44572d5",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"';
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertFalse($resp->isPassed());
        $headers = $resp->getHeaders();
        $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
    }

    /**
     * Valid login.
     * 
     * @covers DigestHttpAuth::auth
     */
    public function testAuthValid() {
        $_SERVER['PHP_AUTH_DIGEST'] = 'username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"';
        $resp = $this->object->auth();
        $this->assertNotNull($resp);
        $this->assertTrue($resp->isPassed());
        $this->assertEquals('aaa', $resp->getUsername());
        $headers = $resp->getHeaders();
        $this->assertFalse(array_key_exists('WWW-Authenticate', $headers));
    }

    /**
     * Valid login with HTTP_AUTHORIZATION source.
     * 
     * @covers DigestHttpAuth::auth
     */
    public function testAuthValid2() {
        $_SERVER['HTTP_AUTHORIZATION'] = 'Digest username="aaa",'
                . 'realm="Test realm",'
                . 'nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093",'
                . 'uri="/test-url.html",'
                . 'qop=auth,'
                . 'nc=00000001,'
                . 'cnonce="0a4f113b",'
                . 'response="02c83108a1fe0d951ba88d0568909936",'
                . 'opaque="f303ba0891f604337a83cfe595eb8a9b"';
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
class DigestTestAccountManager implements IAccountManager {
    
    private static $users = array(
        'aa' => 'aa',
        'aaa' => 'bbb'
    );
    
    public function getUserPassword($username) {
        return isset(self::$users[$username]) ? self::$users[$username] : FALSE;
    }

}