<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $gender;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['list_users', 'show_user'])]
    #[Assert\NotBlank]
    private string $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['list_users', 'show_user'])]
    #[Assert\NotBlank]
    private string $lastname;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['list_users', 'show_user'])]
    #[Assert\NotBlank]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_user'])]
    #[Assert\NotBlank]
    private string $phone_number;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_user'])]
    #[Assert\NotBlank]
    private string $addressLine1;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['show_user'])]
    private ?string $addressLine2;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_user'])]
    #[Assert\NotBlank]
    private string $zipcode;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_user'])]
    #[Assert\NotBlank]
    private string $city;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_user'])]
    #[Assert\NotBlank]
    private string $country;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'users')]
    private Customer $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
