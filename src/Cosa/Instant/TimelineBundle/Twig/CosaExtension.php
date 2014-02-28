<?php

namespace Cosa\Instant\TimelineBundle\Twig;

class CosaExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'datedelta' => new \Twig_Filter_Method($this, 'datedeltaFilter'),
            'customlink' => new \Twig_Filter_Method($this, 'customlinkFilter'),
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
          return $date->format('m-d-Y');
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

    public function customlinkFilter($text)
    {
    //  setlocale(LC_CTYPE,"fr_FR.ISO-8859-1");
      $patterns = array(
        '/(http\S*)/S',
        '/(#\w*)\s*/Su',
        '/\s*@(\w*)\s*/Su',
      );
      $replacements = array(
        '<a href="$1" target="_blank">$1</a>',
        '<a href="http://twitter.com/search?src=hash&q=$1" target="_blank">$1</a> ',
        '<a href="#" onclick="return searchFrom(\'$1\');">@$1</a> ',
      );
      return preg_replace($patterns,$replacements,$text);
    }

    public function getName()
    {
        return 'cosa_extension';
    }
}
