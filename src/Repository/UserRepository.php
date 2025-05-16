<?php

namespace App\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use MongoDB\BSON\Regex;

class UserRepository extends DocumentRepository
{
    /**
     * @throws MongoDBException
     */
    public function gerUsers($get): array
    {
        $options = $get['options'];
        $search = $get['search'];

        $qb = $this->createQueryBuilder();

        $qb->addOr($qb->expr()->field('username')->equals(new Regex('.*' . $search . '.*i')));

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

        $qb->sort('nombre', 'ASC');


        $response['count'] = $count;
        $response['data'] = $qb->getQuery()->execute();

        return $response;
    }

}