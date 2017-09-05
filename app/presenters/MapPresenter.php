<?php


class MapPresenter extends BasePresenter {

    protected function getMapManager(): MapManager {
        return $this->context->getByType(MapManager::class);
    }

    public function renderDefault() {
        $map_author = $this->getParameter("author");
        $map_url = $this->getParameter("url");
    }

    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        $action = $this->getAction();
        if ($action === "default") {
            return UserManager::ROLES;
        }
        return [UserManager::ROLE_VERIFIED_USER];
    }

    // public function
}