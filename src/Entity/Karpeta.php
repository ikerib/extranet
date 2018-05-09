<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\KarpetaRepository")
 */
class Karpeta
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /*****************************************************************************************************************/
    /*** ERLAZIOAK ***************************************************************************************************/
    /*****************************************************************************************************************/

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Taldea", inversedBy="karpetak")
     */
    private $taldeak;

    public function __construct()
    {
        $this->taldeak = new ArrayCollection();
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->enabled = 1;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /*****************************************************************************************************************/
    /*** ERLAZIOAK ***************************************************************************************************/
    /*****************************************************************************************************************/

    public function getId()
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

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection|Taldea[]
     */
    public function getRelation(): Collection
    {
        return $this->taldeak;
    }

    public function addRelation(Taldea $relation): self
    {
        if (!$this->taldeak->contains($relation)) {
            $this->taldeak[] = $relation;
        }

        return $this;
    }

    public function removeRelation(Taldea $relation): self
    {
        if ($this->taldeak->contains($relation)) {
            $this->taldeak->removeElement($relation);
        }

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return Collection|Taldea[]
     */
    public function getTaldeak(): Collection
    {
        return $this->taldeak;
    }

    public function addTaldeak(Taldea $taldeak): self
    {
        if (!$this->taldeak->contains($taldeak)) {
            $this->taldeak[] = $taldeak;
        }

        return $this;
    }

    public function removeTaldeak(Taldea $taldeak): self
    {
        if ($this->taldeak->contains($taldeak)) {
            $this->taldeak->removeElement($taldeak);
        }

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }
}
