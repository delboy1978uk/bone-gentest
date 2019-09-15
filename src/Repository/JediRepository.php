<?php

declare(strict_types=1);

namespace Random\Developer\Jedi\Repository;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Random\Developer\Jedi\Collection\JediCollection;
use Random\Developer\Jedi\Entity\Jedi;

class JediRepository extends EntityRepository
{
    /**
     * @param int $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     * @return Jedi
     * @throws \Doctrine\ORM\ORMException
     */
    public function find($id, $lockMode = null, $lockVersion = null): Jedi
    {
        /** @var Jedi $jedi */
        $jedi =  parent::find($id, $lockMode, $lockVersion);
        if (!$jedi) {
            throw new EntityNotFoundException('Jedi not found.', 404);
        }

        return $jedi;
    }

    /**
     * @param Jedi $jedi
     * @return $jedi
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Jedi $jedi): Jedi
    {
        if(!$jedi->getID()) {
            $this->_em->persist($jedi);
        }
        $this->_em->flush($jedi);

        return $jedi;
    }

    /**
     * @param Jedi $jedi
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Jedi $jedi): void
    {
        $this->_em->remove($jedi);
        $this->_em->flush($jedi);
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalJediCount(): int
    {
        $qb = $this->createQueryBuilder('j');
        $qb->select('count(j.id)');
        $query = $qb->getQuery();

        return (int) $query->getSingleScalarResult();
    }
}
