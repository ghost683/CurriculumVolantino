<?php

namespace App\Core\Utils;

class FlyersUtils
{

    public static function getVolantini(){
        return ['a', 'b', 'c'];
    }

     /**
     * Working on function
     * andrà spostata nelle utils
     */
    public static function readCsv($filters, $fields, $page = 1, $limit = 10){
        $line = 1;
        $added = 0;
        $res = [];
        $headerNames = self::getAvailableFields();
        $headersCsvIndexes = [];
        //key id posizione param, valore valore richiesto
        $filtersMap = [];
        //TODO relativizza path
        if (($handle = fopen(getcwd() . "../../webroot/flyers_data.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $added <= $limit) {
                $line++;
                if(trim($data[0]) !== '' && $data[0] !== 'id'){
                    $row = [];
                    if(count($filtersMap) > 0){
                        foreach(array_keys($filtersMap) as $filterIndex){
                            if($data[$filterIndex] !== $filtersMap[$filterIndex]){
                                continue 2;
                            }
                        }
                    }
                    if($fields !== null ){ // manca controllo su field accettati... TODO
                        foreach($headersCsvIndexes as $index){
                            $row []= $data[$index];
                        }
                        $res []= $row;
                    }else {
                        $res []= $data;
                    }

                    $added++;
                    
                }else if($data[0] == 'id'){ //temporaneo, la lettura del file è da rifattorizzare.
                    //mi salvo gli indici posizionali dei campi richiesti
                    if($fields !== null){
                        foreach($fields as $field){
                            $headersCsvIndexes []= array_search($field, $data);
                        }
                    }else {
                    
                    }
                    if($filters !== null){
                        foreach(array_keys((array) $filters) as $filter){

                            $filtersMap[array_search($filter, $data)] = $filters[$filter];
                        }
                    }
                }
            }
            fclose($handle);
        }
        return $res;
    }

       /**
     * TODO
     * Scaffolding function
     */
    public static function getAvailableFields () {
        return ["id","title","start_date","end_date","is_published","retailer","category"];
    }

    /**
     * TODO trova soluzione per togliere valori
     * quindi rimandare il compito all'utilizzatore.
     */
    public static function getAvailableFilters() {
        return ["category" => 0, "is_published" => 0];
    }

}