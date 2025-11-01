<?php
namespace fmihel\pdf\drivers;

interface IPDFDriver
{
    const PORTRAIT  = 'portrait';
    const LANDSCAPE = 'landscape';
    public function enabled(): bool;
    public function countPage(string $filename);
    public function convert(string $filename, string $outPath, string $outFormat = 'jpg', string $outPrefixName = '$name_$i', array $param = []): array;
    public function extract(string $filename, $pageNum, string $outFileName = ''): string;
    public function info(string $filename): array;
}
