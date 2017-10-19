<?php

/*
 * This file is part of the Atechnologies package.
 * 
 * (c) www.atechnologies.com.ve
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atechnologies\ToolsBundle\Service\LinkGenerator;

/**
 * Interface de generador de link
 * 
 * @author Máximo Sojo maxsojo13@gmail.com <maxtoan in atechnologies>
 */
interface LinkGeneratorInterface
{
    public static function getConfigObjects();
    
    public function getIconsDefinition();
}
