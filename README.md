## About ##
A library that allows you to check CNE website and get the information in JSON format.
## Installation##
    composer require nanoarmando/cnecheck
## Use ##
```php
require "vendor/autoload.php";
use CNE\Cne;

$search = new Cne('V','xxxxxxxx');
echo $search->search();
```
## Additional Settings ##
```php
//Another way to initialize
$search = new Cne();
$search->setData('V','xxxxxxxx');
//Using the CURL method
//See 'allow_url_fopen'
echo $search->search(CURL_METHOD);
```
## License ##
MIT License