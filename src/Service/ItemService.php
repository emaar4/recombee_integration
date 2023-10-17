<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class ItemService
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @throws Exception
     */
    public function parseDatasetCSV(): array
    {
        $dir = $this->kernel->getProjectDir();
        $path = $dir . "/src/Dataset/books.csv";
        $fileHandle = fopen($path, 'r');
        $maxRowLength = 0;
        $headerRow = null;
        $result = [];
        $maxCounter = 0;

        while (($row = fgetcsv($fileHandle, $maxRowLength, ',')) !== false && $maxCounter < 1000) {
            if (!$headerRow) {
                $headerRow = array_map('trim', $row);
            } else {
                if (
                    !in_array('bookID', $headerRow) ||
                    !in_array('title', $headerRow) ||
                    !in_array('authors', $headerRow) ||
                    !in_array('average_rating', $headerRow) ||
                    !in_array('isbn', $headerRow) ||
                    !in_array('isbn13', $headerRow) ||
                    !in_array('language_code', $headerRow) ||
                    !in_array('num_pages', $headerRow) ||
                    !in_array('ratings_count', $headerRow) ||
                    !in_array('text_reviews_count', $headerRow) ||
                    !in_array('publication_date', $headerRow) ||
                    !in_array('publisher', $headerRow)
                ) {
                    throw new Exception('File header is not valid');
                }
            }
            $result[] = array_combine($headerRow, $row);
            $maxCounter++;
        }
        $removed = array_shift($result);
        return $result;
    }

    public function array_value_recursive($key, array $arr){
        $val = array();
        array_walk_recursive($arr, function($v, $k) use($key, &$val){
            if($k == $key) array_push($val, $v);
        });
        return count($val) > 1 ? $val : array_pop($val);
    }
}
