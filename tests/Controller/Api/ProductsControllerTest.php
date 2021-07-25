<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends webTestCase
{
    public function testCreateProduct()
    {
        $client = static::createClient();
        $client->request('POST', '/api/products');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}
