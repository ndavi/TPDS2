<?php

namespace Weblinks\Controller;

use Symfony\Component\HttpFoundation\Request;
use Weblinks\Domain\User;
use Weblinks\Form\Type\UserType;
use Weblinks\Form\Type\LinkType;
use Weblinks\Domain\Link;
use Silex\Application;

class AdminController {

    /**
     * Admin home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $links = $app['dao.link']->findAll();
        $users = $app['dao.user']->findAll();
        return $app['twig']->render('admin.html.twig', array(
                    'links' => $links,
                    'users' => $users));
    }

    public function addUserAction(Request $request, Application $app) {
        $user = new User();
        $userForm = $app['form.factory']->create(new UserType(), $user);
        $userForm->handleRequest($request);
        if ($userForm->isValid()) {
            $salt = substr(md5(time()), 0, 23);
            $user->setSalt($salt);
            $plainPassword = $user->getPassword();
// find the default encoder
            $encoder = $app['security.encoder.digest'];
// compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
        }
        return $app['twig']->render('user_form.html.twig', array(
                    'title' => 'New user',
                    'userForm' => $userForm->createView()));
    }

    public function addLinkAction(Request $request, Application $app) {
        $link = new Link();
        $linkForm = $app['form.factory']->create(new LinkType(), $link);
        $linkForm->handleRequest($request);
        if ($linkForm->isValid()) {
            $token = $app['security']->getToken();
            $link->setUser($token->getUser());
            $app['dao.link']->save($link);
            $app['session']->getFlashBag()->add('success', 'The article was succesfully updated.');
            return $app->redirect('/admin');
        }
        return $app['twig']->render('link_form.html.twig', array(
                    'title' => 'New Link',
                    'linkForm' => $linkForm->createView()));
    }

    public function editUserAction($id, Request $request, Application $app) {
        $user = $app['dao.user']->find($id);
        $userForm = $app['form.factory']->create(new UserType(), $user);
        $userForm->handleRequest($request);
        if ($userForm->isValid()) {
            $plainPassword = $user->getPassword();
            // find the encoder for the user
            $encoder = $app['security.encoder_factory']->getEncoder($user);
            // compute the encoded password
            $password = $encoder->encodePassword($plainPassword, $user->getSalt());
            $user->setPassword($password);
            $app['dao.user']->save($user);
            $app['session']->getFlashBag()->add('success', 'The user was succesfully updated.');
            return $app->redirect('/admin');
        }
        return $app['twig']->render('user_form.html.twig', array(
                    'title' => 'Edit user',
                    'userForm' => $userForm->createView()));
    }

    public function editLinkAction($id, Request $request, Application $app) {
        $link = $app['dao.link']->find($id);
        $linkForm = $app['form.factory']->create(new LinkType(), $link);
        $linkForm->handleRequest($request);
        if ($linkForm->isValid()) {
            $app['dao.link']->save($link);
            $app['session']->getFlashBag()->add('success', 'The article was succesfully updated.');
            return $app->redirect('/admin');
        }
        return $app['twig']->render('link_form.html.twig', array(
                    'title' => 'Edit Link',
                    'linkForm' => $linkForm->createView()));
    }

    public function deleteUserAction($id, Request $request, Application $app) {
        $app['dao.link']->deleteAllByUser($id);
        $app['dao.user']->delete($id);
        $app['session']->getFlashBag()->add('success', 'The user was succesfully removed.');
        return $app->redirect('/admin');
    }

    public function deleteLinkAction($id, Request $request, Application $app) {
        $app['dao.link']->delete($id);
        $app['session']->getFlashBag()->add('success', 'The link was succesfully removed.');
        return $app->redirect('/admin');
    }

}
