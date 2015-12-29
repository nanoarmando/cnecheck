## Acerca de ##
----------
Librería que permite consultar la página web del CNE y obtener la información en formato JSON.
## Instalación ##
    composer require nanoarmando/cnecheck
## Uso ##

```php
require "vendor/autoload.php";
use CNE\Cne;

$search = new Cne('V','xxxxxxxx');
$search->search();
```
## Licencia ##
Licencia MIT.