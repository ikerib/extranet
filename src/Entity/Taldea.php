<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaldeaRepository")
 */
class Taldea
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Karpeta", mappedBy="taldeak")
     */
    private $karpetak;

    public function __construct()
    {
        $this->karpetak = new ArrayCollection();
        $this->enabled = 1;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /*****************************************************************************************************************/
    /*** FIN ERLAZIOAK ***********************************************************************************************/
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
     * @return Collection|Karpeta[]
     */
    public function getKarpetak(): Collection
    {
        return $this->karpetak;
    }

    public function addKarpetak(Karpeta $karpetak): self
    {
        if (!$this->karpetak->contains($karpetak)) {
            $this->karpetak[] = $karpetak;
            $karpetak->addRelation($this);
        }

        return $this;
    }

    public function removeKarpetak(Karpeta $karpetak): self
    {
        if ($this->karpetak->contains($karpetak)) {
            $this->karpetak->removeElement($karpetak);
            $karpetak->removeRelation($this);
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
}
