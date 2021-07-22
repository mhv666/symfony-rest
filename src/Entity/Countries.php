<?php

namespace App\Entity;

use App\Repository\CountriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CountriesRepository::class)
 */
class Countries
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Merchants::class, mappedBy="country")
     */
    private $merchants;

    public function __construct()
    {
        $this->merchants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Merchants[]
     */
    public function getMerchants(): Collection
    {
        return $this->merchants;
    }

    public function addMerchant(Merchants $merchant): self
    {
        if (!$this->merchants->contains($merchant)) {
            $this->merchants[] = $merchant;
            $merchant->setCountry($this);
        }

        return $this;
    }

    public function removeMerchant(Merchants $merchant): self
    {
        if ($this->merchants->removeElement($merchant)) {
            // set the owning side to null (unless already changed)
            if ($merchant->getCountry() === $this) {
                $merchant->setCountry(null);
            }
        }

        return $this;
    }
}
