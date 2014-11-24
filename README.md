PHP-HTTP-Auth-server
====================

[![Build Status](https://api.travis-ci.org/freenetis/php-http-auth-server.svg?branch=master)](https://travis-ci.org/freenetis/php-http-auth-server)

PHP-HTTP-Auth-server is a library for easy implementation of multiple types of HTTP authentication on server side in PHP.

Supported HTTP authentication methods:
- Basic HTTP authentication,
- Digest HTTP authentication (this authentication method requires [mod_auth_digest](http://httpd.apache.org/docs/2.2/mod/mod_auth_digest.html) to be enabled on [Apache server](http://httpd.apache.org/)).

## How to install?

*TODO: using composer*

## How to use it?

### 1. Create an account manager

Account manager is responsible for accessing of information about user accounts. Each manager must implements `\phphttpauthserver\IAccountManager` that defines single method `getUserPassword(string)`. Simple implementation of the account manager may look like this:


```php
class MySimpleAccountManager implements \phphttpauthserver\IAccountManager {
    
    private static $users = array(
        'ned' => 'winter_is_coming',
        'jaime' => 'hear_me_roar'
    );
    
    public function getUserPassword($username) {
        if (!array_key_exists($username, self::$users)) {
            return FALSE;
        }
        return self::$users[$username];
    }
    
}
```

Commonly account manager will somehow access database that contains user accounts information.

### 2. Init authentication service and perform authentication

Authentication service can be create using factory builder method available through `\phphttpauthserver\HttpAuth` class. In order to create the service you must provide type of authentication (`basic` or `digest`), an account manager instance and authentication realm name. A service that handles HTTP basic authentication can be created like this:

```php
$manager = new MySimpleAccountManager();
$service = \phphttpauthserver\HttpAuth::factory('basic', $manager, 'Test realm');
```

The created service can use its method `auth()` that performs authentication from information retrieved from the passed account manager and PHP server variables. Methods returns an instance of `\phphttpauthserver\HttpAuthResponse` that contains following properties:

- `passed` defines whether client request passed authentication,
- `errors` contains an array of errors if request not passed,
- `headers` contains HTTP headers that should be send in the server response.

Example of performing authentication with the created service:

```php
$response = $service->auth();
if ($response->isPassed()) {
    echo 'logged in as ' . $response->getUsername();
} else {
    $errors = implode("\n", $response->getErrors());
    if (headers_send()) {
        die('Authorization Required: ' . $errors);
    }
    header('HTTP/1.0 401 Authorization Required');
    foreach ($response->getHeaders() as $key => $value) {
        header($key . ': ' . $value);
    }
    die($errors);
}

```

