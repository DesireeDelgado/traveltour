<?php

namespace App\Entity;

use App\Repository\FavoritosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoritosRepository::class)]
class Favoritos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'favoritos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $id_usuario = null;

    #[ORM\ManyToOne(inversedBy: 'favoritos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Viaje $id_viaje = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUsuario(): ?Usuario
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(?Usuario $id_usuario): static
    {
        $this->id_usuario = $id_usuario;

        return $this;
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
}
