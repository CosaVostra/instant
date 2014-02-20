<?php

namespace Cosa\Instant\TimelineBundle\Twig;

class CosaExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'datedelta' => new \Twig_Filter_Method($this, 'datedeltaFilter'),
            'datedelta2' => new \Twig_Filter_Method($this, 'datedelta2Filter'),
        );
    }

    public function datedeltaFilter($date)
    {
        if(get_Class($date)=='string'){
            
        }
        $now = new \DateTime();
        $diff = $now->diff($date);
        if($diff->y || $diff->m || $diff->d > 2)
          return $date.format('%m-%d-%Y');
        if($diff->d > 0)
          return $diff->d.' d';
        if(($diff->h)-1) // HACK hour -1 due to insert created_at HACK in InstantController
          return (($diff->h)-1).' h';
        if($diff->i)
          return $diff->i.' m';
        if($diff->s)
          return $diff->s.' s';
        return 'now';
    }

    public function datedelta2Filter($date)
    {
        if(!get_Class($date)){
          $date2 = new \DateTime();
          $date2->setTimestamp(strtotime($date));
          $date = $date2;
        }
        $now = new \DateTime();
        $diff = $now->diff($date);
        if($diff->y>0 || $diff->m>0 || $diff->d > 2)
          return $date.format('%m-%d-%Y');
        if($diff->d > 0)
          return $diff->d.' d';
        if(($diff->h)-1)
          return (($diff->h)-1).' h';
        if($diff->i > 0)
          return $diff->i.' m';
        if($diff->s > 0)
          return $diff->s.' s';
        return 'now';
    }

    public function getName()
    {
        return 'cosa_extension';
    }
}
