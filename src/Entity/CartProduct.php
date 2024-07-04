<?php

declare(strict_types=1);

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Class CartProduct.
 */
#[ORM\Entity]
class CartProduct
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', nullable: false)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'cartProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'cartProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private Cart $cart;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): CartProduct
    {
        $this->product = $product;
        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): CartProduct
    {
        $this->cart = $cart;
        return $this;
    }
}