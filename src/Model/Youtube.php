<?php
/**
 * @license Proprietary
 * @author bjorn
 * @copyright BRBcoffee 2020
 */

namespace App\Model;

use App\Entity\Lol;

/**
 * Class Youtube
 * @author bjorn
 */
class Youtube
{
    /**
     * @param Lol $lol
     * @return bool
     * @author bjorn
     */
    public function isYoutube(Lol $lol): bool
    {
        return count(preg_grep('/(youtube.com).+?(watch)/', [$lol->getImageUrl()]));
    }

    /**
     * @param Lol $lol
     * @return string
     * @author bjorn
     */
    public function getContent(Lol $lol): string
    {
        $imageUrl = $lol->getImageUrl();
        $parts = parse_url($imageUrl);
        parse_str($parts['query'], $params);
        return sprintf('<youtube data-view-id="%s"/>', $params['v']);
    }
}
