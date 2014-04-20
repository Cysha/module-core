<?php

class PageControllerTest extends TestCase
{
    public function testIndex()
    {
        $crawler = $this->client->request('GET', 'api/v1/core/');

        $this->assertTrue($this->client->getResponse()->isOk());
    }
}
