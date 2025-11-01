# pdf v0.3.0

short pdf utilities

install
```bash
$ composer require fmihel/pdf
```

example use ghostscript ```require``` install ghostscript (see: https://www.ghostscript.com)

```php
<?php

use fmihel\pdf\drivers\GSDriver;
use fmihel\pdf\PDF;

require_once __DIR__ . '/vendor/autoload.php';

$file = 'D:/work/fmihel/report/report/examples/media/doc4.pdf';

$pdf = new PDF(new GSDriver());
$pdf->convert($file, __DIR__ . '/tmp', 'jpg', '$name_$i', ['dpi' => 150]);

```

example use Imagick ```!! not fully implemented !!```

```php
<?php

use fmihel\pdf\drivers\ImagickDriver;
use fmihel\pdf\PDF;

require_once __DIR__ . '/vendor/autoload.php';

$file = 'D:/work/fmihel/report/report/examples/media/doc4.pdf';

$pdf = new PDF(new ImagickDriver());
$pdf->convert($file, __DIR__ . '/tmp', 'jpg', '$name_$i', ['dpi' => 150]);

```


## PDF class methods

``countPage($filename)`` - return count page in pdf file

|param|notes|
|---|---|
|$filename| pdf file name|

---
``conver($filename,$to_path,$format,$outFileFormat,$param):array`` - convert pdf file to  graph file format, return list of created files

|param|notes|
|---|---|
|$filename |pdf file name|
|$to_path |dir to save result (must exists!!)|
|$format |format out graph file ( commonly  'jpg')|
|$outFileFormat | template out filename , ex: 'new-$name-$i'|
|$param | addition driver format (see driver) |

---
``extract($filename,$numPage,$outFileName):string`` - extract pdf page from multi pages pdf doc, return list paths to extracted files

|param|notes|
|---|---|
|$filename|pdf file name|
|$numPage |page number (first page is 1)|
|$outFileName | name of result file|
---
``info($filename):array `` - return list of info for pdf pages (orientation and width & height)
|param|notes|
|---|---|
|$filename | pdf file name|
