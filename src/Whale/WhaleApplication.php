<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Whale;

use Doctrine\Common\Cache\FilesystemCache;
use Monolog\Logger;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class WhaleApplication extends Application {

    use Application\UrlGeneratorTrait;
    use Application\MonologTrait;

    public function __construct($values = array())
    {
        parent::__construct($values);

        $this['dgr.config'] = $values;
        $this->register(new MonologServiceProvider(), array(
            'monolog.logfile' => $this['tmp_path'] . '/dgr.log',
            'monolog.level' => $this['debug'] ? Logger::DEBUG : Logger::INFO,
        ));

        $this['cache'] = $this->share(function () {
            return new FilesystemCache($this['tmp_path'] . '/cache');
        });

        $this['logtime'] = $this->protect(
            function ($msg = null, $params = array()) {
                $this->log(
                    (int)((microtime(true) - $this['starttime']) * 1000) . 'ms ' . ($msg ? "[$msg]" : ''),
                    $params
                );
            }
        );
        $this->register(new SessionServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new FormServiceProvider());
        $this->register(new TwigServiceProvider(), array(
            'twig.path' => $this['application_path'] . '/views',
            'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
            'twig.options' => array(
                'cache' => $this['tmp_path'] . '/cache/twig',
                'debug' => $this['debug'],
            ),
        ));
        $path = realpath(__DIR__ . '/Resources/views');
        $this['logtime']('path: ' . $path);
        $this['twig.loader']->addLoader(new \Twig_Loader_Filesystem($path));

        $this->register(new ValidatorServiceProvider());
        $this->register(new TranslationServiceProvider(), array( 'translator.messages' => array()));
        $this->register(new DoctrineServiceProvider(), $this['config']['db']);


        $this->before(
            function () {
                $this['logtime']('before controller');
            }
        );

        $this->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'admin' => array(
                    'pattern' => '^/admin',
                    'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
                    'users' => array(
                        'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                    ),
                    'logout' => array('logout_path' => '/admin/logout'),
                ),
            )
        ));
        $this->get('/login', function(Request $request) {
            return $this['twig']->render('admin/login.twig', array(
                'error'         => $this['security.last_error']($request),
                'last_username' => $this['session']->get('_security.last_username'),
            ));
        });
        $this->get('/admin/logout', function(Request $request) {
            return $this['twig']->render('admin/login.twig', array(
                'error'         => $this['security.last_error']($request),
                'last_username' => $this['session']->get('_security.last_username'),
            ));
        });
        $this['flashbag'] = $this->share(function(){
            return $this['session']->getFlashBag();
        });
        $this->after(
            function () {
                $this['logtime']('after controller');
            }
        );
    }

    /**
     * @param \Closure|object $callable
     * @return callable
     */
    public static function share($callable)
    {
        return parent::share($callable);
    }

    /**
     * @param \Closure|object $callable
     * @return callable
     */
    public static function protect($callable)
    {
        return parent::protect($callable);
    }
} 