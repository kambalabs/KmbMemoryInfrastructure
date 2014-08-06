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
namespace KmbMemoryInfrastructure;

use KmbDomain\Model\Environment;
use KmbDomain\Model\EnvironmentRepositoryInterface;
use KmbDomain\Model\User;
use KmbDomain\Model\UserInterface;
use KmbDomain\Model\UserRepositoryInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Class Fixtures
 *
 * (1) STABLE
 *      — (4) PF1
 *            - (7) ITG
 *                  - (17) ITG1
 *                  - (18) ITG2
 *            - (8) PRP
 *            - (9) PROD
 *      - (5) PF2
 *            - (10) ITG
 *            - (11) PRP
 *            - (12) PROD
 *      - (6) PF3
 *            - (13) PROD
 *
 * (2) UNSTABLE
 *      — (14) PF1
 *      - (15) PF2
 *      - (16) PF3
 *
 * (3) DEFAULT (*)
 *
 * @package KmbMemoryInfrastructure
 */
trait Fixtures
{
    /** @var UserRepositoryInterface */
    protected $userRepository;

    /** @var EnvironmentRepositoryInterface */
    protected $environmentRepository;

    /**
     * @return ServiceManager
     */
    abstract public function getServiceManager();

    public function initFixtures()
    {
        $this->setUserRepository($this->getServiceManager()->get('UserRepository'));
        $this->setEnvironmentRepository($this->getServiceManager()->get('EnvironmentRepository'));

        $jdoe = $this->createUser(1, 'jdoe', 'John DOE', 'jdoe@gmail.com', UserInterface::ROLE_ROOT);
        $jmiller = $this->createUser(2, 'jmiller', 'Jane MILLER', 'jmiller@gmail.com', UserInterface::ROLE_ROOT);
        $psmith = $this->createUser(3, 'psmith', 'Paul SMITH', 'psmith@gmail.com', UserInterface::ROLE_ADMIN);
        $mcollins = $this->createUser(4, 'mcollins', 'Mike COLLINS', 'mcollins@gmail.com', UserInterface::ROLE_USER);
        $madams = $this->createUser(5, 'madams', 'Martin ADAMS', 'madams@gmail.com', UserInterface::ROLE_USER);
        $nmurphy = $this->createUser(6, 'nmurphy', 'Nick MURPHY', 'nmurphy@gmail.com', UserInterface::ROLE_ADMIN);
        $pmatthews = $this->createUser(7, 'pmatthews', 'Paul MATTHEWS', 'pmatthews@gmail.com', UserInterface::ROLE_ROOT);

        $this->userRepository->setAggregateRoots([
            $jdoe,
            $jmiller,
            $psmith,
            $mcollins,
            $madams,
            $nmurphy,
            $pmatthews,
        ]);

        $stable = $this->createEnvironment(1, 'STABLE');
        $unstable = $this->createEnvironment(2, 'UNSTABLE');
        $default = $this->createEnvironment(3, 'DEFAULT', true);
        $stablePF1 = $this->createEnvironment(4, 'PF1');
        $stablePF2 = $this->createEnvironment(5, 'PF2');
        $stablePF3 = $this->createEnvironment(6, 'PF3');
        $stablePF1Itg = $this->createEnvironment(7, 'ITG');
        $stablePF1Prp = $this->createEnvironment(8, 'PRP');
        $stablePF1Prod = $this->createEnvironment(9, 'PROD');
        $stablePF2Itg = $this->createEnvironment(10, 'ITG');
        $stablePF2Prp = $this->createEnvironment(11, 'PRP');
        $stablePF2Prod = $this->createEnvironment(12, 'PROD');
        $stablePF3Prod = $this->createEnvironment(13, 'PROD');
        $unstablePF1 = $this->createEnvironment(14, 'PF1');
        $unstablePF2 = $this->createEnvironment(15, 'PF2');
        $unstablePF3 = $this->createEnvironment(16, 'PF3');
        $stablePF1ItgItg1 = $this->createEnvironment(17, 'ITG1');
        $stablePF1ItgItg2 = $this->createEnvironment(18, 'ITG2');

        $stable->addChild($stablePF1);
        $stable->addChild($stablePF2);
        $stable->addChild($stablePF3);

        $unstable->addChild($unstablePF1);
        $unstable->addChild($unstablePF2);
        $unstable->addChild($unstablePF3);

        $stablePF1->setParent($stable);
        $stablePF1->addChild($stablePF1Itg);
        $stablePF1->addChild($stablePF1Prp);
        $stablePF1->addChild($stablePF1Prod);
        $stablePF1->setUsers([$psmith, $mcollins]);
        $psmith->addEnvironment($stablePF1);
        $mcollins->addEnvironment($stablePF1);

        $stablePF2->setParent($stable);
        $stablePF2->addChild($stablePF2Itg);
        $stablePF2->addChild($stablePF2Prp);
        $stablePF2->addChild($stablePF2Prod);
        $stablePF2->setUsers([$psmith]);
        $psmith->addEnvironment($stablePF2);

        $stablePF3->setParent($stable);
        $stablePF3->addChild($stablePF3Prod);
        $stablePF3->setUsers([$mcollins]);
        $mcollins->addEnvironment($stablePF3);

        $unstablePF1->setParent($unstable);
        $unstablePF1->setUsers([$psmith, $mcollins]);
        $psmith->addEnvironment($unstablePF1);
        $mcollins->addEnvironment($unstablePF1);

        $unstablePF2->setParent($unstable);
        $unstablePF2->setUsers([$psmith]);
        $psmith->addEnvironment($unstablePF2);

        $unstablePF3->setParent($unstable);
        $unstablePF3->setUsers([$mcollins]);
        $mcollins->addEnvironment($unstablePF3);

        $stablePF1Itg->setParent($stablePF1);
        $stablePF1Itg->addChild($stablePF1ItgItg1);
        $stablePF1Itg->addChild($stablePF1ItgItg2);
        $stablePF1Itg->setUsers([$psmith]);
        $psmith->addEnvironment($stablePF1Itg);

        $stablePF1Prp->setParent($stablePF1);

        $stablePF1Prod->setParent($stablePF1);

        $stablePF2Itg->setParent($stablePF2);

        $stablePF2Prp->setParent($stablePF2);

        $stablePF2Prod->setParent($stablePF2);

        $stablePF3Prod->setParent($stablePF3);

        $stablePF1ItgItg1->setParent($stablePF1Itg);

        $stablePF1ItgItg2->setParent($stablePF1Itg);

        $this->environmentRepository->setAggregateRoots([
            $stable,
            $unstable,
            $default,
            $stablePF1,
            $stablePF2,
            $stablePF3,
            $stablePF1Itg,
            $stablePF1Prp,
            $stablePF1Prod,
            $stablePF2Itg,
            $stablePF2Prp,
            $stablePF2Prod,
            $stablePF3Prod,
            $unstablePF1,
            $unstablePF2,
            $unstablePF3,
            $stablePF1ItgItg1,
            $stablePF1ItgItg2,
        ]);
    }

