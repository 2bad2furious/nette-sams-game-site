<?php


use Nette\Application\Routers\CliRouter;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class Router {
    private $consoleMode;

    public function __construct(bool $consoleMode) {
        $this->consoleMode = $consoleMode;
    }

    /**
     * @return \Nette\Application\IRouter
     */
    public function createRouter() {
        $routes = new RouteList();
        if ($this->consoleMode) {

            $routes[] = new CliRouter([
                "action" => "Default:default",
            ]);
        } else {
            $apimethod = BasePresenter::API_METHOD;
            $htmlmethod = BasePresenter::HTML_METHOD;

            $api = "<method {$apimethod}>/";
            $both = "[<method={$htmlmethod} {$htmlmethod}|{$apimethod}>/]";
            $html = "[<method={$htmlmethod} {$htmlmethod}>/]";
            //$routes[] = new Route("LogIn", "User:logIn");
            //$routes[] = new Route("SignUp", "User:signUp");
            //$routes[] = new Route("SignOut", "User:signOut");
            $type = CheckAvailabilityPresenter::AVAILABILITY_TYPE_KEYWORD;
            $types = implode("|", CheckAvailabilityPresenter::AVAILABILITY_TYPES);
            $routes[] = new Route($both . "<presenter auth|user>/<action sign-up|sign-out|log-in>");
            $routes[] = new Route($api . "<presenter check-availability>/<{$type} {$types}>");
            $routes[] = new Route($html . "<presenter>/<action>", "HomePage:default");
        }
        return $routes;
    }
}