<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

class UserRepository extends EntityRepository
{
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $userMetaData = new ClassMetadata(User::class);
        parent::__construct($entityManager, $userMetaData);
    }
}