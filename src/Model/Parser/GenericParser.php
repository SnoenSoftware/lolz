<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Model\Parser;


use App\Entity\Lol;
use App\Model\Api\ParserAbstract;

/**
 * Class GenericParser
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 */
class GenericParser extends ParserAbstract
{
    /**
     * @return Lol
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    public function next(): Lol
    {
        return new Lol();
    }
}
