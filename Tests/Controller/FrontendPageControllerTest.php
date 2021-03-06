<?php
/**
 * This file is part of the Networking package.
 *
 * (c) net working AG <info@networking.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Networking\InitCmsBundle\Tests\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use \Networking\InitCmsBundle\Controller\FrontendPageController,
    \Networking\InitCmsBundle\Helper\LanguageSwitcherHelper,
    \Symfony\Component\HttpFoundation\Request,
    \Symfony\Component\HttpFoundation\Response,
    \Networking\InitCmsBundle\Model\Page,
    \Symfony\Component\Security\Core\Exception\AccessDeniedException,
    \Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    \Symfony\Component\Security\Core\SecurityContext,
    Symfony\Bundle\FrameworkBundle\Routing\Router;
use Networking\InitCmsBundle\Model\PageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/** @author sonja brodersen s.brodersen@networking.ch */
class FrontendPageControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testIndexActionWithAccessDeniedException()
    {
        $this->setExpectedException('\Symfony\Component\Security\Core\Exception\AccessDeniedException');
        // because no user is authenticated: a AuthenticationCredentialsNotFoundException
        $this->setExpectedException(
            'Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException'
        );

        //Mocks
        $mockPage = $this->getMockBuilder('\Networking\InitCmsBundle\Model\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $mockPage->expects($this->once())
            ->method('getVisibility')
            ->will($this->returnValue(PageInterface::VISIBILITY_PROTECTED));

        $mockRequest = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->any())
            ->method('get')
            ->with('_content')
            ->will($this->returnValue($mockPage));

        $mockSecurityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $mockSecurityContext->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null));

        $mockSecurityContext->expects($this->any())
            ->method('isGranted')
            ->with('ROLE_USER')
            ->will($this->returnValue(false));

        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();

        //cache class
        $mockCacheClass = $this->getMockBuilder('Networking\InitCmsBundle\Lib\PhpCache')
            ->disableOriginalConstructor()
            ->getMock();

        $mockContainer->expects($this->at(0))
            ->method('get')
            ->with('networking_init_cms.lib.php_cache')
            ->will($this->returnValue($mockCacheClass));

        $mockContainer->expects($this->at(1))
            ->method('has')
            ->with('security.context')
            ->will($this->returnValue(true));

        $mockContainer->expects($this->at(2))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));

        $mockContainer->expects($this->at(3))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));

        // Controller
        $controller = new FrontendPageController();
        $controller->setContainer($mockContainer);
        $this->assertInstanceOf(
            'Networking\InitCmsBundle\Controller\FrontendPageController',
            $controller,
            'Controller ist a DefaultController'
        );

        $controller->indexAction($mockRequest);
    }

    /**
     */
    public function testIndexActionWithNotFoundHttpException()
    {
        $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        //Mocks
        // page
        $mockPage = $this->getMockBuilder('\Networking\InitCmsBundle\Model\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $mockPage->expects($this->once())
            ->method('getVisibility')
            ->will($this->returnValue(Page::VISIBILITY_PUBLIC));
        $mockPage->expects($this->exactly(2))
            ->method('getStatus')
            ->will($this->returnValue(Page::STATUS_DRAFT));

        // request
        $mockRequest = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->any())
            ->method('get')
            ->with('_content')
            ->will($this->returnValue($mockPage));
        //security context
        $mockSecurityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();


        $mockSecurityContext->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue(null));

        $mockSecurityContext->expects($this->any())
            ->method('isGranted')
            ->with('ROLE_SONATA_ADMIN')
            ->will($this->returnValue(false));
        //Container
        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();
        //cache class
        $mockCacheClass = $this->getMockBuilder('Networking\InitCmsBundle\Lib\PhpCache')
            ->disableOriginalConstructor()
            ->getMock();

        $mockContainer->expects($this->at(0))
            ->method('get')
            ->with('networking_init_cms.lib.php_cache')
            ->will($this->returnValue($mockCacheClass));

        $mockContainer->expects($this->at(1))
            ->method('has')
            ->with('security.context')
            ->will($this->returnValue(true));

        $mockContainer->expects($this->at(2))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));

        $mockContainer->expects($this->at(3))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));

        // Controller
        $controller = new FrontendPageController();
        $controller->setContainer($mockContainer);
        $this->assertInstanceOf(
            'Networking\InitCmsBundle\Controller\FrontendPageController',
            $controller,
            'Controller ist a DefaultController'
        );

        $controller->indexAction($mockRequest);
    }


    public function testLiveAction()
    {

        // MOCKS
        $mockPage = $this->getMock('Networking\InitCmsBundle\Model\Page');
        $mockPage->expects($this->once())
            ->method('getVisibility')
            ->will($this->returnValue(PageInterface::VISIBILITY_PUBLIC));
        $mockPage->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(true));

        $mockSnapshot = $this->getMockBuilder('Networking\InitCmsBundle\Entity\PageSnapshot')
            ->disableOriginalConstructor()
            ->getMock();

        $mockHelper = $this->getMockBuilder('Networking\InitCmsBundle\Helper\PageHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $mockHelper->expects($this->once())
            ->method('unserializePageSnapshotData')
            ->will($this->returnValue($mockPage));

        //cache class
        $mockCacheClass = $this->getMockBuilder('Networking\InitCmsBundle\Lib\PhpCache')
            ->disableOriginalConstructor()
            ->getMock();

        //security context
        $mockSecurityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $mockSecurityContext->expects($this->any())
            ->method('isGranted')
            ->with('ROLE_USER')
            ->will($this->returnValue(true));

        //templating
        $mockTemplating = $this->getMockBuilder('\Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Response')
            ->getMock();

        $mockTemplating->expects($this->once())
            ->method('render')
            ->will($this->returnValue($mockResponse));

        //request
        $mockRequest = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest->expects($this->at(0))
            ->method('get')
            ->with('_content')
            ->will($this->returnValue($mockSnapshot));

        $mockRequest->expects($this->at(1))
            ->method('get')
            ->with('_content')
            ->will($this->returnValue($mockSnapshot));

        $mockRequest->expects($this->at(2))
            ->method('get')
            ->with('_template');

        //Container
        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();


        $mockContainer->expects($this->at(0))
            ->method('get')
            ->with('networking_init_cms.lib.php_cache')
            ->will($this->returnValue($mockCacheClass));

        $mockContainer->expects($this->at(1))
            ->method('has')
            ->with('security.context')
            ->will($this->returnValue(true));

        $mockContainer->expects($this->at(2))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));

        $mockContainer->expects($this->at(3))
            ->method('get')
            ->with('networking_init_cms.helper.page_helper')
            ->will($this->returnValue($mockHelper));

        $mockContainer->expects($this->at(4))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));


        $mockContainer->expects($this->at(5))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($mockTemplating));


        // controller
        $controller = new FrontendPageController();
        $controller->setContainer($mockContainer);
        $response = $controller->liveAction($mockRequest);

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response, 'response object returned');

    }

    /**
     *
     */
    public function testRequest()
    {
        $request = new Request();
        $request->setLocale('de_CH');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Request', $request, 'request class');
        $this->assertEquals('de_CH', $request->getLocale(), 'request locale');
    }

    /**
     * home
     */
    public function testHomeAction()
    {
        $mockPage = $this->getMock('\Networking\InitCmsBundle\Model\Page');
        $mockSnapshot = $this->getMockForAbstractClass(
            '\Networking\InitCmsBundle\Model\PageSnapshot',
            array($mockPage)
        );

        $mockPage->expects($this->once())
            ->method('getVisibility')
            ->will($this->returnValue(PageInterface::VISIBILITY_PUBLIC));

        $mockPage->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(true));


        $pageHelper = new \Networking\InitCmsBundle\Helper\PageHelper();

        $mockSerializer = $this->getMockBuilder('\JMS\Serializer\SerializerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockSerializer->expects($this->once())
            ->method('deserialize')
            ->willReturn($mockPage);

        //security context
        $mockSecurityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();
        $mockSecurityContext->expects($this->any())
            ->method('isGranted')
            ->with('ROLE_USER')
            ->will($this->returnValue(true));

        $_SERVER = array(
            'PATH_INFO' => '/',
            'SCRIPT_NAME' => 'app.php'
        );
        $templateParams = array(
            'template' => 'DemoInitCmsBundle:Default:one_column.html.twig',
            'engine' => 'twig',
            'vars' => array(),
            'isStreamable' => false
        );
        $template = new Template($templateParams);
        $requestParams = array(
            '_content' => $mockSnapshot,
            '_template' => $template
        );
        //templating
        $mockTemplating = $this->getMockBuilder('\Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $mockResponse = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Response')
            ->getMock();

        $mockTemplating->expects($this->once())
            ->method('render')
            ->will($this->returnValue($mockResponse));
        //request
        $request = new Request(array(), array(), array(), array(), array(), $_SERVER);
        $request->attributes->set('_route', 'networking_init_cms_default');
        $request->attributes->set('_locale', 'en');

        //dynamic router
        $mockDynamicRouter = $this->getMockBuilder('\Symfony\Cmf\Component\Routing\DynamicRouter')
            ->disableOriginalConstructor()
            ->getMock();
        $mockDynamicRouter->expects($this->once())
            ->method('matchRequest')
            ->with($request)
            ->willReturn($requestParams);

        //container
        $mockContainer = $this->getMockBuilder('Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();

        //cache class
        $mockCacheClass = $this->getMockBuilder('Networking\InitCmsBundle\Lib\PhpCache')
            ->disableOriginalConstructor()
            ->getMock();


        $mockContainer->expects($this->at(0))
            ->method('get')
            ->with('networking_init_cms.helper.page_helper')
            ->will($this->returnValue($pageHelper));


        $mockContainer->expects($this->at(1))
            ->method('get')
            ->with('networking_init_cms.cms_router')
            ->will($this->returnValue($mockDynamicRouter));


        $mockContainer->expects($this->at(2))
            ->method('get')
            ->with('networking_init_cms.lib.php_cache')
            ->will($this->returnValue($mockCacheClass));

        $mockContainer->expects($this->at(3))
            ->method('has')
            ->with('security.context')
            ->will($this->returnValue(true));

        $mockContainer->expects($this->at(4))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));


        $mockContainer->expects($this->at(5))
            ->method('get')
            ->with('networking_init_cms.helper.page_helper')
            ->will($this->returnValue($pageHelper));

        $mockContainer->expects($this->at(6))
            ->method('get')
            ->with('serializer')
            ->willReturn($mockSerializer);

        $mockContainer->expects($this->at(7))
            ->method('get')
            ->with('security.context')
            ->will($this->returnValue($mockSecurityContext));

        $mockContainer->expects($this->at(8))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($mockTemplating));

        $requestAfter = clone $request;

        $requestAfter->attributes->add($requestParams);
        unset($requestParams['_route']);
        unset($requestParams['_controller']);
        $requestAfter->attributes->set('_route_params', $requestParams);

        $configuration = $requestAfter->attributes->get('_template');
        $requestAfter->attributes->set('_template', $configuration->getTemplate());
        $requestAfter->attributes->set('_template_vars', $configuration->getVars());
        $requestAfter->attributes->set('_template_streamable', $configuration->isStreamable());

        $pageHelper->setContainer($mockContainer);

        $controller = new FrontendPageController();
        $controller->setContainer($mockContainer);
        $response = $controller->homeAction($request);

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response, 'response object returned');
    }


    public function testChangeAdminLanguageAction()
    {
        // session
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $session->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo('admin/_locale'),
                $this->equalTo('xy')
            );
        // request
        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->once())
            ->method('getSession')
            ->will($this->returnValue($session));
        // request headers
        $headers = $this->getMock('\Symfony\Component\HttpFoundation\HeaderBag');
        $headers->expects($this->once())
            ->method('get')
            ->will($this->returnValue('/test/'))
            ->with($this->equalTo('referer'));
        $request->headers = $headers;
        // controller test
        $controller = new FrontendPageController();
        $response = $controller->changeAdminLanguageAction($request, 'xy');
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\RedirectResponse', $response, 'Redirect returned');
    }

}

