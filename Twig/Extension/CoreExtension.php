<?php

namespace Atechnologies\ToolsBundle\Twig\Extension;

/**
 *     
 */
class CoreExtension extends \Twig_Extension {

    protected $loader;

    public function __construct(\Twig_LoaderInterface $loader) {
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('form_top', null, array('node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode', 'is_safe' => array('html'))),
            new \Twig_SimpleFunction('print_error', array($this, 'printError'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('contentHeader', array($this, 'contentHeader'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('contentHeaderAllPeriods', array($this, 'contentHeaderAllPeriods'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('generateLink', array($this, 'generateLink'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('generateLinkUrlOnly', array($this, 'generateLinkUrlOnly'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('call_static_method', array($this, 'call_static_method')),
            new \Twig_SimpleFunction('periodService', array($this, 'getPeriodService'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('get_next_item', array($this, 'getNextItem')),
            new \Twig_SimpleFunction('validPnr', array($this, 'validPnr')),
            new \Twig_SimpleFunction('buttonsSubmit', array($this, 'buttonsSubmit'), array('is_safe' => array('html'))),
        );
    }
    
    /**
     *     [getFilters description]
     *     This is a cool function
     *     @author Máximo Sojo <maxsojo13@gmail.com>
     *     @version     [version]
     *     @date        2017-01-10
     *     @anotherdate 2017-01-10T13:35:06-0430
     *     @return      [type]                   [description]
     */
    public function getFilters() {
        return array(
            new \Twig_SimpleFilter('str_pad', function($input, $padlength, $padstring = '', $padtype = STR_PAD_LEFT) {
                        return str_pad($input, $padlength, $padstring, $padtype);
                    }),
            new \Twig_SimpleFilter('myNumberFormat', array($this, 'myNumberFormat')),
            new \Twig_SimpleFilter('ucwords', array($this, 'ucwords')),
            new \Twig_SimpleFilter('is_string', array($this, 'is_string')),
            new \Twig_SimpleFilter('myFormatDateTime', array($this, 'myFormatDateTime')),
            new \Twig_SimpleFilter('myFormatDate', array($this, 'myFormatDate')),
            new \Twig_SimpleFilter('render_yes_no', array($this, 'renderYesNo'), array('is_safe' => array('html'))),
        );
    }
    
    /**
     *     [contentHeader description]
     *     This is a cool function
     *     @author Máximo Sojo <maxsojo13@gmail.com>
     *     @version     [version]
     *     @date        2017-01-10
     *     @anotherdate 2017-01-10T13:34:59-0430
     *     @param       array                    $args [description]
     *     @return      [type]                         [description]
     */
    function contentHeader($args = []) {
        $route = realpath(__DIR__ . '/../../Resources/views/');
        $args = func_get_args();
        return $this->container->get('templating')->render($route.'/breadcrumb/breadcrumb.html.twig', ['breadcrumb' => $args]);
    }
    
    /**
     *     [buttonsSubmit description]
     *     This is a cool function
     *     @author Máximo Sojo <maxsojo13@gmail.com>
     *     @version     [version]
     *     @date        2017-01-10
     *     @anotherdate 2017-01-10T13:35:20-0430
     *     @param       array                    $args [description]
     *     @return      [type]                         [description]
     */
    function buttonsSubmit($args = []){
        $args = func_get_args();
        return $this->container->get('templating')->render('template/layout/commom/buttons/submit.html.twig', ['buttons' => $args]);
    }

    /**
     * Filtro para formatear numero.
     * @param type $value
     * @param type $decimals
     * @return type
     */
    function myNumberFormat($value, $decimals = 2) {
        return number_format($value, $decimals, ',', '.');
    }

    /**
     * Filtro para Mayusculas en Cada palabra de un String.
     * @param type $value
     * @param type $string
     * @return type
     */
    function ucwords($value) {
        return ucwords(mb_strtolower($value, 'UTF-8'));
    }

    /**
     * Filtro para Determinar si la Entrada es String o No
     * @param type $value
     * @return type
     */
    function is_string($value) {
        return is_string($value);
    }

    /**
     * Genera un link completo para mostrar el objeto
     * 
     * @param type $entity
     * @param type $type
     * @return type
     */
    function generateLink($entity, $type = \Pequiven\SEIPBundle\Service\LinkGenerator::TYPE_LINK_DEFAULT, array $parameters = array()) {
        return $this->container->get('seip.service.link_generator')->generate($entity, $type, $parameters);
    }

    /**
     * Genera solo la url de el objeto
     * 
     * @param type $entity
     * @param type $type
     * @return type
     */
    function generateLinkUrlOnly($entity, $type = \Pequiven\SEIPBundle\Service\LinkGenerator::TYPE_LINK_DEFAULT, array $parameters = array()) {
        return $this->container->get('seip.service.link_generator')->generateOnlyUrl($entity, $type, $parameters);
    }

    /**
     * Renderiza un si y no con color de tag
     * @param type $status
     * @return type
     */
    public function renderYesNo($status) {
        $template = '<span class="tag %s">%s</span>';
        if ($status === true) {
            $response = sprintf($template, "", $this->trans("pequiven.yes"));
        } else {
            $response = sprintf($template, "red-bg", $this->trans("pequiven.no"));
        }
        return $response;
    }

    public function myFormatDateTime($myFormatDate) {
        $dateFormated = "";
        if ($myFormatDate instanceof \DateTime) {
            $dateFormated = $myFormatDate->format($this->getConfiguration()->getGeneralDateFormat());
        }
        return $dateFormated;
    }

    public function myFormatDate($myFormatDate) {
        $dateFormated = "";
        if ($myFormatDate instanceof \DateTime) {
            $dateFormated = $myFormatDate->format("Y-m-d");
        }
        return $dateFormated;
    }

    public function call_static_method($object, $method, array $args) {
        return call_user_func_array(array($object, $method), $args);
    }

    public function getNextItem($loop, $items) {
        $nextItem = null;
        if ($loop['length'] > 1) {
            if ($loop['last'] == false) {
                $nextItem = $items[$loop['index0'] + 1];
            }
        }
        return $nextItem;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        return 'humper_core_extension';
    }

    function generateAsset($path, $packageName = null) {
        return $this->container->get('templating.helper.assets')
                        ->getUrl($path, $packageName);
    }

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    protected function trans($id, array $parameters = array(), $domain = 'messages') {
        return $this->container->get('translator')->trans($id, $parameters, $domain);
    }

    /**
     * Get a user from the Security Context
     *
     * @return mixed
     *
     * @throws LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser() {
        if (!$this->container->has('security.context')) {
            throw new LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }

    private function isGranted($roles) {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        return $this->container->get('security.context')->isGranted($roles);
    }
}
