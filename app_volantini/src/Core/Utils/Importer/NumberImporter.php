<?php
namespace App\Core\Utils\Importer;


/**
 * This class manage imports of Flyers objects from number file
 */
class NumberImporter extends SourceImporter
{
  
    /**
     * don't know why VSCode lint function sign, call perfectly work
     * eclipse not do it.
    */
    public static function importSource(string $filepath, array $fields = null,int $id = null, array $filters = null, $page = 1, $limit = 100): array 
    {
        throw new \Exception("Source type managment not yet implemented", 501);
    } 


}