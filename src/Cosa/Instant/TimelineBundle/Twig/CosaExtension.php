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
        if(is_string($date)){
          $date2 = new \DateTime();
          $date2->setTimestamp(strtotime($date));
          $date = $date2;
        }
        $now = new \DateTime();
        $diff = $now->diff($date);
        if($diff->y || $diff->m || $diff->d > 2)
          return $date.format('%m-%d-%Y');
        if($diff->d > 0)
          return $diff->d.' d ago';
        if($diff->h)
          return $diff->h.' h ago';
        if($diff->i)
          return $diff->i.' m ago';
        if($diff->s)
          return $diff->s.' s ago';
        return 'now';
    }

    public function getName()
    {
        return 'cosa_extension';
    }
}
