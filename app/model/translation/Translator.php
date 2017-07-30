<?php


class Translator implements \Nette\Localization\ITranslator {
    private static $instance;


    private $table;

    private function __construct() {
        $this->table = English::getTable();
    }

    public static function instance(): Translator {
        if (!self::$instance instanceof Translator) {
            self::$instance = new Translator();
        }
        return self::$instance;
    }

    /**
     * Translates the given string.
     * @param  string   \Nette\Localization\message
     * @param  int      \Nette\Localization\plural count
     * @return string
     */
    function translate($message, $count = null) {
        $newMessage = @$this->table[$message];
        if (!is_string($newMessage)) trigger_error("Translation not found for '" . $message . "'");
        return (string)$newMessage;
    }
}