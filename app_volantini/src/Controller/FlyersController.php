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

        $this->set('volantini', $this->readCsv($filters));
        $this->viewBuilder()->setOption('serialize', ['volantini']);
    }


    public function view($id)
    {
        $recipe = $this->Volantini->get($id);
        $this->set('recipe', $recipe);
        $this->viewBuilder()->setOption('serialize', ['recipe']);
    }


    private function readCsv($filters, $page = 1, $limit = 10){
        
        $row = 1;
        $added = 0;
        $res = [];
        if (($handle = fopen("C:/xampp/htdocs/appvolantini/app_volantini/webroot/flyers_data.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && $added <= $limit) {
                $num = count($data);
                $row++;
                if(trim($data[0]) !== '' && $data[0] !== 'id'){
                    $res []= $data;
                    $added++;
                }
            }
            fclose($handle);
        }
        return $res;
    }

}