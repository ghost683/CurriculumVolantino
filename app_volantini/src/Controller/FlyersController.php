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
        $filters = $this->request->getQuery('filter');
        $fields = $this->request->getQuery('fields');

        $this->set('volantini', $this->readCsv($filters, explode(',', $fields)));
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
        if (($handle = fopen("C:/xampp/htdocs/appvolantini/CurriculumVolantino/app_volantini/webroot/flyers_data.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $added <= $limit) {
                $row++;
                if(trim($data[0]) !== '' && $data[0] !== 'id'){
                    $row = [];
                    if($fields !== null ){ // manca controllo su field accettati... TODO
                        foreach($headersCsvIndexes as $index){
                            $row []= $data[$index];
                        }
                    }else {
                        $row = $data;
                    }

                    $res []= $row;
                    $added++;
                    
                }else if($data[0] == 'id' && $fields !== null){ //temporaneo, la lettura del file è da rifattorizzare.
                    //mi salvo gli indici posizionali dei campi richiesti
                    foreach($fields as $field){
                        $headersCsvIndexes []= array_search($field, $data);
                    }
                }
            }
            fclose($handle);
        }
        return $res;
    }

}