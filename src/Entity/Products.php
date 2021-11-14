<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 */
class Products
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
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=Taxes::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tax;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainDescription;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $originPrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainAttribute1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainAttribute2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainAttribute3;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $pictures = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qrCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descriptionShow1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descriptionShow2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descriptionShow3;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $workflowState;

    /**
     * @ORM\ManyToOne(targetEntity=Orders::class, inversedBy="products")
     */
    private $orders;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

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

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTax(): ?Taxes
    {
        return $this->tax;
    }

    public function setTax(?Taxes $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getMainDescription(): ?string
    {
        return $this->mainDescription;
    }

    public function setMainDescription(?string $mainDescription): self
    {
        $this->mainDescription = $mainDescription;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getOriginPrice(): ?int
    {
        return $this->originPrice;
    }

    public function setOriginPrice(?int $originPrice): self
    {
        $this->originPrice = $originPrice;

        return $this;
    }

    public function getMainAttribute1(): ?string
    {
        return $this->mainAttribute1;
    }

    public function setMainAttribute1(?string $mainAttribute1): self
    {
        $this->mainAttribute1 = $mainAttribute1;

        return $this;
    }

    public function getMainAttribute2(): ?string
    {
        return $this->mainAttribute2;
    }

    public function setMainAttribute2(?string $mainAttribute2): self
    {
        $this->mainAttribute2 = $mainAttribute2;

        return $this;
    }

    public function getMainAttribute3(): ?string
    {
        return $this->mainAttribute3;
    }

    public function setMainAttribute3(?string $mainAttribute3): self
    {
        $this->mainAttribute3 = $mainAttribute3;

        return $this;
    }

    public function getPictures(): ?array
    {
        return $this->pictures;
    }

    public function setPictures(?array $pictures): self
    {
        $this->pictures = $pictures;

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    public function setQrCode(?string $qrCode): self
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    public function getDescriptionShow1(): ?string
    {
        return $this->descriptionShow1;
    }

    public function setDescriptionShow1(?string $descriptionShow1): self
    {
        $this->descriptionShow1 = $descriptionShow1;

        return $this;
    }

    public function getDescriptionShow2(): ?string
    {
        return $this->descriptionShow2;
    }

    public function setDescriptionShow2(?string $descriptionShow2): self
    {
        $this->descriptionShow2 = $descriptionShow2;

        return $this;
    }

    public function getDescriptionShow3(): ?string
    {
        return $this->descriptionShow3;
    }

    public function setDescriptionShow3(?string $descriptionShow3): self
    {
        $this->descriptionShow3 = $descriptionShow3;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeImmutable $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getWorkflowState(): ?string
    {
        return $this->workflowState;
    }

    public function setWorkflowState(string $workflowState): self
    {
        $this->workflowState = $workflowState;

        return $this;
    }

    public function getOrders(): ?Orders
    {
        return $this->orders;
    }

    public function setOrders(?Orders $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
