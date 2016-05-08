#TwoFactor

Use Two Factor Authentication for Codiad.

##Installation

- Download the zip file and unzip it to your plugin folder.
- Define alternative Authentication in `config.php`, f.e.:
```php
	define("AUTH_PATH", "plugins/Codiad-TwoFactor-master/authenticator.php");
```

##Credits
- [GoogleAuthenticator from PHPGangsta](https://github.com/PHPGangsta/GoogleAuthenticator)