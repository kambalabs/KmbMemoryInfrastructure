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
use KmbDomain\Model\UserInterface;
use KmbDomain\Service\UserRepositoryInterface;

class UserRepository extends Memory\Repository implements UserRepositoryInterface
{
    /**
     * @param $login
     * @return UserInterface
     */
    public function getByLogin($login)
    {
        foreach ($this->aggregateRoots as $aggregateRoot) {
            if ($aggregateRoot->getLogin() === $login) {
                return $aggregateRoot;
            }
        }
        return null;
    }

    /**
     * @param EnvironmentInterface $environment
     * @return array
     */
    public function getAllByEnvironment($environment)
    {
        return $environment->getUsers();
    }

    /**
     * @return array
     */
    public function getAllNonRoot()
    {
        $users = [];
        foreach ($this->aggregateRoots as $aggregateRoot) {
            if ($aggregateRoot->getRole() != UserInterface::ROLE_ROOT) {
                $users[] = $aggregateRoot;
            }
        }
        return $users;
    }

    /**
     * @param EnvironmentInterface $environment
     * @return array
     */
    public function getAllAvailableForEnvironment($environment)
    {
        $availableUsers = $this->getAllNonRoot();
        foreach ($environment->getUsers() as $user) {
            foreach ($availableUsers as $index => $availableUser) {
                if ($user->getId() == $availableUser->getId()) {
                    unset($availableUsers[$index]);
                }
            }
        }
        return array_values($availableUsers);
    }
}
