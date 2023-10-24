<?php

namespace App\Entity;

use App\Repository\CancionesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CancionesRepository::class)]
class Canciones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre es obligatorio')]
    private ?string $nombre = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La duración es obligatoria')]
    private ?float $duracion = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Autor $autor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }


    public function getDuracion(): ?float
    {
        return $this->duracion;
    }

    public function setDuracion(float $duracion): static
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getAutor(): ?Autor
    {
        return $this->autor;
    }

    public function setAutor(?Autor $autor): static
    {
        $this->autor = $autor;

        return $this;
    }
}
