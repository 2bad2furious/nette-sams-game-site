<?php


use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter {

    const API_METHOD = "api",
        HTML_METHOD = "html",
        METHODS = [self::API_METHOD, self::HTML_METHOD];

    const SOMETHING_WENT_WRONG = "something_went_wrong",
        NO_PARAMETER_RECEIVED = "no_parameter_received";

    const MESSAGE_TYPE_INFO = "info",
        MESSAGE_TYPE_ERROR = "error",
        MESSAGE_TYPE_WARNING = "warning",
        MESSAGE_TYPES = [self::MESSAGE_TYPE_INFO, self::MESSAGE_TYPE_ERROR, self::MESSAGE_TYPE_WARNING];

    private $identity;

    public function beforeRender() {
        $this->template->setTranslator($this->getTranslator());
    }

    public function getIdentity(): UserIdentity {
        $identity = $this->identity;
        if (!$identity instanceof UserIdentity) throw new Exception("Identity needed for current action.");
        return $identity;
    }

    protected function setIdentity(UserIdentity $identity) {
        $this->identity = $identity;
    }

    public final function checkRequirements($element) {
        $roles = $this->getRoles();
        if (!$roles) {
            trigger_error("NO ROLES RECEIVED, using all roles");
            $roles = UserManager::ROLES;
        }

        foreach ($roles as $role) {
            $this->checkRole($role);
            if ($this->hasUserRole($role)) return;
        }

        $translator = $this->getTranslator();

        if (!array_diff($roles, UserManager::ONLY_GUESTS)) {
            $title = UserManagement::ACTION_FOR_GUEST_ONLY;
            $additional = sprintf($translator->translate(UserManagement::ACTION_FOR_GUEST_ONLY_ADDITIONAL), $this->link("Auth:logIn"));

            //if user is logged and need verification
        } else if (!array_diff($roles, UserManager::ONLY_VERIFIED) && $this->getUser()->isLoggedIn()) {
            $title = UserManagement::ACTION_FOR_VERIFIED_ONLY;
            $additional = sprintf($translator->translate(UserManagement::ACTION_FOR_VERIFIED_ONLY_ADDITIONAL), $this->link("Profile:resend"));

            //if user is not logged
        } else if (!array_diff($roles, UserManager::ONLY_VERIFIED) || !array_diff($roles, UserManager::USERS)) {
            $title = UserManagement::ACTION_FOR_USERS_ONLY;
            $additional = sprintf($translator->translate(UserManagement::ACTION_FOR_USERS_ONLY_ADDITIONAL), $this->link("Auth:logIn"), $this->link("Auth:signUp"));
        } else throw new Exception("Unknown role configuration");

        $this->template->setTranslator($translator);
        $this->template->setFile(__DIR__ . "/templates/rights/rights.latte");
        $this->template->additional = $additional;
        $this->template->user = $this->getUser()->getIdentity();
        $this->template->title = $translator->translate($title);
        $this->template->render();
        $this->terminate();
    }

    private function hasUserRole($role): bool {
        $this->checkRole($role);

        $user = $this->getUser()->getIdentity();
        if ((!$user instanceof UserIdentity || $user->getRole() === UserManager::ROLE_GUEST)
            && $role === UserManager::ROLE_GUEST
        ) return true;
        return ($user instanceof UserIdentity && $user->getRole() === $role);
    }

    private function checkRole(string $role) {
        if (!$this->getUserManager()->roleExists($role)) throw new Exception("invalid role " . $role);
    }

    public function createComponentHeader($name) {
        return new HeaderControl($this, $name);
    }

    public function getUserManager(): UserManager {
        return $this->getUser()->getAuthenticator(true);
    }

    public function getTranslator(): Translator {
        return $this->context->getByType(Translator::class);
    }

    public function getMailer(): MailerWrapper {
        return $this->context->getByType(MailerWrapper::class);
    }

    public function getFormFactory(): FormFactory {
        return $this->context->getByType(FormFactory::class);
    }

    protected function isApiMethod(): bool {
        return $this->getParameter("method") === self::API_METHOD;
    }

    protected function isHtmlMethod(): bool {
        return $this->getParameter("method") === self::HTML_METHOD;
    }

    /**
     * forces you to use constants, just to prettify code
     * @param string $message
     * @param string $type
     * @return stdClass
     * @throws Exception
     */
    public function flashMessage($message, $type = self::MESSAGE_TYPE_INFO): StdClass {
        if (!in_array($type, self::MESSAGE_TYPES)) throw new Exception("You should use the constants provided");
        return parent::flashMessage($message, $type);
    }

    protected function error404(string $message = "") {
        $this->error($message);
        $this->terminate();
    }

    /**
     * for access control
     * @return array
     */
    abstract protected function getRoles(): array;
}