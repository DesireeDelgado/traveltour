<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $tipo = null;

    #[ORM\Column(length: 100)]
    private ?string $nickname = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    private ?string $contrasenia = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $biografia = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fecha_registro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url_foto_perfil = null;

    /**
     * @var Collection<int, Viaje>
     */
    #[ORM\OneToMany(targetEntity: Viaje::class, mappedBy: 'id_usuario')]
    private Collection $viajes;

    /**
     * @var Collection<int, Comentario>
     */
    #[ORM\OneToMany(targetEntity: Comentario::class, mappedBy: 'id_usuario')]
    private Collection $comentarios;

    /**
     * @var Collection<int, Favoritos>
     */
    #[ORM\OneToMany(targetEntity: Favoritos::class, mappedBy: 'id_usuario', orphanRemoval: true)]
    private Collection $favoritos;

    public function __construct()
    {
        $this->viajes = new ArrayCollection();
        $this->comentarios = new ArrayCollection();
        $this->favoritos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): static
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getContrasenia(): ?string
    {
        return $this->contrasenia;
    }

    public function setContrasenia(string $contrasenia): static
    {
        $this->contrasenia = $contrasenia;

        return $this;
    }

    public function getBiografia(): ?string
    {
        return $this->biografia;
    }

    public function setBiografia(?string $biografia): static
    {
        $this->biografia = $biografia;

        return $this;
    }

    public function getFechaRegistro(): ?\DateTimeImmutable
    {
        return $this->fecha_registro;
    }

    public function setFechaRegistro(\DateTimeImmutable $fecha_registro): static
    {
        $this->fecha_registro = $fecha_registro;

        return $this;
    }

    public function getUrlFotoPerfil(): ?string
    {
        return $this->url_foto_perfil;
    }

    public function setUrlFotoPerfil(?string $url_foto_perfil): static
    {
        $this->url_foto_perfil = $url_foto_perfil;

        return $this;
    }

    /**
     * @return Collection<int, Viaje>
     */
    public function getViajes(): Collection
    {
        return $this->viajes;
    }

    public function addViaje(Viaje $viaje): static
    {
        if (!$this->viajes->contains($viaje)) {
            $this->viajes->add($viaje);
            $viaje->setIdUsuario($this);
        }

        return $this;
    }

    public function removeViaje(Viaje $viaje): static
    {
        if ($this->viajes->removeElement($viaje)) {
            // set the owning side to null (unless already changed)
            if ($viaje->getIdUsuario() === $this) {
                $viaje->setIdUsuario(null);
            }
        }

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
            $comentario->setIdUsuario($this);
        }

        return $this;
    }

    public function removeComentario(Comentario $comentario): static
    {
        if ($this->comentarios->removeElement($comentario)) {
            // set the owning side to null (unless already changed)
            if ($comentario->getIdUsuario() === $this) {
                $comentario->setIdUsuario(null);
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
            $favorito->setIdUsuario($this);
        }

        return $this;
    }

    public function removeFavorito(Favoritos $favorito): static
    {
        if ($this->favoritos->removeElement($favorito)) {
            // set the owning side to null (unless already changed)
            if ($favorito->getIdUsuario() === $this) {
                $favorito->setIdUsuario(null);
            }
        }

        return $this;
    }
}
