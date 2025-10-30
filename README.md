# pdf v0.0.2

short pdf utilities

install
```bash
$ composer require fmihel/pdf
```

example
```php
<?php

use fmihel\pdf\drivers\GSDriver;
use fmihel\pdf\PDF;

require_once __DIR__ . '/vendor/autoload.php';

$file = 'D:/work/fmihel/report/report/examples/media/doc4.pdf';

$pdf = new PDF(new GSDriver());
$pdf->convert($file, __DIR__ . '/tmp', 'jpg', '$name_$i', ['dpi' => 150]);

```

Api (PDF class)

|func|notes|
|---|---|
|countPage($filename)|return count page in pdf file|
|conver($filename,$to_path,$format)|return count page in pdf file|



