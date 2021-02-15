<?php

namespace App\Core\Utils\Importer;


/**
 * This class manage imports of Flyers objects from csv file
 */
class CsvImporter extends SourceImporter
{

    /**
     * don't know why VSCode lint function sign, call perfectly work
     * eclipse not do it.
     * import flyer(s) from csv source.
     */
    public static function importSource(string $filepath, array $fields = null,int $id = null, array $filters = null, $page = 1, $limit = 100): array  
    { 
        if (!file_exists($filepath)) {
            throw new \Exception('Resource file not found.', 404);
        }

        $line = 1;
        $added = 0;
        $res = []; 

        //for example purpose only.
        $headerNames = parent::getAvailableFields();

        $filtersMap = [];
        $chunkStart = ($page * $limit) - $limit + 1;

        // deprecable if known structure. maintained for example purpose only.
        $startDateIndex = array_search("start_date", $headerNames);
        $endDateIndex = array_search("end_date", $headerNames);
        $today = date("Y-m-d");


        if (($handle = fopen($filepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // manage header line
                if ($line == 1) {
                    if ($filters !== null) {
                        foreach (array_keys((array) $filters) as $filter) {
                            //filter map helper.
                            $filtersMap[array_search($filter, $data)] = $filters[$filter];
                        }
                    }

                    //possible header recovery. not done for example purpose only

                    $line++;
                    continue;
                }

                //eluding controls for id search
                //if recive id, i always want get the record 
                if ($id !== null) {
                    if ($id == $data[array_search("id", $headerNames)]) {
                        return parent::extractRow($data, $fields);
                    }
                    continue;
                }
                //manage pagination, excluding invalid row and invalid flyers
                if (
                    trim($data[0]) == ''            ||
                    $data[$startDateIndex] > $today ||
                    $data[$endDateIndex] < $today   ||
                    $line++ <= $chunkStart
                ) {

                    continue;
                }

                if ($added >= $limit) {
                    return $res;
                }

                //check filters value.
                if (count($filtersMap) > 0) {
                    foreach (array_keys($filtersMap) as $filterIndex) {
                        //check requested filters value based on filter map helper
                        if ($data[$filterIndex] !== $filtersMap[$filterIndex]) {
                            continue 2;
                        }
                    }
                }

                //manage extraction
                $row = parent::extractRow($data, $fields);
                if ($row != null) {
                    $res[] = $row;
                    $added++;
                }
            }
            fclose($handle);
        }
        return $res;
    }

}
