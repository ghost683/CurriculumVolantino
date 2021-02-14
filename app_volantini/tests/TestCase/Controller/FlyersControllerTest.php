<?php
declare(strict_types=1);

/**
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * flyersControllerTest class
 *
 * @uses \App\Controller\FlyersControllerTest
 */
class FlyersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * testMultipleGet method
     *
     * @return void
     */
    public function testMultipleGet()
    {
        $this->get('/flyers.json');
        $this->assertResponseSuccess();
        $this->get('/flyers.json');
        $this->assertResponseSuccess();
        $this->get('/flyers.json?page=2&limit=50&fields=title,category&filter[category]=Discount&filter[is_published]=1');
        $this->assertResponseSuccess();
    }

    public function testGetFlyer(){
        $this->get('/flyers/1.json');
        $this->assertResponseSuccess();
        $this->get('/flyers/127.json?fields=id');
        $this->assertResponseSuccess();
    }

    /**
     * test error retrive pagination flyers
     *
     * @return void
     */
    public function testNotFoundPaginationFlyers()
    {
        $this->get('/flyers.json?page=2&limit=50&fields=title,category&filter[category]=Discount&filter[is_published]=0');
        $this->assertResponseError();
        $this->get('/flyers.json?page=3&limit=50&fields=title,category&filter[category]=&filter[is_published]=1');
        $this->assertResponseError();
        $this->get('/flyers.json');
    }

      /**
     * test error retrive flyer.
     *
     * @return void
     */
    public function testNotFoundFlyer()
    {
        $this->get("/flyers/190.json");
        $this->get('/flyers.json?page=2&limit=50&fields=title,category&filter[category]=Discount&filter[is_published]=0');
        $this->assertResponseError();
        $this->get('/flyers.json?page=3&limit=50&fields=title,category&filter[category]=&filter[is_published]=1');
        $this->assertResponseError();
    }

    /**
     * test correct id recovery and fields list.
     */
    public function testIdResult(){
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $this->get('/flyers/1.json?fields=id,title,category');
        $this->assertResponseOk();

        $expected = [
            'success' => true, 
            'code' => 200, 
            'results' => [
                'id' => '1',
                'title' => 'Dpiu Fresco',
                'category' => 'Discount'
            ]
        ];
        $expected = json_encode($expected, JSON_PRETTY_PRINT);
        $this->assertEquals($expected, (string)$this->_response->getBody());
    }


    /**
     * test records per page results
     */
    public function testPAggingCountResult(){
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);

        //default page 1, limit 100
        $this->get('/flyers.json');
        $this->assertResponseOk();
        $this->assertEquals(100, count(json_decode((string)$this->_response->getBody())->results));

        // page 2, 14 valid results left
        $this->get('/flyers.json?page=2');
        $this->assertResponseOk();
        $this->assertEquals(14, count(json_decode((string)$this->_response->getBody())->results));
    }
}
