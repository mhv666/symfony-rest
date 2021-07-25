<?php

namespace App\Form\Model;

use App\Entity\Categories;

class CategoriesDto
{
    public ?int $id = null;
    public ?string $name = null;

    public static function createFromCategory(Categories $category): self
    {
        $dto = new self();
        $dto->id = $category->getId();
        $dto->name = $category->getName();
        return $dto;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
