<?php

namespace
{
    /**
     *
     * @param Mixed $datetime
     * @return \DateTime
     */
    function datetime($datetime)
    {
        if( is_string($datetime) )
        {
            return new \DateTime($datetime);
        }
        elseif( $datetime instanceof \DateTime )
        {
            return clone $datetime;
        }
        elseif( is_null($datetime) )
        {
            return new \DateTime();
        }
        else
        {
            throw new \InvalidArgumentException('$datetime must be a date/time string or a \DateTime');
        }
    }

    /**
     * Return the "now" DateTime, it could be overrided by overidding app['now']
     * @return \DateTime
     */
    function now()
    {
        $app = \Ongoo\Core\Configuration::getInstance()->get('application');
        return $app['now']();
    }

    function decimal($number)
    {
        return round($number, 2, PHP_ROUND_HALF_UP);
    }

    function trans($id, $values = array(), $domain = null, $locale = null)
    {
        $dependencyInjection = \Ongoo\Core\Configuration::getInstance()->get('application');
        if( !$dependencyInjection->offsetExists('translator'))
        {
            return $id;
        }
        return $dependencyInjection['translator']->trans($id, $values, $domain, $locale);
    }

    function transChoice($id, $number, $values = array(), $domain = null, $locale = null)
    {
        $dependencyInjection = \Ongoo\Core\Configuration::getInstance()->get('application');
        if( !$dependencyInjection->offsetExists('translator'))
        {
            return $id;
        }
        return $dependencyInjection['translator']->transChoice($id, $number, $values, $domain, $locale);
    }
}
