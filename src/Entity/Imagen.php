<?php

namespace App\Entity;

use App\Repository\ImagenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagenRepository::class)]
class Imagen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'imagenes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Viaje $id_viaje = null;

    #[ORM\Column(length: 255)]
    private ?string $url_path = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdViaje(): ?Viaje
    {
        return $this->id_viaje;
    }

    public function setIdViaje(?Viaje $id_viaje): static
    {
        $this->id_viaje = $id_viaje;

        return $this;
    }

    public function getUrlPath(): ?string
    {
        return $this->url_path;
    }

    public function setUrlPath(string $url_path): static
    {
        $this->url_path = $url_path;

        return $this;
    }
}
