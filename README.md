# pdf v0.1.0

short pdf utilities

install
```bash
$ composer require fmihel/pdf
```

example use ghostscript ```requier``` install ghostscript (see: https://www.ghostscript.com)

```php
<?php

use fmihel\pdf\drivers\GSDriver;
use fmihel\pdf\PDF;

require_once __DIR__ . '/vendor/autoload.php';

$file = 'D:/work/fmihel/report/report/examples/media/doc4.pdf';

$pdf = new PDF(new GSDriver());
$pdf->convert($file, __DIR__ . '/tmp', 'jpg', '$name_$i', ['dpi' => 150]);

```
example use Imagick

```php
<?php

use fmihel\pdf\drivers\ImagickDriver;
use fmihel\pdf\PDF;

require_once __DIR__ . '/vendor/autoload.php';

$file = 'D:/work/fmihel/report/report/examples/media/doc4.pdf';

$pdf = new PDF(new ImagickDriver());
$pdf->convert($file, __DIR__ . '/tmp', 'jpg', '$name_$i', ['dpi' => 150]);

```


Api (PDF class)

|func|params|notes|
|---|---|---|
|countPage($filename)|$filename - pdf file name|return count page in pdf file|
|conver($filename,$to_path,$format,$outFileFormat,$param):array|$filename - pdf file name|convert pdf file to  graph file format, return list of created files|
||$to_path - dir to save result (must exists!!)||
||$format - format out graph file ( commonly  'jpg')||
||$outFileFormat - template out filename , ex: 'new-$name-$i'||
||$param - addition driver format (see driver) ||