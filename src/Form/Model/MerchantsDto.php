<?php

namespace App\Form\Model;

use App\Entity\Merchants;

class MerchantsDto
{
    public ?int $id = null;
    public ?string $name = null;

    public static function createFromCategory(Merchants $merchant): self
    {
        $dto = new self();
        $dto->id = $merchant->getId();
        $dto->merchant_name = $merchant->getMerchantName();
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
