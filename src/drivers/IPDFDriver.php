<?php
namespace fmihel\pdf\drivers;

interface IPDFDriver
{
    public function enabled(): bool;
    public function countPage(string $filename);
    public function convert(string $filename, string $outPath, string $outFormat = 'jpg', string $outPrefixName = '$name_$i', array $param = []);
}
