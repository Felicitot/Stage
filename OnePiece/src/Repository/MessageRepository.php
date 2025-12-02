<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }
    // src/Repository/MessageRepository.php

    public function findConversation($user1, $user2)
    {
        return $this->createQueryBuilder('m')
            ->where('(m.relation = :user1 AND m.receveur = :user2) OR (m.relation = :user2 AND m.receveur = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->orderBy('m.dateEnvoi', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findUnreadMessages($relation, $receveur)
    {
    return $this->createQueryBuilder('m')
        ->andWhere('m.relation = :relation')
        ->andWhere('m.receveur = :receveur')
        ->andWhere('m.luOuNon = false')
        ->setParameter('relation', $relation)
        ->setParameter('receveur', $receveur)


        ->getQuery()
        ->getResult();
    }
    
    /**
 * @return array<int, array{utilisateur: \App\Entity\Utilisateur, lastMessageDate: \DateTimeInterface}>
 */
public function findConversationsForUser($user): array
{
    $conn = $this->getEntityManager()->getConnection();

    $sql = <<<SQL
        SELECT 
            u.id,
            u.Prenoms,
            u.Nom,
            MAX(m.date_envoi) AS lastMessageDate
        FROM message m
        JOIN utilisateur u ON (
            u.id = CASE 
                WHEN m.relation_id = :user_id THEN m.receveur_id 
                ELSE m.relation_id 
            END
        )
        WHERE m.relation_id = :user_id OR m.receveur_id = :user_id
        GROUP BY u.id, u.Prenoms, u.Nom
        ORDER BY lastMessageDate DESC
    SQL;

    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery(['user_id' => $user->getId()]);

    return $result->fetchAllAssociative();
}






    //    /**
    //     * @return Message[] Returns an array of Message objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Message
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
