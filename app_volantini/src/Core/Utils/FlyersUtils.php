<?php

namespace App\Core\Utils;

define("FLYERS_RESOURCES_PATH", __DIR__ . '/../../../webroot/flyers_resources/');
define("DEFAULT_CSV", "flyers_data.csv");

use App\Core\Utils\Importer\CsvImporter;
use App\Core\Utils\Importer\NumberImporter;
use App\Core\Utils\Importer\SourceImporter;

/**
 * Flyers Utils class. used for manage flyers imports.
 *
 * Provides features for reading different kind of flyers source
 * and helper functions.
 *
 */
class FlyersUtils
{
    /**
     * retrive flyers paginated list
     * @param string[]|null $filters Variable to obtain
     * @param string[]|null $filds  list indicating requested fields
     * @param number $page indicating pagination
     * @param number $limit indicating resultset size limit
     * @return string[] Value extratted, or empty list.
     */
    public static function getFlyers(array $filters = null, array $fields = null, $page = 1, $limit = 10): array
    {
        /**
         * here can be implemented logic for source file choosen.
         */
        $filePath = FLYERS_RESOURCES_PATH . DEFAULT_CSV;
        return self::getImporter(DEFAULT_CSV)->importSource($filePath, $fields, null, $filters, $page, $limit);
    }

    /**
     * get a Flyer releted by his id.
     * @param number $id flyer id.
     * @return array flyer rappresentation.
     */
    public static function getFlyer(int $id, array $fields = null): array
    {
        /**
         * here can be implemented logic for source file choosen.
         */
        $filePath = FLYERS_RESOURCES_PATH . DEFAULT_CSV;
        return self::getImporter(DEFAULT_CSV)->importSource($filePath, $fields, $id);
    }

       /**
     * @param string[] @fields list of requested fields
     * @return string|null
     */
    public static function checkValidFields(array $fields): ?string
    {
        $notExistingFieldsList = array_diff($fields, SourceImporter::getAvailableFields());
        if (count($notExistingFieldsList) > 0) {
            $list = implode(",", $notExistingFieldsList);
            return $list;
        } else {
            return null;
        }
    }

    /**
     * @param string[] @filters list of requested filter in format key => val
     * @return string|null 
     */
    public static function checkValidFilters(array $filters): ?string
    {
        $invalidFilters = array_diff_key((array) $filters, array_flip(SourceImporter::getAvailableFilters()));
        if (count($invalidFilters) > 0) {
            $list = implode(",", array_keys($invalidFilters));
            return $list;
        } else {
            return null;
        }
    }
 
    /**
     * factory method that retrive the correct importer releted by file extension.
     * @param $fileName source file name.
     * @return SourceImporter concrete importer.
     */
    private static function getImporter(string $fileName): SourceImporter 
    {
        switch(explode(".",$fileName)[1]){
            case "csv":
               return new CsvImporter();
                break;
            case "number":
                return new NumberImporter(); 
                break;
            default:
                throw new \Exception("Invalid source type", 500);
        } 
    }

}
