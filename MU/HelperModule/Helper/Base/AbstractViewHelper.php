<?php
/**
 * Helper.
 *
 * @copyright Michael Ueberschaer (MU)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Michael Ueberschaer <info@homepages-mit-zikula.de>.
 * @link https://homepages-mit-zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio (https://modulestudio.de).
 */

namespace MU\HelperModule\Helper\Base;

use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Zikula\Core\Response\PlainResponse;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\ThemeModule\Engine\ParameterBag;
use MU\HelperModule\Helper\ControllerHelper;

/**
 * Helper base class for view layer methods.
 */
abstract class AbstractViewHelper
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var FilesystemLoader
     */
    protected $twigLoader;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PermissionApiInterface
     */
    protected $permissionApi;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var ParameterBag
     */
    protected $pageVars;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * ViewHelper constructor.
     *
     * @param Twig_Environment       $twig             Twig service instance
     * @param FilesystemLoader       $twigLoader       Twig loader service instance
     * @param RequestStack           $requestStack     RequestStack service instance
     * @param PermissionApiInterface $permissionApi    PermissionApi service instance
     * @param VariableApiInterface   $variableApi      VariableApi service instance
     * @param ParameterBag           $pageVars         ParameterBag for theme page variables
     * @param ControllerHelper       $controllerHelper ControllerHelper service instance
     *
     * @return void
     */
    public function __construct(
        Twig_Environment $twig,
        FilesystemLoader $twigLoader,
        RequestStack $requestStack,
        PermissionApiInterface $permissionApi,
        VariableApiInterface $variableApi,
        ParameterBag $pageVars,
        ControllerHelper $controllerHelper
    ) {
        $this->twig = $twig;
        $this->twigLoader = $twigLoader;
        $this->request = $requestStack->getCurrentRequest();
        $this->permissionApi = $permissionApi;
        $this->variableApi = $variableApi;
        $this->pageVars = $pageVars;
        $this->controllerHelper = $controllerHelper;
    }

    /**
     * Determines the view template for a certain method with given parameters.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     *
     * @return string name of template file
     */
    public function getViewTemplate($type, $func)
    {
        // create the base template name
        $template = '@MUHelperModule/' . ucfirst($type) . '/' . $func;
    
        // check for template extension
        $templateExtension = '.' . $this->determineExtension($type, $func);
    
        // check whether a special template is used
        $tpl = $this->request->query->getAlnum('tpl', '');
        if (!empty($tpl)) {
            // check if custom template exists
            $customTemplate = $template . ucfirst($tpl);
            if ($this->twigLoader->exists($customTemplate . $templateExtension)) {
                $template = $customTemplate;
            }
        }
    
        $template .= $templateExtension;
    
        return $template;
    }

    /**
     * Helper method for managing view templates.
     *
     * @param string  $type               Current controller (name of currently treated entity)
     * @param string  $func               Current function (index, view, ...)
     * @param array   $templateParameters Template data
     * @param string  $template           Optional assignment of precalculated template file
     *
     * @return mixed Output
     */
    public function processTemplate($type, $func, array $templateParameters = [], $template = '')
    {
        $templateExtension = $this->determineExtension($type, $func);
        if (empty($template)) {
            $template = $this->getViewTemplate($type, $func);
        }
    
        // look whether we need output with or without the theme
        $raw = $this->request->query->getBoolean('raw', false);
        if (!$raw && $templateExtension != 'html.twig') {
            $raw = true;
        }
    
        $output = $this->twig->render($template, $templateParameters);
        $response = null;
        if (true === $raw) {
            // standalone output
            $response = new PlainResponse($output);
        } else {
            // normal output
            $response = new Response($output);
        }
    
        return $response;
    }

    /**
     * Get extension of the currently treated template.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     *
     * @return string Template extension
     */
    protected function determineExtension($type, $func)
    {
        $templateExtension = 'html.twig';
        if (!in_array($func, ['view', 'display'])) {
            return $templateExtension;
        }
    
        $extensions = $this->availableExtensions($type, $func);
        $format = $this->request->getRequestFormat();
        if ($format != 'html' && in_array($format, $extensions)) {
            $templateExtension = $format . '.twig';
        }
    
        return $templateExtension;
    }

    /**
     * Get list of available template extensions.
     *
     * @param string $type Current controller (name of currently treated entity)
     * @param string $func Current function (index, view, ...)
     *
     * @return array List of allowed template extensions
     */
    public function availableExtensions($type, $func)
    {
        $extensions = [];
        $hasAdminAccess = $this->permissionApi->hasPermission('MUHelperModule:' . ucfirst($type) . ':', '::', ACCESS_ADMIN);
        if ($func == 'view') {
            if ($hasAdminAccess) {
                $extensions = [];
            } else {
                $extensions = [];
            }
        } elseif ($func == 'display') {
            if ($hasAdminAccess) {
                $extensions = [];
            } else {
                $extensions = [];
            }
        }
    
        return $extensions;
    }
}
