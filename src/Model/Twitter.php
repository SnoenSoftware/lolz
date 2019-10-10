<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Model;


use App\Entity\Lol;

/**
 * Class Twitter
 * @author Bjørn Snoen <bjorn.snoen@visma.com>
 */
class Twitter
{
    /**
     * @param Lol $lol
     * @return bool
     * @author Bjørn Snoen <bjorn.snoen@visma.com>
     */
    public function isTweet(Lol $lol): bool
    {
        return count(preg_grep('/(twitter.com).+?(status)/', [$lol->getImageUrl()]));
    }

    /**
     * @param Lol $lol
     * @return string
     * @author bjorn
     */
    public function getContent(Lol $lol): string
    {
        return sprintf('<tweet data-url="%s"/>', $lol->getImageUrl());
    }
}
