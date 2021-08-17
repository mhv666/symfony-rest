<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends webTestCase
{
    //GET >> LIST
    public function testGetEmptyListProduct()
    {
        $client = static::createClient();
        $client->request('GET', '/api/products?per_page=5&q=NoExist&color=blue&page=1');

        //asssert when try to get an empty page
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetListProduct()
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    //POST
    public function testCreateProduct()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/products',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{
                "name": "Lorem",
                "description": "Qui optio  optio optio optio optio optio optio  optio optio optio optio consectetur ad ullam perspiciatis",
                "image": "http://lorempixel.com/400/200/food/1",
                "color": "blue",
                "merchant": "12",
                "category": "6",
                "price": "290.28",
                "ean13": "6938832514614",
                "stock": "15",
                "tax_percentage": "4"
            }'
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        $objContent = json_decode($content);
        $id = (!empty($objContent->id)) ? $objContent->id  : null;


        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($client->getResponse()->getContent());
        $this->assertIsInt($id);
    }

    public function testCreateWrongProduct()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/products',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{
                "name": "Lorem",
                "description": "Qui optio  optio optio optio optio optio optio  optio optio optio optio consectetur ad ullam perspiciatis",
                "image": "http://lorempixel.com/400/200/food/1",
                "color": "blue",
                "merchant": "12",
                "category": "6",
                "price": "-100",
                "ean13": "6938832514614",
                "stock": "15",
                "tax_percentage": "4"
            }'
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        $objContent = json_decode($content);
        $message = (!empty($objContent->message)) ? $objContent->message  : null;


        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($client->getResponse()->getContent());
        $this->assertSame('Validation Failed', $message);
    }



    //GET >> ONE
    public function testGetSingleProduct()
    {
        $client = static::createClient();
        $client->request('GET', '/api/products/41');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetSingleEmptyProduct()
    {
        $client = static::createClient();
        $client->request('GET', '/api/products/200000000');
        //assert 
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    // //PUT
    public function testUpdateProduct()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/products/41',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{
                "name": "Lorem",
                "description": "Qui optio  optio optio optio optio optio optio  optio optio optio optio consectetur ad ullam perspiciatis",
                "image": "http://lorempixel.com/400/200/food/1",
                "color": "blue",
                "merchant": "11",
                "category": "6",
                "price": "100",
                "ean13": "6938832514614",
                "stock": "15",
                "tax_percentage": "4"
            }'
        );
        $response = $client->getResponse();
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($client->getResponse()->getContent());
    }
    public function testUpdateProductMissingFields()
    {
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/products/41',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{
                "name": "Lorem"
                
            }'
        );
        $response = $client->getResponse();


        $this->assertSame(400, $client->getResponse()->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($client->getResponse()->getContent());
    }
    //DELETE
    public function testDeleteProduct()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/products/46');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testDeleteProductWrong()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/products/aaa');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
