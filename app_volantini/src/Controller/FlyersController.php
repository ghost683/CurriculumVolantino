<?php 

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\BadRequestException;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

use App\Utils\VolantiniUtils;

class FlyersController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Get all
     * assunto: i filtri ed i field devono essere scritti nello stesso formato (case sensitive).
     */
    public function index()
    {
        $page = $this->request->getQuery('page');
        $filters = $this->request->getQuery('filter'); 

        $fields = $this->request->getQuery('fields') !== null   ?
            explode(",", $this->request->getQuery('fields'))    : 
            null;

        if($fields !== null && count($fields) > 0){
            $notExistingFieldsList = array_diff($this->getAvailableFields(), $fields);
            if(count($notExistingFieldsList) > 0){
                $list = implode(",", $notExistingFieldsList);
                throw new BadRequestException("Not allowed fields: {$list}");
            }
        }

        $notAllowedFiltersList = array_diff_key((array) $filters, $this->getAvailableFilters());
        
        if(count($notAllowedFiltersList) > 0 ){
            $list = implode(",", array_keys($notAllowedFiltersList));
            throw new BadRequestException("Not allowed filters: {$list}.");
        }        

        $responseData = $this->readCsv((array) $filters, $fields);

        if(count($responseData) == 0){
            throw new NotFoundException();
        }

        $this->set('volantini', $responseData);
        $this->viewBuilder()->setOption('serialize', ['volantini']);
    }


    public function view($id)
    {
        $recipe = $this->Volantini->get($id);
        $this->set('recipe', $recipe);
        $this->viewBuilder()->setOption('serialize', ['recipe']);
    }


    /**
     * Working on function
     * andrà spostata nelle utils
     */
    private function readCsv($filters, $fields, $page = 1, $limit = 10){
        $row = 1;
        $added = 0;
        $res = [];
        $headersCsvIndexes = [];
        //key id posizione param, valore valore richiesto
        $filtersMap = [];
        //TODO relativizza path
        if (($handle = fopen(getcwd() . "../../webroot/flyers_data.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $added <= $limit) {
                $row++;
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
                        $row = $data;
                    }

                    $added++;
                    
                }else if($data[0] == 'id'){ //temporaneo, la lettura del file è da rifattorizzare.
                    //mi salvo gli indici posizionali dei campi richiesti
                    if($fields !== null){
                        foreach($fields as $field){
                            $headersCsvIndexes []= array_search($field, $data);
                        }
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
     * TODO rifattorizzare, da spostare 
     * Scaffolding function
     */
    private function getAvailableFields () {
        return ["id","title","start_date","end_date","is_published","retailer","category"];
    }

    /**
     * TODO rifattorizzare, da spostare, trova soluzione per togliere valori
     * quindi rimandare il compito all'utilizzatore.
     */
    private function getAvailableFilters() {
        return ["category" => 0, "is_published" => 0];
    }

}