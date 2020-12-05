<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2020
 */

namespace App\Model\Api;


use Psr\Http\Message\ResponseInterface;

/**
 * Class ParserAbstract
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 */
abstract class ParserAbstract implements ParserInterface
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * ParserAbstract constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @return \DateTimeImmutable
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getNow(): \DateTimeImmutable
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new \DateTimeImmutable('now');
    }
}
