<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{


    #[ORM\Column(type: 'datetime_immutable', options:["default" => "CURRENT_TIMESTAMP"])]
    private $createdAt;


     #[ORM\Column(type:"datetime", options:["default" => "CURRENT_TIMESTAMP"])]
    private $updatedAt;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

     #[ORM\PrePersist]
     #[ORM\PreUpdate]
    public function updateTimestamps(){
        if($this->getCreatedAt() === null){
            $this->setCreatedAt(new \DateTimeImmutable());
        }
        $this->setUpdatedAt(new \DateTime());
    }

}