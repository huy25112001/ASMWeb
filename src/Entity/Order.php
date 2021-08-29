<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, inversedBy="orders")
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Quantity;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $CustomerName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $CustomerAddress;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $Phone;

    public function __construct()
    {
        $this->name = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Product[]
     */
    public function getName(): Collection
    {
        return $this->name;
    }

    public function addName(Product $name): self
    {
        if (!$this->name->contains($name)) {
            $this->name[] = $name;
        }

        return $this;
    }

    public function removeName(Product $name): self
    {
        $this->name->removeElement($name);

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(?int $Quantity): self
    {
        $this->Quantity = $Quantity;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->CustomerName;
    }

    public function setCustomerName(?string $CustomerName): self
    {
        $this->CustomerName = $CustomerName;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->CustomerAddress;
    }

    public function setCustomerAddress(?string $CustomerAddress): self
    {
        $this->CustomerAddress = $CustomerAddress;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->Phone;
    }

    public function setPhone(?string $Phone): self
    {
        $this->Phone = $Phone;

        return $this;
    }
}
