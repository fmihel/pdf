<?php
namespace fmihel\pdf;

use fmihel\pdf\drivers\IPDFDriver;

class PDF
{

    private $driver;

    function __construct(IPDFDriver $driver)
    {
        $this->driver = $driver;
    }

    function countPage(string $pdf_filename)
    {
        if ($this->driver->enabled()) {
            return $this->driver->countPage($pdf_filename);
        }
        return 0;
    }

    function convert(string $pdf_filename, string $to_path, string $format = 'jpg', string $out_name_format = '$name_$i', array $param = []): array
    {
        if ($this->driver->enabled()) {
            return $this->driver->convert($pdf_filename, $to_path, $format, $out_name_format, $param);
        }
        return [];
    }

    public function extract(string $filename, $pageNum, string $outFileName = ''): string
    {
        if ($this->driver->enabled()) {
            return $this->driver->extract($filename, $pageNum, $outFileName);
        }
        return '';
    }

}
