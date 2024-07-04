<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Catalog\EditController;


use App\Entity\Product;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class EditControllerFixture.
 */
class EditControllerFixture extends AbstractFixture
{
    const PRODUCT_ID_FOR_EDIT_CONTROLLER_TEST = 'fbcb8c51-5dcc-4fd4-a4cd-ceb9b400bff7';

    public function load(ObjectManager $manager): void
    {
        $products = [
            new Product(self::PRODUCT_ID_FOR_EDIT_CONTROLLER_TEST, 'Product name', 1990),
        ];

        foreach ($products as $product) {
            $manager->persist($product);
        }

        $manager->flush();
    }
}