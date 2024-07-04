<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
class Product implements \App\Service\Catalog\Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', nullable: false)]
    private UuidInterface $id;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'integer', nullable: false)]
    private string $price;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTime $createdAt;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: CartProduct::class,cascade: ['persist','remove'])]
    private Collection $cartProducts;

    public function __construct(string $id, string $name, int $price)
    {
        $this->id = Uuid::fromString($id);
        $this->name = $name;
        $this->price = $price;
        $this->cartProducts = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): Product
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<array-key,CartProduct>
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts->add($cartProduct);
            $cartProduct->setProduct($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            if ($cartProduct->getProduct()->getId() === $this->getId()) {
                $cartProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }
}
