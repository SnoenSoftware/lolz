<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Model\Parser;

use App\Entity\Lol;
use App\Model\Api\ParserAbstract;

/**
 * Class XkcdParser
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 */
class XkcdParser extends ParserAbstract
{
    /**
     * @var \SimpleXMLElement
     */
    protected $responseBody;

    /**
     * @var int
     */
    protected $pointer = 0;

    /**
     * @var \SimpleXMLElement
     */
    protected $pointingAt;


    /**
     * @return Lol|null
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function next(): ?Lol
    {
        $body = $this->getResponseBody();
        $this->pointingAt = $body->entry[$this->pointer++];

        $lol = new Lol();
        $lol->setFetched($this->getNow());

        try {
            $lol->setImageUrl($this->getImageUrl())
                ->setCaption($this->getCaption())
                ->setTitle($this->getTitle())
                ->setUrl($this->getUrl());
        } catch (\Exception $e) {
            if ($this->pointingAt == null) {
                return null;
            } else {
                return $this->next();
            }
        }
        return $lol;
    }

    /**
     * @return string
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getImageUrl(): string
    {
        try {
            return $this->getSummary()['src'];
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * @return string
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function getUrl(): string
    {
        return $this->pointingAt->link['href'];
    }

    /**
     * @return string|null
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     * @throws \Exception
     */
    protected function getCaption(): ?string
    {
        return $this->getSummary()['title'];
    }


    protected function getResponseBody(): \SimpleXMLElement
    {
        if (!isset($this->responseBody)) {
            $this->responseBody = new \SimpleXMLElement($this->getResponse()->getBody()->getContents());
        }
        return $this->responseBody;
    }

    /**
     * @return \SimpleXMLElement
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     * @throws \Exception
     */
    protected function getSummary(): \SimpleXMLElement
    {
        return new \SimpleXMLElement((string)$this->pointingAt->summary);
    }

    protected function getTitle(): string
    {
        return $this->pointingAt->title;
    }
}
