<?php


use Nette\Database\Row;

class UserManager implements \Nette\Security\IAuthenticator, \Nette\Security\IAuthorizator {
    private $database;

    //t1 column +table names
    const USER_TABLE = "user",
        USER_ID = "user_id",
        NICKNAME = "username",
        EMAIL = "email",
        PW = "password";

    //t2 column + table names
    const ROLE_TABLE = "user_role",
        ROLE_ID = "role_id",
        USER_ROLE_ID = "user_role_id";


    const ROLE_USER = 1,
        ROLE_VERIFIED_USER = 2;

    const ROLES = [self::ROLE_USER, self::ROLE_VERIFIED_USER];

    const DEFAULT_ROLES = [self::ROLE_USER];

    /**
     * UserAuthenticator constructor.
     * @param \Nette\Database\Connection $database
     */
    public function __construct(Nette\Database\Context $database) {
        $this->database = $database;
    }

    /**
     * Performs an authentication against e.g. database.
     * and returns IIdentity on success or throws AuthenticationException
     * @return \Nette\Security\IIdentity
     * @throws \Nette\Security\AuthenticationException
     */
    public function authenticate(array $credentials) {
        $identification = $credentials[self::USERNAME];
        $password = $credentials[self::PASSWORD];

        $user = $this->database->table(self::USER_TABLE)->where([
            self::NICKNAME => $identification,
        ])->fetch();

        if (!$user || !Nette\Security\Passwords::verify($password, $user->{self::PW})) throw new \Nette\Security\AuthenticationException();

        return new UserIdentity($user->{self::USER_ID}, $user->{self::NICKNAME}, $user->{self::EMAIL}, $this->getRoles($user->{self::USER_ID}));
    }

    public function register(string $username, string $email, string $password): bool {
        //do the next thing only if it is ok
        $last_id = 0;
        $result = $this->database->table(self::USER_TABLE)->insert([
                self::NICKNAME => $username,
                self::PW       => Nette\Security\Passwords::hash($password),
                self::EMAIL    => $email,
            ])
            && $last_id = $this->database->getInsertId()
                && $this->insertDefaultRoles($last_id);
        if (!$result) $this->database->rollBack();
        return $result;
    }

    public function usernameExists(string $username): bool {
        return boolval($this->database->table(self::USER_TABLE)->where([
            self::NICKNAME => $username,
        ])->fetch());
    }

    public function emailExists(string $email): bool {
        return boolval($this->database->table(self::USER_TABLE)->where([
            self::EMAIL => $email,
        ])->fetch());
    }

    /**
     * @param string|Nette\Forms\Controls\TextInput $password
     * @param string $useless
     * @return bool
     * @throws Exception
     */
    public function isPasswordOk($password, $useless = "") {
        if ($password instanceof Nette\Forms\Controls\TextInput)
            $password = $password->getValue();
        else if (!is_string($password)) throw new Exception("Invalid parameter");

        return preg_match("/[0-9](.)*[0-9]/", $password) && preg_match("/[a-z](.)*[a-z]/", $password) && preg_match("/[A-Z](.)*[A-Z]/", $password);
    }

    public function isEmailOk(string $email): bool {
        try {
            $domain = explode("@", $email)[1];
            return filter_var($email, FILTER_VALIDATE_EMAIL) && checkdnsrr($domain, 'MX');
        } catch (Exception $ex) {
            return false;
        }
    }

    public function isUsernameOk(string $username): bool {
        return !preg_match("/[^0-9a-zA-Z-_+]/", $username);
    }

    /**
     * Performs a role-based authorization.
     * @param  string  \Nette\Security\role
     * @param  string  resource
     * @param  string  \Nette\Security\privilege
     * @return bool
     */
    function isAllowed($role, $resource, $privilege) {
        diedump($role, $resource, $privilege);
    }

    public function getOneByName(string $username):?UserIdentity {
        return $this->getOneBy(self::NICKNAME, $username);
    }

    public function changeUsername(int $id, string $username): bool {
        $user = $this->getOneByName($username);
        if ($user instanceof UserIdentity && $user->getId() !== $id) throw new Exception("Username already taken");
        if (!$this->isUsernameOk($username)) throw new Exception("Username not ok");
        if (!$this->getOneById($id)) throw new Exception("User does not exist");

        return boolval($this->database->table(self::USER_TABLE)->where([
            self::USER_ID => $id,
        ])->update([
            self::NICKNAME => $username,
        ]));
    }

    private function getOneById(int $id):?UserIdentity {
        return $this->getOneBy(self::USER_TABLE . "." . self::USER_ID, $id);
    }

    private function insertDefaultRoles(int $id): bool {
        foreach (self::DEFAULT_ROLES as $role) {
            $r = $this->database->table(self::ROLE_ID)
                ->insert([
                    self::USER_ID => $id,
                    self::ROLE_ID => $role,
                ]);
            if (!$r) return false;
        }
        return true;
    }

    public function getOneBy(string $column, string $value):?UserIdentity {
        $data = $this->database->table(self::USER_TABLE)->where([
            $column => $value,
        ])->fetch();
        if ($data) {
            $roles = $this->getRoles($data->{self::USER_ID});
            return new UserIdentity($data->{self::USER_ID}, $data{self::NICKNAME}, $data{self::EMAIL}, $roles);
        }
        return null;
    }

    private function getRoles(int $user_id): array {
        $roles = [];
        $data = $this->database->table(self::ROLE_TABLE)->where([
            self::USER_ID => $user_id,
        ])->fetchAll();

        foreach ($data as $role) {
            $role_num = $role->{self::ROLE_ID};
            if (in_array($role_num, self::ROLES)) {
                $roles[] = $role_num;
            } else {
                trigger_error("ROLE " . $role_num . " NOT FOUND IN ARRAY");
            }
        }
        return $roles;
    }

    public function getOneByEmail(string $email):?UserIdentity {
        return $this->getOneBy(self::EMAIL, $email);
    }
}