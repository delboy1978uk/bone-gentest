<?php

declare(strict_types=1);

namespace Random\Developer\Jedi\Service;

use DateTime;
use Doctrine\ORM\EntityManager;
use Random\Developer\Jedi\Entity\Jedi;
use Random\Developer\Jedi\Repository\JediRepository;

class JediService
{
    /** @var EntityManager $em */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $data
     * @return Jedi
     */
    public function createFromArray(array $data): Jedi
    {
        $jedi = new Jedi();

        return $this->updateFromArray($jedi, $data);
    }

    /**
     * @param Jedi $jedi
     * @param array $data
     * @return Jedi
     */
    public function updateFromArray(Jedi $jedi, array $data): Jedi
    {
        isset($data['id']) ? $jedi->setId($data['id']) : null;
        isset($data['name']) ? $jedi->setName($data['name']) : $jedi->setName('');
        isset($data['lightsaberColor']) ? $jedi->setLightsaberColor((int) $data['lightsaberColor']) : null;

        return $jedi;
    }

    /**
     * @param Jedi $jedi
     * @return Jedi
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveJedi(Jedi $jedi): Jedi
    {
        return $this->getRepository()->save($jedi);
    }

    /**
     * @param Jedi $jedi
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteJedi(Jedi $jedi): void
    {
        $this->getRepository()->delete($jedi);
    }

    /**
     * @return JediRepository
     */
    public function getRepository(): JediRepository
    {
        /** @var JediRepository $repository */
        $repository = $this->em->getRepository(Jedi::class);

        return $repository;
    }
}
