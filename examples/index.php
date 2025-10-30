<?php

use fmihel\pdf\drivers\GSDriver;
use fmihel\pdf\PDF;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/drivers/IPDFDriver.php';
require_once __DIR__ . '/../src/drivers/GSDriver.php';
require_once __DIR__ . '/../src/PDF.php';

$file = 'D:/work/fmihel/report/report/examples/media/doc3.pdf';

$pdf = new PDF(new GSDriver());
$pdf->convert($file, __DIR__ . '/tmp', 'png');
