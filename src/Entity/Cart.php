<?php

namespace App\Entity;

use App\Service\Catalog\Product as ProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
class Cart implements \App\Service\Cart\Cart
{
    public const CAPACITY = 3;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', nullable: false)]
    private UuidInterface $id;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: CartProduct::class,cascade: ['persist','remove'], orphanRemoval: true)]
    private Collection $cartProducts;

    public function __construct(string $id)
    {
        $this->id = Uuid::fromString($id);
        $this->cartProducts = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getTotalPrice(): int
    {
        return array_reduce(
            $this->cartProducts->toArray(),
            static fn(int $total, CartProduct $cartProduct): int => $total + $cartProduct->getProduct()->getPrice(),
            0
        );
    }

    #[Pure]
    public function isFull(): bool
    {
        return $this->cartProducts->count() >= self::CAPACITY;
    }

    /**
     * @return Collection<array-key,ProductInterface>
     */
    public function getProducts(): Collection
    {
        return $this->cartProducts->map(function (CartProduct $cartProduct) {
            return $cartProduct->getProduct();
        });
    }

    #[Pure]
    public function hasProduct(Product $product): bool
    {
        return 0 < count($this->cartProducts->filter(function (CartProduct $cartProduct) use ($product): bool {
            return $cartProduct->getProduct()->getId() === $product->getId();
        }));
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        $this->cartProducts->add($cartProduct);

        return $this;
    }

    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->contains($cartProduct)) {
            $this->cartProducts->removeElement($cartProduct);
        }

        return $this;
    }

    public function addProduct(Product $product): self
    {
        if ($this->isFull()) {
            throw new \Exception('Cart is full.');
        }

        if ($this->hasProduct($product)) {
            throw new \Exception('Product is already in the cart.');
        }

        $cartProduct = new CartProduct();
        $cartProduct->setProduct($product);
        $cartProduct->setCart($this);
        $this->addCartProduct($cartProduct);

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $cartProduct = $this->cartProducts->filter(function (CartProduct $cartProduct) use ($product) {
            return $cartProduct->getProduct()->getId() === $product->getId();
        })->first();

        if (!$cartProduct) {
            throw new \Exception('Product not found in cart.');
        }

        $this->removeCartProduct($cartProduct);

        return $this;
    }
}
