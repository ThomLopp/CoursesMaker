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

    public function countEquipesValides($numero = null)
    {
        $qb = $this
            ->createQueryBuilder('e');
            $qb->select('count(e.id)')
                ->where('e.valide = true');
        if($numero)
        {
            $qb->join('e.course', 'c')
                ->join('c.edition', 'ed')
                ->andWhere('ed.numero = :numero')
                ->setParameter('numero',$numero);
        }
        $count = $qb->getQuery()->getSingleScalarResult();
        return $count;
    }

    public function countEquipes($numero = null)
    {
        $qb = $this
            ->createQueryBuilder('e');
        $qb->select('count(e.id)');
        if($numero){
            $qb->join('e.course', 'c')
                ->join('c.edition', 'ed')
                ->andWhere('ed.numero = :numero')
                ->setParameter('numero',$numero);
        }
         return $qb->getQuery()->getSingleScalarResult();
    }

    public function countEquipesByCourse($course)
    {
        $qb = $this
            ->createQueryBuilder('e');
        $qb->select('count(e.id)');
        $qb->join('e.course', 'c','WITH','c.id =:course')
            ->setParameter('course',$course);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countEquipesValidesByCourse($course)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('count(e.id)');
        $qb->where('e.valide = true')
            ->join('e.course','c','WITH','c.id = :course')
            ->setParameter('course',$course);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findEdition($numero)
    {
        $qb = $this->createQueryBuilder('e');
        if($numero)
        {
            $qb->join('e.course', 'c')
                ->join('c.edition', 'ed')
                ->where('ed.numero = :numero')
                ->setParameter('numero',$numero);
        }
        return $qb->getQuery()->getResult();
    }

    public function export($numero)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT ed.id, '
                . 'e.username equipe, '
                . 'j.nom nom, '
                . 'j.prenom prenom, '
                . 'c.nom course, '
                . 'j.papiers_ok certif, '
                . 'j.etudiant etudiant, '
                . 'j.carte_ok carte, '
                . 'j.paiement_ok paiement  '
                . 'FROM RotisCourseMakerBundle:Edition ed '
                . 'JOIN RotisCourseMakerBundle:Course c WITH c.edition = ed.id '
                . 'JOIN RotisCourseMakerBundle:Equipe e WITH e.course = c.id '
                . 'JOIN RotisCourseMakerBundle:Joueur j WITH j.equipe = e.id '
                . 'WHERE ed.numero = :numero'
            )->setParameter('numero',$numero)
            ->getResult();
    }
}
