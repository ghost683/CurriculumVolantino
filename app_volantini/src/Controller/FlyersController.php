<?php 

namespace App\Controller;
use App\Core\Utils\FlyersUtils;
use App\Core\Utils\ResponseUtils;
use Exception;

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
     * retrive json response
     * @param number|null $page recived in query string, indicate the pagination.
     * @param number|null $limit recived in query string, indicate response chunk.
     * @param string[]|null $filter recived in query string, indicate filters values.
     * @param string|null $fields recived in query string, indicate requested fields list.
     * @return App\Core\Http\Response
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
        
        try {
            // Check available fields list
            if($fields !== null && count($fields) > 0 && $invalidFields = FlyersUtils::checkValidFields($fields)){
                $this->responseError(ResponseUtils::formatResponseError(400, "Bad Request", "Not allowed fields: {$invalidFields}"));
            }else if($filters !== null && $invalidFilters = FlyersUtils::checkValidFilters($filters)){
                //check available filters list
                $this->responseError(ResponseUtils::formatResponseError(400, "Bad Request", "Not allowed filters: {$invalidFilters}"));
            }else if(!$responseData = FlyersUtils::getFlyers((array) $filters, $fields, $page, $limit)){
                $this->responseError(ResponseUtils::formatResponseError(404, "Not found", "Not found"));
            }else {
                $this->responseSuccess(ResponseUtils::formatResponseSuccess($responseData));
            }

        } catch(Exception $e){
            $this->responseError(ResponseUtils::formatResponseError($e->getCode(), $e->getMessage()));
        }
    }

    /**
     * retrive json response for requested flyer
     *
     * @param number|null $id, indicate flyer id.
     * @param string|null $fields recived in query string, indicate requested fields list.
     * @return App\Core\Http\Response
     */
    public function view($id)
    {
        $fields = $this->request->getQuery('fields') !== null   ?
            explode(",", $this->request->getQuery('fields'))    : 
            null;

        try{
            // Check available fields list
            if($fields !== null && count($fields) > 0 && $invalidFields = FlyersUtils::checkValidFields($fields)){
                $this->responseError(ResponseUtils::formatResponseError(400, "Bad Request", "Not allowed fields: {$invalidFields}"));
            }else if(!$responseData = FlyersUtils::getFlyer($id, $fields)){
                $this->responseError(ResponseUtils::formatResponseError(404, "Not found", "Resource $id not found"));
            }else {
                $this->responseSuccess(ResponseUtils::formatResponseSuccess($responseData));
            }
        }catch (Exception $e){
            $this->responseError(ResponseUtils::formatResponseError($e->getCode(), $e->getMessage()));
        }
    }

    /**
     * helper function that format and send error response
     * TO be refactor somewhere
     * @param array @response the response array 
     */
    private function responseError($response) {
        $this->set(
            [   
                'success' => $response['success'],
                'code' => $response['code'],
                'error' => $response['error']
            ]);
        
        $this->response = $this->response->withStatus($response['code']);
        $this->viewBuilder()
            ->setOption('serialize', ['success','code','error']);
    }

     /**
     * helper function that format and send success response
     * TO be refactor somewhere
     * @param array @response the response array 
     */
    private function responseSuccess($response){
        $this->set(
            [   
                'success' => $response['success'],
                'code' => $response['code'],
                'results' => $response['results']
            ]);
        $this->viewBuilder()
            ->setOption('serialize', ['success','code', 'results']);
    }
}