<?php 

namespace App\Controller;



use Cake\Core\Configure;

use App\Core\Utils\FlyersUtils;
use App\Core\Http\Response;

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
        $response = new Response();
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

        
        // Check available fields list
        if($fields !== null && count($fields) > 0){
            $notExistingFieldsList = array_diff($fields, FlyersUtils::getAvailableFields());
            if(count($notExistingFieldsList) > 0){
                $list = implode(",", $notExistingFieldsList);
                //TO be fefactor
                $response->responseError(400, "Bad Request", "Not allowed fields: {$list}");
            }
        }

        //check available filters list
        $notAllowedFiltersList = array_diff_key((array) $filters, FlyersUtils::getAvailableFilters());
        if(count($notAllowedFiltersList) > 0 ){
            $list = implode(",", array_keys($notAllowedFiltersList));
            //TO be refactor
            $response->responseError(400, "Bad Request", "Not allowed filters: {$list}");
        }        
       
        $responseData = FlyersUtils::readCsv((array) $filters, $fields, $page, $limit);
        if(count($responseData) == 0){
            //To be reactor
            $response->responseError(404, "Not found", "Not found");
        }

        //TO be refactor
        $response->responseSuccess($responseData);
    }


    public function view($id)
    {
        $recipe = $this->Volantini->get($id);
        $this->set('recipe', $recipe);
        $this->viewBuilder()->setOption('serialize', ['recipe']);
    }
}