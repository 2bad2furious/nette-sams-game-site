<?php


use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

class MailerWrapper {
    const FROM_EMAIL = "no-reply@xd.cz",
        FROM_NAME = "No reply",
        SUPPORT_EMAIL = "support@xd.cz",
        SUPPORT_NAME = "Support name";

    const MAIL_TABLE = "mail",
        MAIL_ID_COLUMN = "mail_id",
        MAIL_MESSAGE_COLUMN = "message",
        MAIL_SENT_DATE_COLUMN = "sent_date";
    /**
     * @var \Nette\Database\Context
     */
    private $context;
    /**
     * @var SmtpMailer
     */
    private $mailer;
    /**
     * @var TemplateFactory
     */
    private $templateFactory;
    /**
     * @var TokenManager
     */
    private $tokenManager;

    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var \Nette\Application\LinkGenerator
     */
    private $linkGenerator;

    /**
     * MailerWrapper constructor.
     * @param \Nette\Database\Context $context
     * @param SmtpMailer $mailer
     * @param ITemplateFactory $templateFactory
     * @param TokenManager $tokenManager
     * @param Translator $translator
     * @param \Nette\Application\LinkGenerator $linkGenerator
     */
    public function __construct(\Nette\Database\Context $context, SmtpMailer $mailer, ITemplateFactory $templateFactory, TokenManager $tokenManager, Translator $translator, \Nette\Application\LinkGenerator $linkGenerator) {
        $this->context = $context;
        $this->mailer = $mailer;
        $this->templateFactory = $templateFactory;
        $this->tokenManager = $tokenManager;
        $this->translator = $translator;
        $this->linkGenerator = $linkGenerator;
    }


    public function sendRegisterationConfirmationAndVerificationEmail(UserIdentity $identity): bool {
        $template = $this->createTemplate();
        $token = $this->tokenManager->createNew(TokenManager::ACTION_USER_VERIFY, $identity->getId(), 30 * TokenManager::DAY);

        $template->title = $this->translator->translate(UserManagement::REGISTER_EMAIL_TITLE);

        $message_translation = $this->translator->translate(UserManagement::REGISTER_EMAIL_MESSAGE);
        $template->message = sprintf(
            $message_translation,
            $this->linkGenerator->link("Token:", [
                'token-action' => TokenManager::ACTION_USER_VERIFY,
                'token'        => $token,
            ]),
            $token,
            $this->linkGenerator->link("Profile:verify")
        );
        $body = $template->getLatte()->renderToString(__DIR__ . "/templates/registration.latte", $template->getParameters());
        $message = $this->createMessage();
        $message->setHtmlBody($body);
        return $this->send($message);
    }

    public function resendVerificationEmail(string $email, Token $token): bool {
        return false;
    }

    private function createTemplate(\Nette\Application\UI\Control $control = null): Template {
        $template = $this->templateFactory->createTemplate($control);
        $template->setTranslator($this->translator);
        $template->getLatte()->addProvider('uiControl', $this->linkGenerator);
        return $template;
    }

    private function createMessage(): Message {
        $message = new Message();
        $message->setFrom(self::FROM_EMAIL, self::FROM_NAME);
        $message->addReplyTo(self::SUPPORT_EMAIL, self::SUPPORT_NAME);
        return $message;
    }

    private function addToDb(Message $message): bool {
        return boolval($this->context->table(self::MAIL_TABLE)->insert([
            self::MAIL_MESSAGE_COLUMN => serialize($message),
        ]));
    }

    public function send(Message $message): bool {
        try {
            $this->mailer->send($message);
        } catch (\Nette\Mail\SmtpException $exception) {
            \Tracy\Debugger::log($exception);
            throw $exception;
            return false;
        } finally {
            return $this->addToDb($message);
        }
    }
}