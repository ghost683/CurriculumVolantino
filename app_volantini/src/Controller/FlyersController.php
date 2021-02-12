<?php 

namespace App\Controller;
use App\Core\Utils\FlyersUtils;
use App\Core\Http\Response;

/**
 * This controller will return Flyers in json format
 *
 */
class FlyersController extends AppController
{

    /**
     * retrive json response
     *
     * @param number|null $page recived in query string, indicate the pagination.
     * @param number|null $limit recived in query string, indicate response chunk.
     * @param string[]|null $filter recived in query string, indicate filters values.
     * @param string|null $fields recived in query string, indicate requested fields list.
     * @return App\Core\Http\Response
     */
    public function index(): Response
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
                $response->responseError(400, "Bad Request", "Not allowed fields: {$list}");
            }
        }

        //check available filters list
        $notAllowedFiltersList = array_diff_key((array) $filters, array_flip(FlyersUtils::getAvailableFilters()));
        if(count($notAllowedFiltersList) > 0 ){
            $list = implode(",", array_keys($notAllowedFiltersList));
            $response->responseError(400, "Bad Request", "Not allowed filters: {$list}");
        }        
       
        $responseData = FlyersUtils::getFlyers((array) $filters, $fields, $page, $limit);
        if(count($responseData) == 0){
            $response->responseError(404, "Not found", "Not found");
        }
        $response->responseSuccess($responseData);
    }


    /**
     * retrive json response
     *
     * @param number|null $id, indicate flyers id.
     * @param string|null $fields recived in query string, indicate requested fields list.
     * @return App\Core\Http\Response
     */
    public function view($id): Response
    {
        $response = new Response();
        $fields = $this->request->getQuery('fields') !== null   ?
            explode(",", $this->request->getQuery('fields'))    : 
            null;

        // Check available fields list
        if($fields !== null && count($fields) > 0){
            $notExistingFieldsList = array_diff($fields, FlyersUtils::getAvailableFields());
            if(count($notExistingFieldsList) > 0){
                $list = implode(",", $notExistingFieldsList);
                $response->responseError(400, "Bad Request", "Not allowed fields: {$list}");
            }
        }

        $responseData = FlyersUtils::getFlyersById($id, $fields);

        if(count($responseData) == 0){
            $response->responseError(404, "Not found", "Not found");
        }
        $response->responseSuccess($responseData);
    }
}