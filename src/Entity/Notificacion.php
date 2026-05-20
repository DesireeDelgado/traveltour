<?php

namespace App\Entity;

use App\Repository\NotificacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificacionRepository::class)]
class Notificacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $mensaje = null;

    #[ORM\Column]
    private ?bool $leido = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'notificaciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'notificaciones')]
    private ?Viaje $viaje = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): static
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function isLeido(): ?bool
    {
        return $this->leido;
    }

    public function setLeido(bool $leido): static
    {
        $this->leido = $leido;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getViaje(): ?Viaje
    {
        return $this->viaje;
    }

    public function setViaje(?Viaje $viaje): static
    {
        $this->viaje = $viaje;

        return $this;
    }
}
