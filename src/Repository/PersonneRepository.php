<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Personne>
 *
 * @method Personne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personne[]    findAll()
 * @method Personne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    public function save(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Personne[] Returns an array of Personne objects
    */

     /**
     * 
     */
    public function findPersonneByAgeInterval($minAge, $maxAge) : array
    {   
        $qb = $this->createQueryBuilder('p');
        $qb = $this->createQueryBuilder('p');
      
        return $qb->getQuery()->getResult();             
    }


    // public function findPersonneByAgeInterval($minAge, $maxAge) : array
    // {   

    //     return $this->createQueryBuilder('p')
    //         ->andWhere('p.age >= : minAge and p.age <= :maxAge')
    //         ->setParameters(['minAge' => $minAge, 'maxAge' => $maxAge])
    //         ->getQuery()
    //         ->getResult();             
    // }


    /**
     * 
     */
    public function statsPersonneByAgeInterval($minAge, $maxAge) : array
    {
        $qb =  $this->createQueryBuilder('p')
            ->select('avg(p.age) as ageMoyen, count(p.id) as nombrePersonne');
            // ->andWhere('p.age >= :minAge and p.age <= :maxAge')
            
            // ->setParameters(['minAge' => $minAge, 'maxAge' => $maxAge])

            $this->addIntervalAge($qb, $minAge, $maxAge);
            return $qb->getQuery()->getScalarResult();
    }


    /**
     * Ajouter unique l'intervalle au niveau de notre QuerryBuild
     */
    private function addIntervalAge(QueryBuilder $qb, $minAge, $maxAge) 
    {
        $qb->andWhere('p.age >= :minAge and p.age <= :maxAge')
           ->setParameters(['minAge' => $minAge, 'maxAge' => $maxAge]);
    }




   /* public function findByExampleField($value): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.exampleField = :val')
           ->setParameter('val', $value)
           ->orderBy('p.id', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findOneBySomeField($value): ?Personne
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.exampleField = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   } */






}
