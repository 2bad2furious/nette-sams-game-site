<?php


use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class Router {

    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter() {
        $routes = new RouteList();

        $apimethod = BasePresenter::API_METHOD;
        $htmlmethod = BasePresenter::HTML_METHOD;

        $api = "<method {$apimethod}>/";
        $both = "[<method={$htmlmethod} {$htmlmethod}|{$apimethod}>/]";
        $html = "[<method={$htmlmethod} {$htmlmethod}>/]";
        //$routes[] = new Route("LogIn", "User:logIn");
        //$routes[] = new Route("SignUp", "User:signUp");
        //$routes[] = new Route("SignOut", "User:signOut");
        $type = UserPresenter::AVAILABILITY_TYPE_KEYWORD;
        $types = implode("|", UserPresenter::AVAILABILITY_TYPES);
        $routes[] = new Route($both . "<presenter user>/<action sign-up|sign-out|log-in>");
        $routes[] = new Route($api . "<presenter user>/<action check-availability>/<{$type} {$types}>");
        $routes[] = new Route($html . "<presenter>/<action>", "HomePage:default");
        return $routes;
    }
}