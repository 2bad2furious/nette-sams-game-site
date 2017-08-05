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

    const ACTION_USER_VERIFY = "user-verify";

    /* @var \Nette\Database\Context $database */
    private $database;

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
    public function createNew(string $action = "", ?int $expireTime = null, int $user_id = 0): string {
        $token = \Nette\Utils\Random::generate(self::TOKEN_STRING_LENGTH);
        if ($this->exists($token)) {
            return $this->createNew($action, $expireTime, $user_id);
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

    public function exists(string $token): bool {
        return $this->getByToken($token) instanceof StdClass;
    }

    /**
     * @param string $token
     * @return null|StdClass
     */
    public function getByToken(string $token):?StdClass {
        $result = $this->database->table(self::TOKEN_TABLE)->where([
            self::TOKEN_STRING => $token,
        ])->fetch();
        if ($result === false) {
            $result = null;
        }
        return $result;
    }
}