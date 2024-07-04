<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Catalog;


use App\Tests\Functional\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractCatalogProductController.
 */
abstract class AbstractCatalogProductController extends WebTestCase
{
    const PRODUCT_NAME = 'Product name';
    const PRODUCT_PRICE = 1990;

    public function test_product_with_empty_name_cannot_be_added(): void
    {
        $this->doRequestForProductWithEmptyName(json_encode([
            'name' => '    ',
            'price' => 1990,
        ]));

        self::assertResponseStatusCodeSame(422);

        $response = $this->getJsonResponse();
        self::assertequals('Invalid name or price.', $response['error_message']);
    }

    public function test_product_without_a_price_cannot_be_added(): void
    {
        $this->doRequestForProductWithoutPrice(json_encode([
            'name' => 'Product name',
        ]));

        self::assertResponseStatusCodeSame(422);

        $response = $this->getJsonResponse();
        self::assertequals('Invalid name or price.', $response['error_message']);
    }

    public function test_product_with_non_positive_price_cannot_be_added(): void
    {
        $this->doRequestForProductWithNonPositive(json_encode([
            'name' => 'Product name',
            'price' => 0,
        ]));

        self::assertResponseStatusCodeSame(422);

        $response = $this->getJsonResponse();
        self::assertequals('Invalid name or price.', $response['error_message']);
    }

    protected function checkAddEditResponse(): void
    {
        $this->client->request('GET', '/products');
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = $this->getJsonResponse();
        self::assertCount(1, $response['products']);
        self::assertequals(self::PRODUCT_NAME, $response['products'][0]['name']);
        self::assertequals(self::PRODUCT_PRICE, $response['products'][0]['price']);
    }

    abstract protected function doRequest(array $requestData = [], string $content = ''): void;

    abstract protected function doRequestForProductWithEmptyName(): void;
    abstract protected function doRequestForProductWithoutPrice(): void;
    abstract protected function doRequestForProductWithNonPositive(): void;
}