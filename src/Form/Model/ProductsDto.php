<?php

namespace App\Form\Model;

use App\Entity\Products;
use App\Form\Model\CategoriesDto;
use App\Form\Model\MerchantsDto;
use DateTimeInterface;

class ProductsDto
{
    public ?string $name = null;
    public ?string $description = null;
    public ?int $image = null;
    public ?string $color = null;
    public ?string $price = null;
    public ?string $ean13 = null;
    public ?string $stock = null;
    public ?string $tax_percentage = null;
    /** @var \App\Form\Model\CategoriesDto|null */
    public ?CategoriesDto $category = null;
    /** @var \App\Form\Model\MerchantsDto|null */
    public ?MerchantsDto $merchant = null;
    public ?DateTimeInterface $created_at = null;

    public function __construct()
    {
        $this->categories = [];
    }

    public static function createEmpty(): self
    {
        return new self();
    }

    public static function createFromBook(Products $products): self
    {
        $dto = new self();
        $dto->title = $products->getName();
        $dto->description = $products->getDescription();
        $dto->color = $products->getColor();
        $dto->image = $products->getImage();
        $dto->price = $products->getPrice();
        $dto->ean13 = $products->getEan13();
        $dto->stock = $products->getStock();
        $dto->tax_percentage = $products->getTaxPercentage();

        $dto->created_at = $products->getCreatedAt();
        return $dto;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return \App\Form\Model\CategoriesDto|null
     */
    public function getCategories(): ?CategoriesDto
    {
        return $this->category;
    }

    /**
     * @return \App\Form\Model\MerchantDto|null
     */
    public function getMerchant(): ?MerchantsDto
    {
        return $this->merchant;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getReadAt(): ?DateTimeInterface
    {
        return $this->readAt;
    }
}
