<?php


class Token {
    private $id = 0;
    private $token = "";
    private $user_id = 0;
    private $action = "";
    private $expire;
    private $created;
    private $used = 0;

    /**
     * Token constructor.
     * @param int $id
     * @param string $token
     * @param int $user_id
     * @param string $action
     * @param \Nette\Utils\DateTime|null $expire
     * @param \Nette\Utils\DateTime $created
     * @param int $used
     */
    public function __construct(int $id, string $token, int $user_id, string $action, ?Nette\Utils\DateTime $expire, Nette\Utils\DateTime $created, int $used) {
        $this->id = $id;
        $this->token = $token;
        $this->user_id = $user_id;
        $this->action = $action;
        $this->expire = $expire;
        $this->created = $created;
        $this->used = $used;
    }

    public static function getFromStdClass(StdClass $result): Token {
        return new Token($result->{TokenManager::TOKEN_ID}, $result->{TokenManager::TOKEN_STRING}, $result->{UserManager::USER_ID}, $result->{TokenManager::ACTION}, $result->{TokenManager::EXPIRE}, $result->{TokenManager::CREATED}, $result->{TokenManager::USED});
    }

    public function isUsed(): bool {
        return $this->getUsed() !== 0;
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
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @return int
     */
    public function getUserId(): int {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getAction(): string {
        return $this->action;
    }

    /**
     * @return \Nette\Utils\DateTime|null
     */
    public function getExpire():?Nette\Utils\DateTime {
        return $this->expire;
    }

    /**
     * @return \Nette\Utils\DateTime
     */
    public function getCreated(): \Nette\Utils\DateTime {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getUsed(): int {
        return $this->used;
    }
}