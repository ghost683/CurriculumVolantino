<?php

namespace App\Core\Utils\Importer;

/**
 * This abstract class provide helper functions and declare import method
 */
abstract class SourceImporter
{
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
    abstract public static function importSource(string $filepath, array $fields = null,int $id = null, array $filters = null, $page = 1, $limit = 100);


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
    protected static function extractRow(array $data, array $fields = null): array
    {
        if($fields == null){
            $fields = self::getAvailableFields();
        }
        $row = [];
        foreach ($fields as $field) {
            // retrive data by positional index of requested field
            if($field == 'id' || $field == 'is_published'){
                $row []= (int)$data[array_search($field, self::getAvailableFields())];
            }else {
                $row[] = $data[array_search($field, self::getAvailableFields())];
            }
        }
        return array_combine($fields, $row);
    }
}
