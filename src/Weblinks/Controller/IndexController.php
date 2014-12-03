<?php

namespace Weblinks\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class IndexController {

    public function indexAction(Application $app) {
        $links = $app['dao.link']->findAll();
        return $app['twig']->render('index.html.twig', array('links' => $links));
    }

    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
                    'error' => $app['security.last_error']($request),
                    'last_username' => $app['session']->get('_security.last_username'),
        ));
    }

}
