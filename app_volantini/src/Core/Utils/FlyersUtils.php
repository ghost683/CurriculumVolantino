<?php

namespace App\Core\Utils;

/**
 * Flyers Utils class. used for manage flyers imports.
 *
 * Provides features for reading different kind of flyers source
 * and provide helper functions.
 *
 */
class FlyersUtils
{
    /**
     * Used to read csv file by pagination
     * 
     * @param string[]|null $filters Variable to obtain
     * @param string[]|null $filds  list indicating requested fields
     * @param number $page indicating pagination
     * @param number $limit indicating resultset size limit
     * @return string[] Value extratted, or empty list.
     */
    public static function readCsv(array $filters = null, array $fields = null, $page = 1, $limit = 10): array
    {
        $line = 1;
        $added = 0;
        $res = [];
        $headerNames = self::getAvailableFields();
        //key id posizione param, valore valore richiesto
        $filtersMap = [];
        $chunkEnd = $page * $limit;
        $chunkStart = $chunkEnd - $limit + 1;

        if (($handle = fopen(getcwd() . "../../webroot/flyers_data.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $added <= $limit) {
                // manage header line
                if ($line == 1) {
                    if ($filters !== null) {
                        foreach (array_keys((array) $filters) as $filter) {
                            //creo mappa di appoggio indexPosizione => valRichiesto
                            $filtersMap[array_search($filter, $data)] = $filters[$filter];
                        }
                    }
                    $line++;
                    continue;
                }
                // manage pagination, chunk = $page * limit
                if($line < $chunkStart){
                    continue;
                }
                if($added >= $chunkEnd){
                    return $res;
                }
                //manage extraction
                if (trim($data[0]) !== '') {
                    $row = [];
                    if (count($filtersMap) > 0) {
                        foreach (array_keys($filtersMap) as $filterIndex) {
                            //check requested filters value
                            if ($data[$filterIndex] !== $filtersMap[$filterIndex]) {
                                continue 2;
                            }
                        }
                    }
                    if ($fields !== null) {
                        // devo recuperare l'indice dei vari fields dalla lista degli header
                        // i fields richiesti sono già corretti qua
                        foreach($fields as $field){
                            // get positional index of requested field
                            $row []= $data[array_search($field, $headerNames)];
                        }

                        $res[] = $row;
                    } else {
                        $res[] = $data;
                    }
                    $added++;
                }
            }
            fclose($handle);
        }
        return $res;
    }

    /**
     * Scaffolding function
     * return ordered array off header
     */
    public static function getAvailableFields(): array
    {
        return ["id", "title", "start_date", "end_date", "is_published", "retailer", "category"];
    }

    /**
     * TODO trova soluzione per togliere valori
     * quindi rimandare il compito all'utilizzatore.
     */
    public static function getAvailableFilters(): array
    {
        return ["category" => 0, "is_published" => 0];
    }

     /**
     * @param string[] $row containig ordered row data
     * 
     * 
     */
    private function extractRow(array $row){

    }
}
