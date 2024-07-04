<?php

namespace App\Tests\Functional\Controller\Catalog\AddController;


use App\Tests\Functional\Controller\Catalog\AbstractCatalogProductController;
use App\Tests\Functional\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AddControllerTest extends AbstractCatalogProductController
{
    public function test_adds_product(): void
    {
        $this->doRequest([
            'name' => self::PRODUCT_NAME,
            'price' => self::PRODUCT_PRICE,
        ]);

        $this->checkAddEditResponse();
    }

    protected function doRequest(array $requestData = [], string $content = ''): void
    {
        $this->client->request('POST', '/products',$requestData);
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    protected function doRequestForProductWithEmptyName(): void
    {
        $this->client->request('POST', '/products',[
            'name' => '    ',
            'price' => 1990,
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    protected function doRequestForProductWithoutPrice(): void
    {
        $this->client->request('POST', '/products',[
            'name' => 'Product name',
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    protected function doRequestForProductWithNonPositive(): void
    {
        $this->client->request('POST', '/products',[
            'name' => 'Product name',
            'price' => 0,
        ]);
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }
}