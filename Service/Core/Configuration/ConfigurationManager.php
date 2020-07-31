<?php

/*
 * This file is part of the Maxtoan Tools package.
 * 
 * (c) https://maximosojo.github.io/tools-bundle
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Maxtoan\ToolsBundle\Service\Core\Configuration;

use App\Entity\M\Core\Configuration;
use Maxtoan\ToolsBundle\Service\BaseService;
use Maxtoan\ToolsBundle\Model\Core\Configuration\DefaultConfigurationWrapper;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Service configurations
 * @author Máximo Sojo <maxsojo13@gmail.com>
 */
class ConfigurationManager extends BaseService
{
    /**
     * @var \Maxtoan\ToolsBundle\Model\Core\Configuration\ConfigurationWrapper
     */
    private $configurationsWrapper = null;

    /**
            
     * Adaptador origen de los datos
     * @var Adapter\ConfigurationAdapterInterface
     */
    private $adapter;

    function __construct(Adapter\ConfigurationAdapterInterface $adapter,array $options = array())
    {
        if(!class_exists("Symfony\Component\OptionsResolver\OptionsResolver")){
            throw new \Exception(sprintf("The package '%s' is required, please install https://packagist.org/packages/symfony/options-resolver",'"symfony/options-resolver": "^3.1"'));
        }
        $this->setOptions($options);
        $this->adapter = $adapter;
        $this->configurationsWrapper = [];
        if($this->options["add_default_wrapper"] === true){
            $this->addWrapper(new DefaultConfigurationWrapper());
        }
    }

    /**
     * Añade un grupo de configuracion
     * @param \Maxtoan\ToolsBundle\Model\Core\Configuration\ConfigurationWrapper $configuration
     * @return \Maxtoan\ToolsBundle\Model\Core\Configuration\ConfigurationWrapper
     * @throws \RuntimeException
     */
    public function addWrapper(\Maxtoan\ToolsBundle\Model\Core\Configuration\ConfigurationWrapper $configuration) 
    {
        $name = strtoupper($configuration->getName());
        if($this->hasWrapper($name)){
            throw new \RuntimeException(sprintf("The configuration name '%s' already added",$configuration->getName()));
        }
        $configuration->setManager($this);
        $this->configurationsWrapper[$name] = $configuration;
        return $this;
    }

    /**
     * Retorna el wrapper de una configuracion
     * @param type $name
     * @return \Maxtoan\ToolsBundle\Model\Core\Configuration\ConfigurationWrapper
     * @throws \RuntimeException
     */
    public function getWrapper($name)
    {
        $name = strtoupper($name);
        if(!$this->hasWrapper($name)){
            throw new \RuntimeException(sprintf("The configuration name '%s' is not added",$name));
        }
        return $this->configurationsWrapper[$name];
    }
    
    public function hasWrapper($wrapperName,$throwException = false)
    {
        $wrapperName = strtoupper($wrapperName);
        $added = false;
        if(isset($this->configurationsWrapper[$wrapperName])){
            $added = true;
        }else{
            if($throwException === true){
                throw new \InvalidArgumentException(sprintf("The ConfigurationWrapper with name '%s' dont exist.",$wrapperName));
            }
        }
        return $added;
    }

    /**
     * Retorna el valor de la configuracion de la base de datos
     * 
     * @param string $key Indice de la configuracion
     * @param mixed $default Valor que se retornara en caso de que no exista el indice
     * @return mixed
     */
    function get($key,$wrapperName = null,$default = null)
    {
        // $configuration = $this->getConfiguration($key,$wrapperName);
        $configuration = $this->adapter->find($key);
        if($configuration !== null){
            $value = $configuration->getValue();
            // $value = $this->reverseTransform($configuration->getValue(), $configuration);
        }else{
            $value = $default;
        }
        return $value;
    }

    /**
     * Registra configuración
     * @author Máximo Sojo <maxsojo13@gmail.com>
     * @param  $key
     * @param  $value
     */
    public function set($key,$value)
    {
        $config = $this->getConfigurationKey($key);
        if ($config) {
            $config->setValue($value);
            $this->save($config,false);
        }

        return $config;
    }

    /**
     * Retorna la configuracion
     * @param type $key
     * @param type $wrapperName
     * @return \Maxtoan\ToolsBundle\Model\Core\Configuration\ConfigurationInterface
     */
    private function getConfiguration($key,$wrapperName = null)
    {
        if($wrapperName === null){
            $wrapperName = DefaultConfigurationWrapper::getName();
        }
        $key = strtoupper($key);
        $wrapperName = strtoupper($wrapperName);
        return $this->getWrapper($wrapperName);
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * cache_dir:     The cache directory (or null to disable caching)
     *   * debug:         Whether to enable debugging or not (false by default)
     *   * resource_type: Type hint for the main resource (optional)
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'debug'                  => false,
            'add_default_wrapper'  => false,
        ]);
        $resolver->addAllowedTypes("add_default_wrapper","boolean");
        
        $this->options = $resolver->resolve($options);
    }
}