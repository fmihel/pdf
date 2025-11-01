<?php

use fmihel\console;
use fmihel\pdf\drivers\GSDriver;
use fmihel\pdf\PDF;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/drivers/IPDFDriver.php';
require_once __DIR__ . '/../src/drivers/GSDriver.php';
require_once __DIR__ . '/../src/PDF.php';

$file = __DIR__ . '/media/doc3.pdf';

$driver = new GSDriver();
// $pdf    = new PDF($driver);
// $pdf = new PDF(new ImagickDriver());
// $pdf->convert($file, __DIR__ . '/tmp', 'pdf', '$name_$i', ['dpi' => 150]);

console::log($driver->info($file));
