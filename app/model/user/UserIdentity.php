<?php


class UserIdentity implements \Nette\Security\IIdentity {

    private $id;
    private $username;
    private $email;
    private $role;
    private $admin;
    private $description;

    /**
     * UserIdentity constructor.
     * @param int $id
     * @param string $username
     * @param string $email
     * @param string $description
     * @param int $role
     * @param bool $admin
     */
    public function __construct(int $id, string $username, string $email, string $description, int $role, bool $admin) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->admin = $admin;
        $this->description = $description;
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

    function getRole(): int {
        return $this->role;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool {
        return $this->admin;
    }

    public function isVerified(): bool {
        return $this->getRole() === UserManager::ROLE_VERIFIED_USER;
    }

    function getRoles(): array {
        return [$this->getRole()];
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }
}