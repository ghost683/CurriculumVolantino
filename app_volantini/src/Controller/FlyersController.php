<?php 

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
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
     */
    public function index()
    {
        $page = $this->request->getQuery('page');
        $filters = array_intersect_key((array) $this->request->getQuery('filter'), ["category" => 0, "is_published" => 0]); 
        $fields = $this->request->getQuery('fields');

        $this->set('volantini', $this->readCsv((array) $filters, $fields !== null ?explode(',', $fields) : null));
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
        if (($handle = fopen("C:/xampp/htdocs/appvolantini/CurriculumVolantino/app_volantini/webroot/flyers_data.csv", "r")) !== FALSE) {
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

}