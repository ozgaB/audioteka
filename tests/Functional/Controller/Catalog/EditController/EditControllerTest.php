<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Catalog\EditController;


use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Tests\Functional\Controller\Catalog\AbstractCatalogProductController;
use App\Tests\Functional\Controller\Catalog\ListController\ListControllerFixture;
use App\Tests\Functional\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EditControllerTest.
 */
class EditControllerTest extends AbstractCatalogProductController
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures(new EditControllerFixture());
    }

    public function testEditProduct(): void
    {
        $this->doRequest([],json_encode([
            'name' => self::PRODUCT_NAME,
            'price' => self::PRODUCT_PRICE,
        ]));
        $this->checkAddEditResponse();
    }

    protected function doRequest(array $requestData = [], string $content = ''): void
    {
        $this->client->request('POST', '/products/fbcb8c51-5dcc-4fd4-a4cd-ceb9b400bff7/edit', content: $content);
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    protected function doRequestForProductWithEmptyName(): void
    {
        $this->client->request('POST', '/products/fbcb8c51-5dcc-4fd4-a4cd-ceb9b400bff7/edit',content: json_encode([
            'name' => '    ',
            'price' => 1990,
        ]));
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    protected function doRequestForProductWithoutPrice(): void
    {
        $this->client->request('POST', '/products/fbcb8c51-5dcc-4fd4-a4cd-ceb9b400bff7/edit',content: json_encode([
            'name' => 'Product name',
        ]));
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }

    protected function doRequestForProductWithNonPositive(): void
    {
        $this->client->request('POST', '/products/fbcb8c51-5dcc-4fd4-a4cd-ceb9b400bff7/edit',content: json_encode([
            'name' => 'Product name',
            'price' => 0,
        ]));
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);
    }
}