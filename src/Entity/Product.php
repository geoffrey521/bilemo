<?php

namespace App\Entity;

use App\Entity\Traits\EntityTimestampableTrait;
use App\Model\EntityTimestampableInterface;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product implements EntityTimestampableInterface
{
    use EntityTimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product', 'list_products'])]
    #[Assert\NotBlank]
    private string $brand;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product', 'list_products'])]
    #[Assert\NotBlank]
    private string $model;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['show_product'])]
    private string $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product'])]
    #[Assert\NotBlank]
    private string $screenSize;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product'])]
    #[Assert\NotBlank]
    private string $weight;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product'])]
    #[Assert\NotBlank]
    private string $processor;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product'])]
    #[Assert\NotBlank]
    private string $ram;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product', 'list_products'])]
    #[Assert\NotBlank]
    private string $storage;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_product'])]
    #[Assert\NotBlank]
    private string $camera;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getScreenSize(): ?string
    {
        return $this->screenSize;
    }

    public function setScreenSize(string $screenSize): self
    {
        $this->screenSize = $screenSize;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getProcessor(): ?string
    {
        return $this->processor;
    }

    public function setProcessor(string $processor): self
    {
        $this->processor = $processor;

        return $this;
    }

    public function getRam(): ?string
    {
        return $this->ram;
    }

    public function setRam(string $ram): self
    {
        $this->ram = $ram;

        return $this;
    }

    public function getStorage(): ?string
    {
        return $this->storage;
    }

    public function setStorage(string $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getCamera(): ?string
    {
        return $this->camera;
    }

    public function setCamera(string $camera): self
    {
        $this->camera = $camera;

        return $this;
    }
}
