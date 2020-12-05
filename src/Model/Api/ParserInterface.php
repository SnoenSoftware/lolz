<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright BRBcoffee 2020
 */

namespace App\Model\Api;


use App\Entity\Lol;
use App\Model\Parser\Exceptions\InvalidConfigurationException;
use Psr\Http\Message\ResponseInterface;

interface ParserInterface
{
    /**
     * ParserInterface constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response);


    /**
     * @throws InvalidConfigurationException
     * @return Lol
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function next(): ?Lol;
}
