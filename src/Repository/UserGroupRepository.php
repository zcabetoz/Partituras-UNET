<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class UserGroupRepository extends DocumentRepository
{
    /**
     * @throws MongoDBException
     */
    public function findGroupByRole(string $role)
    {
        $qb = $this->createQueryBuilder();

        $qb->field('roles')->equals($role);

        return $qb->getQuery()->execute();
    }
}