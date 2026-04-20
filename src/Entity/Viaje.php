<?php

namespace App\Entity;

use App\Repository\ViajeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ViajeRepository::class)]
class Viaje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'viajes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuario $id_usuario = null;

    #[ORM\Column(length: 150)]
    private ?string $titulo = null;

    #[ORM\Column(length: 150)]
    private ?string $destino = null;

    #[ORM\Column]
    private ?int $duracion = null;

    #[ORM\Column(nullable: true)]
    private ?float $presupuesto = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fecha_creacion = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenido = null;

    /**
     * @var Collection<int, Comentario>
     */
    #[ORM\OneToMany(targetEntity: Comentario::class, mappedBy: 'id_viaje', orphanRemoval: true)]
    private Collection $comentarios;

    /**
     * @var Collection<int, Imagen>
     */
    #[ORM\OneToMany(targetEntity: Imagen::class, mappedBy: 'id_viaje', orphanRemoval: true)]
    private Collection $imagenes;

    /**
     * @var Collection<int, Favoritos>
     */
    #[ORM\OneToMany(targetEntity: Favoritos::class, mappedBy: 'id_viaje', orphanRemoval: true)]
    private Collection $favoritos;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $alojamiento = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $gastronomia = null;

    public function __construct()
    {
        $this->comentarios = new ArrayCollection();
        $this->imagenes = new ArrayCollection();
        $this->favoritos = new ArrayCollection();
        $this->fecha_creacion = new \DateTimeImmutable();
    }

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

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDestino(): ?string
    {
        return $this->destino;
    }

    public function setDestino(string $destino): static
    {
        $this->destino = $destino;

        return $this;
    }

    public function getDuracion(): ?int
    {
        return $this->duracion;
    }

    public function setDuracion(int $duracion): static
    {
        $this->duracion = $duracion;

        return $this;
    }

    public function getPresupuesto(): ?float
    {
        return $this->presupuesto;
    }

    public function setPresupuesto(?float $presupuesto): static
    {
        $this->presupuesto = $presupuesto;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeImmutable
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion(\DateTimeImmutable $fecha_creacion): static
    {
        $this->fecha_creacion = $fecha_creacion;

        return $this;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    /**
     * @return Collection<int, Comentario>
     */
    public function getComentarios(): Collection
    {
        return $this->comentarios;
    }

    public function addComentario(Comentario $comentario): static
    {
        if (!$this->comentarios->contains($comentario)) {
            $this->comentarios->add($comentario);
            $comentario->setIdViaje($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): static
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getIdViaje() === $this) {
                $comentario->setIdViaje(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Imagen>
     */
    public function getImagenes(): Collection
    {
        return $this->imagenes;
    }

    public function addImagene(Imagen $imagene): static
    {
        if (!$this->imagenes->contains($imagene)) {
            $this->imagenes->add($imagene);
            $imagene->setIdViaje($this);
        }

        return $this;
    }

    public function removeImagene(Imagen $imagene): static
    {
        if ($this->imagenes->removeElement($imagene)) {
            // set the owning side to null (unless already changed)
            if ($imagene->getIdViaje() === $this) {
                $imagene->setIdViaje(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favoritos>
     */
    public function getFavoritos(): Collection
    {
        return $this->favoritos;
    }

    public function addFavorito(Favoritos $favorito): static
    {
        if (!$this->favoritos->contains($favorito)) {
            $this->favoritos->add($favorito);
            $favorito->setIdViaje($this);
        }

        return $this;
    }

    public function removeFavorito(Favoritos $favorito): static
    {
        if ($this->favoritos->removeElement($favorito)) {
            // set the owning side to null (unless already changed)
            if ($favorito->getIdViaje() === $this) {
                $favorito->setIdViaje(null);
            }
        }

        return $this;
    }

    public function getAlojamiento(): ?string
    {
        return $this->alojamiento;
    }

    public function setAlojamiento(?string $alojamiento): static
    {
        $this->alojamiento = $alojamiento;

        return $this;
    }

    public function getGastronomia(): ?string
    {
        return $this->gastronomia;
    }

    public function setGastronomia(?string $gastronomia): static
    {
        $this->gastronomia = $gastronomia;

        return $this;
    }
}
