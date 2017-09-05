<?php


class TokenPresenter extends BasePresenter {
    const INVALID_TOKEN = "invalid_token_string";

    public function renderDefault() {
        $action = $this->getParameter("token-action", "");
        $tokenString = $this->getParameter("token", "");

        $token = $this->getTokenManager()->getByTokenStringAndAction($tokenString, $action);

        if ($token instanceof Token) {
            if ($action === TokenManager::ACTION_USER_VERIFY) {
                $message = "";
            } else if ($action === TokenManager::ACTION_RESET_PASSWORD) {

                $message = "";
            } else {
                $message = self::INVALID_TOKEN;
            }
        } else {
            $message = self::INVALID_TOKEN;
        }

        $this->template->message = $message;
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return UserManager::ROLES;
    }

    private function getTokenManager(): TokenManager {
        return $this->context->getByType(TokenManager::class);
    }
}