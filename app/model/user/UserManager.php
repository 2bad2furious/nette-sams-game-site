<?php


use Nette\Database\Row;

class UserManager implements \Nette\Security\IAuthenticator, \Nette\Security\IAuthorizator {
    private $database;
    /**
     * @var TokenManager
     */
    private $tokenManager;


    //t1 column +table names
    const USER_TABLE = "user";
    const USER_ID = "user_id",
        NICKNAME = "username",
        EMAIL = "email",
        PW = "password",
        DESCRIPTION = "description",
        ROLE = "role",
        ADMIN = "admin";

    const COLUMNS = [self::USER_ID, self::NICKNAME, self::EMAIL, self::PW, self::DESCRIPTION, self::ROLE, self::ADMIN];

    const ROLE_GUEST = 0,
        ROLE_USER = 1,
        ROLE_VERIFIED_USER = 2;

    const ROLES = [self::ROLE_GUEST, self::ROLE_USER, self::ROLE_VERIFIED_USER];

    const DEFAULT_ROLES = [self::ROLE_USER];

    const ONLY_GUESTS = [self::ROLE_GUEST];

    const USERS = [self::ROLE_USER, self::ROLE_VERIFIED_USER];

    const ONLY_VERIFIED = [self::ROLE_VERIFIED_USER];

    /**
     * UserAuthenticator constructor.
     * @param \Nette\Database\Context $database
     * @param TokenManager $tokenManager
     */
    public function __construct(Nette\Database\Context $database, TokenManager $tokenManager) {
        $this->database = $database;
        $this->tokenManager = $tokenManager;
    }

    /**
     * Performs an authentication against e.g. database.
     * and returns IIdentity on success or throws AuthenticationException
     * @param array $credentials
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

        return new UserIdentity($user->{self::USER_ID}, $user->{self::NICKNAME}, $user->{self::EMAIL}, $user->{self::DESCRIPTION}, $user->{self::ROLE}, $user->{self::ADMIN});
    }

    public function register(string $username, string $email, string $password, int $role, bool $admin): bool {
        if (!$this->isUsernameOk($username) || !$this->isEmailOk($email) || !$this->isEmailUnique($email) || !$this->isUsernameUnique($username)) throw new Exception("Invalid email or username");
        $result = 0;
        $result = $this->database->table(self::USER_TABLE)->insert([
            self::NICKNAME => $username,
            self::PW       => Nette\Security\Passwords::hash($password),
            self::EMAIL    => $email,
            self::ROLE     => $role,
            self::ADMIN    => $admin,

        ]);
        try {
            if ($result) {
                \Tracy\Debugger::log($this->tokenManager->createNew(TokenManager::ACTION_USER_VERIFY, TokenManager::DAY, $this->database->getInsertId()));
                //TODO insert MailQueue with verification link and stuff
            }
        } catch (Exception $ex) {
            $result = 0;
        }
        if (!$result) $this->database->rollBack();
        return boolval($result);
    }

    public
    function usernameExists(string $username): bool {
        return boolval($this->database->table(self::USER_TABLE)->where([
            self::NICKNAME => $username,
        ])->fetch());
    }

    public
    function emailExists(string $email): bool {
        return boolval($this->database->table(self::USER_TABLE)->where([
            self::EMAIL => $email,
        ])->fetch());
    }

    /**
     * @param string $password
     * @return bool
     * @internal param string $useless
     */
    public
    function isPasswordOk(string $password) {
        return preg_match("/[0-9](.)*[0-9]/", $password) && preg_match("/[a-z](.)*[a-z]/", $password) && preg_match("/[A-Z](.)*[A-Z]/", $password);
    }

    public
    function isEmailOk(string $email): bool {
        try {
            $domain = explode("@", $email)[1];
            //added A check after SO thread https://stackoverflow.com/a/1666823
            return filter_var($email, FILTER_VALIDATE_EMAIL) && checkdnsrr($domain, 'MX') && checkdnsrr($domain, "A");
        } catch (Exception $ex) {
            return false;
        }
    }

    public
    function isUsernameOk(string $username): bool {
        return !preg_match("/[^0-9a-zA-Z-_+]/", $username);
    }

    /**
     * Performs a role-based authorization.
     * @param  string  \Nette\Security\role
     * @param  string  resource
     * @param  string  \Nette\Security\privilege
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege) {
        diedump($role, $resource, $privilege);
    }

    public
    function getOneByName(string $username):?UserIdentity {
        return $this->getOneBy(self::NICKNAME, $username);
    }

    public
    function changeUsername(int $id, string $username): bool {
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

    private
    function getOneById(int $id):?UserIdentity {
        return $this->getOneBy(self::USER_TABLE . "." . self::USER_ID, $id);
    }

    public
    function getOneBy(string $column, string $value):?UserIdentity {
        if (!in_array($column, self::COLUMNS)) throw new Exception("column does not exist");
        $data = $this->database->table(self::USER_TABLE)->where([
            $column => $value,
        ])->fetch();
        if ($data) {
            return new UserIdentity($data->{self::USER_ID}, $data->{self::NICKNAME}, $data->{self::EMAIL}, $data->{self::DESCRIPTION}, $data->{self::ROLE}, $data->{self::ADMIN});
        }
        return null;
    }

    public
    function getOneByEmail(string $email):?UserIdentity {
        return $this->getOneBy(self::EMAIL, $email);
    }

    public function roleExists(string $role) {
        return in_array($role, self::ROLES);
    }

    /**
     * @param string $username
     * @param null|UserIdentity $user
     * @return bool true if its ok(either completely new or current user's
     */
    public function isUsernameUnique(string $username, ?UserIdentity $user = null) {
        $user_id = ($user instanceof UserIdentity) ? $user->getId() : null;
        $user2 = $this->getOneByName($username);
        return !($user2 instanceof UserIdentity && $user2->getId() !== $user_id);
    }

    /**
     * @param string $email
     * @param null|UserIdentity $user
     * @return bool true if its ok(either completely new or current user's
     */
    public function isEmailUnique(string $email, ?UserIdentity $user = null) {
        $user_id = ($user instanceof UserIdentity) ? $user->getId() : null;
        $user2 = $this->getOneByEmail($email);
        return !($user2 instanceof UserIdentity && $user2->getId() !== $user_id);
    }
}