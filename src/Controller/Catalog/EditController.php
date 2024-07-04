<?php

declare(strict_types=1);

namespace App\Controller\Catalog;


use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Messenger\AddProductToCatalog;
use App\Messenger\EditProduct;
use App\Messenger\MessageBusAwareInterface;
use App\Messenger\MessageBusTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EditController.
 */
#[Route(path: '/products/{product}/edit', name: 'catalog_edit', methods: ['POST'])]
class EditController extends AbstractController implements MessageBusAwareInterface
{
    use MessageBusTrait;

    public function __invoke(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (false === $form->isValid()) {
            $errors = $this->getFormErrors($form);
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        /** @var Product $product */
        $product = $form->getData();

        $this->dispatch(
            new EditProduct(
                $product->getId(),
                $product->getName(),
                $product->getPrice(),
            )
        );

        return new Response('', Response::HTTP_ACCEPTED);
    }

    private function getFormErrors(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}