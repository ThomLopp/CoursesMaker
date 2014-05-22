<?php

namespace Rotis\CourseMakerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Doctrine\ORM\NoResultException;

class EquipeRepository extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
        ;

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin RotisCourseMakerBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }

    public function findByJoinedCourseId($id)
    {
        $qb = $this
            ->createQueryBuilder('p')
            ->where('p.course = :id')
            ->setParameter('id', $id );

        $query = $qb->getQuery();
        $equipes = $query->getResult();
        return $equipes;
    }

    public function findLike($mot)
    {
        $qb = $this
            ->createQueryBuilder('e');

        $qb->where($qb->expr()->like('e.username', ':mot'))
            ->orderBy('e.username', 'ASC')
            ->setParameter('mot', '%'.$mot.'%');

        $query = $qb->getQuery();
        $equipes = $query->getResult();
        return $equipes;
    }

    public function findEquipesValides()
    {
        $qb = $this
            ->createQueryBuilder('e');
            $qb->select('count(e.id)')
                ->where('e.valide = true');
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }
}
