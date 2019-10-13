<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 * @copyright Visma Digital Commerce 2019
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
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    public function next(): ?Lol;
}
