<?php

namespace Cosa\Instant\TimelineBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * InstantRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InstantRepository extends EntityRepository
{

    public function getNbNewTweets($id,$last = 0)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('t.twitter_id,t.created_at')
            ->from('CosaInstantTimelineBundle:Instant', 'i')
            ->join('i.tweets', 't')
            ->where('i.id = '.$id)
            ->orderBy('t.created_at','DESC')
            ->getQuery();
        $tweet_ids = $q->getResult();
        $cpt = 0;
        foreach($tweet_ids as $tweet_id){
          if($last==$tweet_id['twitter_id'])
            break;
          $cpt++;
        }
        return $cpt;
    }

    public function getList($id, $off=0, $nb=100)
    {
        $q = $this->_em->createQueryBuilder()
            ->select('t,i')
            ->from('CosaInstantTimelineBundle:Instant', 'i')
            ->join('i.tweets', 't')
            ->where('i.id = '.$id)
            ->orderBy('t.created_at','DESC')
            ->setFirstResult($off)
            ->setMaxResults($nb);
 
        return $q->getQuery()->getResult();
    }

    public function getLastId()
    {
        try{
          return $this->getEntityManager()
            ->createQuery(
                'SELECT i.id
                 FROM CosaInstantTimelineBundle:Instant i
                 ORDER BY i.id DESC'
            )
            ->setMaxResults(1)
            ->getSingleResult();
        }catch(\Exception $e){
          return false;
        }
    }

    public function findByUserNTitle($user_id,$title)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT i
                 FROM CosaInstantTimelineBundle:Instant i
                 WHERE i.title=:title
                 AND i.user=:user_id
                 LIMIT 1'
            )
            ->setParameter('user_id',$user_id)
            ->setParameter('title',$title)
            ->setMaxResults(1)
            ->getResult();
    }

}
