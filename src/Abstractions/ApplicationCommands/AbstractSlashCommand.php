<?php

namespace Hephaestus\Framework\Abstractions\ApplicationCommands;

use Discord\Discord;
use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Hephaestus;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Psr\Http\Message\RequestInterface;

use function React\Async\await;

abstract class AbstractSlashCommand
extends Command
implements InteractionHandler
// , RequestInterface
{
    use InteractsWithLoggerProxy;

    public function __construct()
    {
        $attributes = array_merge(
            [
                "type"                          => Command::CHAT_INPUT,
                "description"                   => "An Hephaestus command",
                "default_member_permissions"    => 0,
            ],
            get_class_vars($this::class),
        );
        parent::__construct(app(Discord::class), $attributes);
        $name = class_basename($this);
        $this->log("debug", "Constructing <fg=cyan>{$this->name}</>", [__METHOD__]);
    }

    public function __destruct()
    {
        $this->log("debug", "Destructing <fg=cyan>{$this->name}</>", [__METHOD__]);
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminatorAttributeName(): string
    {
        return 'name';
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminator(): string
    {
        return $this[$this->getDiscriminatorAttributeName()];
    }

    /**
     * @inheritdoc
     */
    public string $name;

    /**
     * @inheritdoc
     */
    public string $description;

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     *
     * @return string
     */
    public function getRequestTarget()
    {

    }

    // /**
    //  * Return an instance with the specific request-target.
    //  *
    //  * If the request needs a non-origin-form request-target — e.g., for
    //  * specifying an absolute-form, authority-form, or asterisk-form —
    //  * this method may be used to create an instance with the specified
    //  * request-target, verbatim.
    //  *
    //  * This method MUST be implemented in such a way as to retain the
    //  * immutability of the message, and MUST return an instance that has the
    //  * changed request target.
    //  *
    //  * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
    //  *     request-target forms allowed in request messages)
    //  * @param string $requestTarget
    //  * @return static
    //  */
    // public function withRequestTarget(string $requestTarget);

    // /**
    //  *
    //  */
    // public function getMethod();

    // /**
    //  * @inheritdoc
    //  */
    // public function withMethod(string $method)
    // {

    // }

    // /**
    //  * @inheritdoc
    //  */
    // public function getUri() {
    //     return get_class($this);
    // }

    // /**
    //  * @inheritdoc
    //  */
    // public function withUri(UriInterface $uri, bool $preserveHost = false)
    // {
    //     return true;
    // }
}
