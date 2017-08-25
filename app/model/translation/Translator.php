<?php


class Translator implements \Nette\Localization\ITranslator {
    private static $instance;

    private $table;


    public function __construct() {
        $this->table = English::getTable();
    }

    /**
     * Translates the given string.
     * @param  string   \Nette\Localization\message
     * @param  int      \Nette\Localization\plural count
     * @return string
     */
    function translate($message, $count = null) {
        $newMessage = @$this->table[$message];
        \Tracy\Debugger::log($message . " - " . $newMessage);
        if (!is_string($newMessage) && !in_array($message, $this->table)) trigger_error("Could not find translation for '{$message}'");
        return $newMessage ? $newMessage : $message;
    }
}