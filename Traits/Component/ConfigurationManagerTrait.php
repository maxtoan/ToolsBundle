<?php

/*
 * This file is part of the Maxtoan Tools package.
 * 
 * (c) https://maximosojo.github.io/tools-bundle
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maxtoan\ToolsBundle\Traits\Component;

/**
 * ConfigurationManagerTrait
 * 
 * @author Máximo Sojo <maxsojo13@gmail.com>
 */
trait ConfigurationManagerTrait
{    
    /**
     * Manejador de configuraciones
     *  
     * @author Máximo Sojo <maxsojo13@gmail.com>
     * @return ConfigurationManager
     */
    public function getConfigurationManager()
    {
        return $this->container->get("maxtoan_tools.manager.configuration");
    }
}