    public function createUser($id = null, $login = null, $name = null, $email = null, $role = null)
    {
        $user = new User($login, $name, $email, $role);
        $user->setId($id);
        return $user;
    }

    public function createEnvironment($id = null, $name = null, $default = false)
    {
        $environment = new Environment();
        $environment->setId($id);
        $environment->setName($name);
        $environment->setDefault($default);
        return $environment;
    }

    /**
     * Set EnvironmentRepository.
     *
     * @param \KmbDomain\Model\EnvironmentRepositoryInterface $environmentRepository
     * @return Fixtures
     */
    public function setEnvironmentRepository($environmentRepository)
    {
        $this->environmentRepository = $environmentRepository;
        return $this;
    }

    /**
     * Get EnvironmentRepository.
     *
     * @return \KmbDomain\Model\EnvironmentRepositoryInterface
     */
    public function getEnvironmentRepository()
    {
        return $this->environmentRepository;
    }

    /**
     * Set UserRepository.
     *
     * @param \KmbDomain\Model\UserRepositoryInterface $userRepository
     * @return Fixtures
     */
    public function setUserRepository($userRepository)
    {
        $this->userRepository = $userRepository;
        return $this;
    }

    /**
     * Get UserRepository.
     *
     * @return \KmbDomain\Model\UserRepositoryInterface
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }
}
