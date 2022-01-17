<?php

namespace App\Entity;

use App\Repository\AnimeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnimeRepository::class)
 */
class Anime
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img;

    /**
     * @ORM\OneToMany(targetEntity=Library::class, mappedBy="anime")
     */
    private $libraries;

    /**
     * @ORM\Column(type="integer")
     */
    private $ep;

    public function __construct()
    {
        $this->libraries = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Collection|Library[]
     */
    public function getLibraries(): Collection
    {
        return $this->libraries;
    }

    public function addLibrary(Library $library): self
    {
        if (!$this->libraries->contains($library)) {
            $this->libraries[] = $library;
            $library->setAnime($this);
        }

        return $this;
    }

    public function removeLibrary(Library $library): self
    {
        if ($this->libraries->removeElement($library)) {
            // set the owning side to null (unless already changed)
            if ($library->getAnime() === $this) {
                $library->setAnime(null);
            }
        }

        return $this;
    }

    public function getEp(): ?int
    {
        return $this->ep;
    }

    public function setEp(int $ep): self
    {
        $this->ep = $ep;

        return $this;
    }
}
