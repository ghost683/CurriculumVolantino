<?php 

namespace App\Controller;



use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\BadRequestException;

use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

use App\Core\Utils\FlyersUtils;

/**
 * This controller will return Flyers in json format
 *
 */
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
        if($page == null || intval($page) < 1){
            $page = 1;
        }

        $limit = $this->request->getQuery('limit');
        if($limit == null || intval($limit) < 1){
            $limit = 100;
        }

        $filters = $this->request->getQuery('filter'); 
        $fields = $this->request->getQuery('fields') !== null   ?
            explode(",", $this->request->getQuery('fields'))    : 
            null;

        if($fields !== null && count($fields) > 0){
            $notExistingFieldsList = array_diff($this->getAvailableFields(), $fields);
            if(count($notExistingFieldsList) > 0){
                $list = implode(",", $notExistingFieldsList);
                return new Response([$this->responseError(400, "Bad Request", "Not allowed fields: {$list}")]);
                /* $this->set('volantini', $this->responseError(400, "Bad Request", "Not allowed fields: {$list}"));
                $this->viewBuilder()->setOption('serialize', ['volantini']); */
            }
        }
        

        $notAllowedFiltersList = array_diff_key((array) $filters, $this->getAvailableFilters());
        
        if(count($notAllowedFiltersList) > 0 ){
            $list = implode(",", array_keys($notAllowedFiltersList));
            return new Response([$this->responseError(400, "Bad Request", "Not allowed filters: {$list}")]);
            
            /* $this->set('volantini', $this->responseError(400, "Bad Request", "Not allowed filters: {$list}"));
            $this->viewBuilder()->setOption('serialize', ['volantini']); */
        }        

        $responseData = $this->readCsv((array) $filters, $fields, $page, $limit);

        if(count($responseData) == 0){
            $this->set('volantini', $this->responseError(404, "Not found", "Not found"));
            $this->viewBuilder()->setOption('serialize', ['volantini']);
        }
       /*  $response = $this->response->withStringBody($this->responseSuccess($responseData));
        return $response; */
        //return new Response([$this->responseSuccess($responseData)]);

       
        $this->set('results', $this->responseSuccess($responseData));
        $this->viewBuilder()->setOption('serialize', ['results']);
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
       // $headerNames;
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
                    }

                    $added++;
                    
                }else if($data[0] == 'id'){ //temporaneo, la lettura del file è da rifattorizzare.
                    //mi salvo gli indici posizionali dei campi richiesti
                    if($fields !== null){
                        foreach($fields as $field){
                            $headersCsvIndexes []= array_search($field, $data);
                            // recupero il nome dei campi che voglio.
                            //potrei far collassare le due cose.
                            $headerNames = array_map(function ($index) use ($data) { return $data[$index]; } , $headersCsvIndexes);
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

    private function responseError($errorCode, $message, $debug) {

        $error = [
            "success" => false,
            "code" => $errorCode,
            "error" => [
                "message" => $message,
                "debug" => $debug
            ]
        ];
        return json_encode($error);
    }

    private function responseSuccess($results){

        $response = [
            "success" => true,
            "code" => 200,
            "results" => $results
        ];
        $response = mb_convert_encoding($response, 'UTF-8', 'UTF-8');
        return json_encode($response);
    } 

}