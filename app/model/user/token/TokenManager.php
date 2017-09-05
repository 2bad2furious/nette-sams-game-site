<?php


use Nette\Utils\DateTime;

class TokenManager {
    //db columns + table name
    const TOKEN_TABLE = "token",
        TOKEN_STRING = "token",
        TOKEN_ID = "token_id",
        EXPIRE = "expire",
        USED = "used",
        CREATED = "created",
        ACTION = "action",
        TOKEN_STRING_LENGTH = 40;

    const CHARSET = "a-zA-Z0-9";

    /* int DAY = 60*60*24 */
    const DAY = 86400;

    /* int HOUR = 60*60 */
    const HOUR = 3600;

    const ACTION_USER_VERIFY = "user-verify",
        ACTION_RESET_PASSWORD = "reset-password",
        ACTIONS = [self::ACTION_RESET_PASSWORD, self::ACTION_USER_VERIFY],
        ONE_USE_ACTIONS = [self::ACTION_USER_VERIFY, self::ACTION_RESET_PASSWORD];

    /* @var \Nette\Database\Context $database */
    private $database;

    /**
     * @var Nette\Security\User $user
     * @inject
     */
    public $user;

    /**
     * TokenManager constructor.
     * @param \Nette\Database\Context $context
     */
    public function __construct(Nette\Database\Context $context) {
        $this->database = $context;
    }

    /**
     * @param string $action name for your action
     * @param int|null $expireTime number of seconds, null for no limit
     * @param int $user_id
     * @return string
     */
    public function createNew(string $action, int $user_id, ?int $expireTime = null): string {
        $token = \Nette\Utils\Random::generate(self::TOKEN_STRING_LENGTH);
        if ($this->exists($token)) {
            return $this->createNew($action, $user_id, $expireTime);
        }

        if (!is_null($expireTime))
            $expireTime = DateTime::from($expireTime);

        $this->database->table(self::TOKEN_TABLE)->insert([
            self::TOKEN_STRING   => $token,
            UserManager::USER_ID => $user_id,
            self::ACTION         => $action,
            self::EXPIRE         => $expireTime,
        ]);
        return $token;
    }

    /**
     * @param string $token
     * @param string|null $action
     * @return bool
     */
    public function exists(string $token, ?string $action = null): bool {
        return $this->getResult($token, $action, false) instanceof StdClass;
    }

    /**
     * @param string $token
     * @param string $action
     * @param bool $check
     * @return null|Token
     */
    public function getByTokenStringAndAction(string $token, string $action, bool $check = true):?Token {
        $result = $this->getResult($token, $action, $check);

        //if exists
        if ($result instanceof StdClass) {
            $this->database->table(self::TOKEN_TABLE)->where([
                self::TOKEN_ID => $result->{self::TOKEN_ID},
            ])->update([
                self::USED => $result->{self::USED} + 1,
            ]);

            return Token::getFromStdClass($result);
        }
        return null;
    }

    private function getResult(string $token, ?string $action, bool $check) {
        $where = [
            self::TOKEN_STRING => $token,
        ];

        if ($action)
            $where[self::ACTION] = $action;

        if ($check) {
            if ($cur_user_id = intval($this->user->getId()))
                $where[UserManager::USER_ID] = $cur_user_id;

            if (in_array($action, self::ONE_USE_ACTIONS)) {
                $where[self::USED] = 0;
            }
        };
        return $this->database->table(self::TOKEN_TABLE)->where($where)->fetch();
    }
}