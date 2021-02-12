<?php

namespace App\Core\Utils;

define("FLYERS_RESOURCES_PATH",  getcwd() . "../../webroot/flyers_resources/");
define("DEFAULT_CSV", "flyers_data.csv");


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
     * factory class for retrive flyers from different source
     * @param string[]|null $filters Variable to obtain
     * @param string[]|null $filds  list indicating requested fields
     * @param number $page indicating pagination
     * @param number $limit indicating resultset size limit
     * @return string[] Value extratted, or empty list.
     */
    public static function getFlyers(array $filters = null, array $fields = null, $page = 1, $limit = 10): array
    {
        //TODO manage different source
        return self::readCsv(FLYERS_RESOURCES_PATH . DEFAULT_CSV, $fields, null, $filters, $page, $limit);
    }

    public static function getFlyersById($id, array $fields = null): array
    {
        //TODO manage different source
        return self::readCsv(FLYERS_RESOURCES_PATH . DEFAULT_CSV, $fields, $id);
    }

    /**
     * Used to read csv file by pagination
     * 
     * @param string[]|null $filds  list indicating requested fields
     * @param number|null $id flyer id for specific search.
     * @param string[]|null $filters Variable to obtain
     * @param number|1 $page indicating pagination
     * @param number|100 $limit indicating resultset size limit
     * @return string[] Value extratted, or empty list.
     */
    private static function readCsv(string $filepath, array $fields = null, $id = null, array $filters = null, $page = 1, $limit = 100): array
    {

        if(!file_exists($filepath)){
            throw new \Exception('Resource file not found.', 404);
        }

        $line = 1;
        $added = 0;
        $res = [];

        //for example purpose only.
        $headerNames = self::getAvailableFields();

        $filtersMap = [];
        $chunkStart = ($page * $limit) - $limit + 1;

        // deprecable if known structure. maintained for example purpose only.
        $startDateIndex = array_search("start_date", $headerNames);
        $endDateIndex = array_search("end_date", $headerNames);
        $today = date("Y-m-d");


        if (($handle = fopen($filepath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $added <= $limit) {
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
                        return self::extractRow($data, $fields);
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
                $row = self::extractRow($data, $fields);
                if ($row != null) {
                    $res[] = $row;
                    $added++;
                }
            }
            fclose($handle);
        }
        return $res;
    }

    /**
     * @param string[] @fields list of requested fields
     * @return string|null
     */
    public static function checkValidFields(array $fields): ?string 
    {
        $notExistingFieldsList = array_diff($fields, self::getAvailableFields());
        if(count($notExistingFieldsList) > 0){
            $list = implode(",", $notExistingFieldsList);
            return $list;
        }else {
            return null;
        }
    }

    /**
     * @param string[] @filters list of requested filter in format key => val
     * @return string|null 
     */
    public static function checkValidFilters(array $filters): ?string
    {
        $invalidFilters = array_diff_key((array) $filters, array_flip(self::getAvailableFilters()));
        if(count($invalidFilters) > 0 ){
            $list = implode(",", array_keys($invalidFilters));
            return $list;
        }else {
            return null;
        }     
    }

    /**
     * Scaffolding function
     * @return string[] ordered header fields list
     */
    public static function getAvailableFields(): array
    {
        return ["id", "title", "start_date", "end_date", "is_published", "retailer", "category"];
    }

    /**
     * Scaffolding function
     * @return string[] available filters list
     */
    public static function getAvailableFilters(): array
    {
        return ["category", "is_published"];
    }

    /**
     * @param string[] $row ordered row data
     * @return string[] map of recovered key => value
     */
    private static function extractRow(array $data, $fields): array
    {
        $row = [];

        if ($fields !== null) {
            foreach ($fields as $field) {
                // retrive data by positional index of requested field
                $row[] = $data[array_search($field, self::getAvailableFields())];
            }
            return array_combine($fields, $row);
        } else {
            return  array_combine(self::getAvailableFields(), $data);
        }
    }
}
