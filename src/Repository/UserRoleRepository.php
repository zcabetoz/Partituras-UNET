<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class UserRoleRepository extends DocumentRepository
{
    /**
     * @throws MongoDBException
     */
    public function getRoles($options): iterable
    {
        $qb = $this->createQueryBuilder();

        $qb2 = clone $qb;
        $count = $qb2->count()->getQuery()->execute();

        if (isset($options['skip'])) {
            $skip = ($options['skip'] - 1) * $options['limit'];
            $qb->skip($skip);
        }

        if (isset($options['limit'])) {
            $limit = $options['limit'];
            $qb->limit($limit);
        }

        $qb->sort('role', 'ASC');
        $response['count'] = $count;
        $response['data'] = $qb->getQuery()->execute();

        return $response;
    }

    /**
     * @throws MongoDBException
     */
    public function getRolesFromGroup($roles, $exclude): iterable
    {
        $qb = $this->createQueryBuilder();

        $field = $qb->field('role');

        $exclude === 'false' ? $field->in($roles) : $field->notIn($roles);

        return $qb->getQuery()->execute();
    }
}