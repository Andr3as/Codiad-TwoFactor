# TwoFactor

Use Two Factor Authentication for Codiad.

## Requirements
- PHP < 7.2.0
- 2FA application like [Google Authenticator](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2)

## Installation

- Download the zip file and unzip it to your plugin folder.
- Define alternative Authentication in `config.php`, f.e.:
```php
	define("AUTH_PATH", BASE_PATH . "/plugins/Codiad-TwoFactor-master/authenticator.php");
```

## Credits
- [GoogleAuthenticator PHP Class from PHPGangsta](https://github.com/PHPGangsta/GoogleAuthenticator)