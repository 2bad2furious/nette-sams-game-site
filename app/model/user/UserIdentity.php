<?php


class UserIdentity implements \Nette\Security\IIdentity {

    private $id;
    private $username;
    private $email;
    private $roles = [];

    /**
     * UserIdentity constructor.
     * @param int $id
     * @param string $username
     * @param string $email
     * @param array $roles
     */
    public function __construct(int $id = -1, string $username = "no-name", string $email = "no-email", array $roles = []) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->roles = $roles;
    }


    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return array
     **/
    function getRoles() {
        return $this->roles;
    }
}