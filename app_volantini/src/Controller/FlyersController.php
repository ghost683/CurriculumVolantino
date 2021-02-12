<?php 

namespace App\Controller;
use App\Core\Utils\FlyersUtils;
use App\Core\Http\Response;
use Exception;

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
        if($fields !== null && count($fields) > 0 && $invalidFields = FlyersUtils::checkValidFields($fields)){
            $response->responseError(400, "Bad Request", "Not allowed fields: {$invalidFields}");
        }

        //check available filters list
        if($filters !== null && $invalidFilters = FlyersUtils::checkValidFilters($filters)){
            $response->responseError(400, "Bad Request", "Not allowed filters: {$invalidFilters}");
        }   
       
        try{
            if(!$responseData = FlyersUtils::getFlyers((array) $filters, $fields, $page, $limit)){
                $response->responseError(404, "Not found", "Not found");
            }
            $response->responseSuccess($responseData);

        }catch( Exception $e) {
            $response->responseError($e->getCode(), $e->getMessage());
        }
    }

    /**
     * retrive json response for requested flyer
     *
     * @param number|null $id, indicate flyer id.
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
        if($fields !== null && count($fields) > 0 && $invalidFields = FlyersUtils::checkValidFields($fields)){
            $response->responseError(400, "Bad Request", "Not allowed fields: {$invalidFields}");
        }

        try{
            if(!$responseData = FlyersUtils::getFlyersById($id, $fields)){
                $response->responseError(404, "Not found", "Resource $id not found");
            }
            $response->responseSuccess($responseData);
        }catch( Exception $e){
            $response->responseError($e->getCode(), $e->getMessage());
        }
    }
}