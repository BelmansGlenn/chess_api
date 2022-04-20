<?php

namespace App\Service\EntityManager;

use Doctrine\ORM\EntityManagerInterface;

class EntityManagerService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create($object)
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    public function update()
    {
        $this->entityManager->flush();
    }

    public function delete($object)
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }


}