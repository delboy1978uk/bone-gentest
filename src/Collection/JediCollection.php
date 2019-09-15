<?php

declare(strict_types=1);

namespace Random\Developer\Jedi\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use JsonSerializable;
use LogicException;
use Random\Developer\Jedi\Entity\Jedi;

class JediCollection extends ArrayCollection implements JsonSerializable
{
    /**
     * @param Jedi $jedi
     * @return $this
     * @throws LogicException
     */
    public function update(Jedi $jedi): JediCollection
    {
        $key = $this->findKey($jedi);
        if($key) {
            $this->offsetSet($key,$jedi);
            return $this;
        }
        throw new LogicException('Jedi was not in the collection.');
    }

    /**
     * @param Jedi $jedi
     */
    public function append(Jedi $jedi): void
    {
        $this->add($jedi);
    }

    /**
     * @return Jedi|null
     */
    public function current(): ?Jedi
    {
        return parent::current();
    }

    /**
     * @param Jedi $jedi
     * @return int|null
     */
    public function findKey(Jedi $jedi): ?int
    {
        $it = $this->getIterator();
        $it->rewind();
        while($it->valid()) {
            if($it->current()->getId() == $jedi->getId()) {
                return $it->key();
            }
            $it->next();
        }
        return null;
    }

    /**
     * @param int $id
     * @return Jedi|null
     */
    public function findById(int $id): ?Jedi
    {
        $it = $this->getIterator();
        $it->rewind();
        while($it->valid()) {
            if($it->current()->getId() == $id) {
                return $it->current();
            }
            $it->next();
        }
        return null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $collection = [];
        $it = $this->getIterator();
        $it->rewind();
        while($it->valid()) {
            /** @var Jedi $row */
            $row = $it->current();
            $collection[] = $row->toArray();
            $it->next();
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function jsonSerialize(): string
    {
        return \json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->jsonSerialize();
    }
}
