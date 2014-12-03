<?php

use Symfony\Component\HttpFoundation\Request;
use Weblinks\Domain\User;
use Weblinks\Form\Type\UserType;
use Weblinks\Form\Type\LinkType;
use Weblinks\Domain\Link;

// Home page
$app->get('/', "Weblinks\Controller\IndexController::indexAction");

$app->get('/login', "Weblinks\Controller\IndexController::loginAction")->bind('login');

$app->get('/admin', "Weblinks\Controller\AdminController::indexAction");
  
$app->match('/admin/user/add', "Weblinks\Controller\AdminController::addUserAction");

$app->match('/admin/link/add', "Weblinks\Controller\AdminController::addLinkAction");

$app->match('/admin/user/{id}/edit', "Weblinks\Controller\AdminController::editUserAction");

$app->match('/admin/link/{id}/edit', "Weblinks\Controller\AdminController::editLinkAction");

$app->match('/admin/user/{id}/delete', "Weblinks\Controller\AdminController::deleteUserAction");

$app->match('/admin/link/{id}/delete', "Weblinks\Controller\AdminController::deleteLinkAction");
