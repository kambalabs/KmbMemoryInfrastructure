<?php
/**
 * @copyright Copyright (c) 2014 Orange Applications for Business
 * @link      http://github.com/kambalabs for the sources repositories
 *
 * This file is part of Kamba.
 *
 * Kamba is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Kamba is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Kamba.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace KmbMemoryInfrastructure\Service;

use GtnPersistBase\Infrastructure\Memory;
use KmbDomain\Model\EnvironmentInterface;
use KmbDomain\Service\EnvironmentRepositoryInterface;
use KmbDomain\Model\UserInterface;

class EnvironmentRepository extends Memory\Repository implements EnvironmentRepositoryInterface
{
    /** @var EnvironmentInterface[] $aggregateRoots */
    protected $aggregateRoots;

    /**
     * @return EnvironmentInterface[]
     */
    public function getAllRoots()
    {
        $roots = [];
        foreach ($this->aggregateRoots as $aggregateRoot) {
            if (!$aggregateRoot->hasParent()) {
                $roots[] = $aggregateRoot;
            }
        }
        return $roots;
    }

    /**
     * @return EnvironmentInterface
     */
    public function getDefault()
    {
        foreach ($this->aggregateRoots as $aggregateRoot) {
            if ($aggregateRoot->isDefault()) {
                return $aggregateRoot;
            }
        }
        return null;
    }

    /**
     * @param string $name
     * @return EnvironmentInterface
     */
    public function getRootByName($name)
    {
        foreach ($this->getAllRoots() as $root) {
            if ($root->getName() === $name) {
                return $root;
            }
        }
        return null;
    }

    /**
     * @param EnvironmentInterface $environment
     * @return array
     */
    public function getAllChildren(EnvironmentInterface $environment)
    {
        return $environment->getChildren();
    }

    /**
     * @param EnvironmentInterface $environment
     * @return EnvironmentInterface
     */
    public function getParent(EnvironmentInterface $environment)
    {
        return $environment->getParent();
    }

    /**
     * @param UserInterface $user
     * @return EnvironmentInterface[]
     */
    public function getAllForUser(UserInterface $user)
    {
        $environments = [];
        foreach ($this->aggregateRoots as $aggregateRoot) {
            if ($aggregateRoot->hasUser($user)) {
                $environments[] = $aggregateRoot;
            }
        }
        return $environments;
    }
}
