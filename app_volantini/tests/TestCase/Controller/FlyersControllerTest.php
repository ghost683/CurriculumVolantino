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

}
