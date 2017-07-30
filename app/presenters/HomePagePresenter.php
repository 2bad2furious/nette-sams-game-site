<?php


class HomePagePresenter extends BasePresenter {
    public function renderDefault() {
        $this->template->loggedIn = true;
        $user = new StdClass();
        $user->username = "user";
        $this->template->user = $user;
    }
}