<?php

/*
 * This file is part of the Atechnologies package.
 * 
 * (c) www.atechnologies.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atechnologies\ToolsBundle\Repository;

/**
 * EntityRepository
 *
 * @author Máximo Sojo maxsojo13@gmail.com <maxtoan at atechnologies>
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EntityRepository extends \Doctrine\ORM\EntityRepository
{   
    public function findByNot($field, $value)
    {
        $qb = $this->createQueryBuilder('a');
        $qb->where($qb->expr()->not($qb->expr()->eq('a.'.$field, '?1')));
        $qb->setParameter(1, $value);

        return $qb->getQuery()
            ->getResult();
    }
    
	/**
     * Retorna un contructor de consultas con el alias de la clase
     * @return \Doctrine\ORM\QueryBuilder
     */
    function getQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlies());
    }
    
    /**
     * Retorna una consulta con los elementos activos
     * @return \Doctrine\ORM\QueryBuilder
     */
    function getQueryAllActives()
    {
        $qb = $this->getQueryBuilder();
        $qb
            ->andWhere($this->getAlies().".enabled = :enabled")
            ->setParameter("enabled", true)
        ;
        return $qb;
    }
    
    /**
     * Retorna los elementos activos
     * @return \Doctrine\ORM\QueryBuilder
     */
    function getAllActives()
    {
        $qb = $this->getQueryAllActives();        
        return $qb->getQuery()->getResult();
    }

    /**
     * Query paginator
     * @author Máximo Sojo maxsojo13@gmail.com <maxtoan at atechnologies>
     * @param  array
     * @param  array
     * @return [type]
     */
    public function createPaginator(array $criteria = array(),array $order = array()) 
    {
        $query = $this->getQueryBuilder();
        return $query->getQuery()->getArrayResult();
    }
}
