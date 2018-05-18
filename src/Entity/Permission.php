<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PermissionRepository")
 */
class Permission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canWrite;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Taldea", inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taldea;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Karpeta", inversedBy="permissions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $karpeta;

    /*****************************************************************************************************************/
    /*** ERLAZIOAK ***************************************************************************************************/
    /*****************************************************************************************************************/

    public function getId()
    {
        return $this->id;
    }

    public function getCanWrite(): ?bool
    {
        return $this->canWrite;
    }

    public function setCanWrite(bool $canWrite): self
    {
        $this->canWrite = $canWrite;

        return $this;
    }

    public function getTaldea(): ?taldea
    {
        return $this->taldea;
    }

    public function setTaldea(?taldea $taldea): self
    {
        $this->taldea = $taldea;

        return $this;
    }

    public function getKarpeta(): ?karpeta
    {
        return $this->karpeta;
    }

    public function setKarpeta(?karpeta $karpeta): self
    {
        $this->karpeta = $karpeta;

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
