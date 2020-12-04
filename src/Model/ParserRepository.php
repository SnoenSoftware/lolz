<?php
/**
 * @license Proprietary
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Model;


use App\Entity\Feed;
use App\Model\Api\ParserInterface;
use App\Model\Parser\GenericParser;
use Psr\Http\Message\ResponseInterface;

class ParserRepository
{
    /**
     * @param Feed $feed
     * @param ResponseInterface $response
     * @return ParserInterface
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function getParserForFeed(Feed $feed, ResponseInterface $response): ParserInterface
    {
        $class = $feed->getParser();
        if (class_exists($class)) {
            return new $class($response);
        }
        throw new \InvalidArgumentException("Parser " . $class . " does not exist");
    }

    /**
     * @return string[]
     * @throws \ReflectionException
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    public function getParsers(): array
    {
        $reflector = new \ReflectionClass(GenericParser::class);
        $dir = dirname($reflector->getFileName());
        $namespace = $reflector->getNamespaceName();
        $directoryIterator = new \DirectoryIterator($dir);

        $parsers = [];

        foreach ($directoryIterator as $file) {
            $className = $namespace . '\\' . str_replace('.php', '', $file);

            if (!class_exists($className) || !$this->isRealParser($className)) {
                continue;
            }
            $parsers[] = $className;
        }

        return $parsers;
    }

    /**
     * @param string $className
     * @return bool
     * @throws \ReflectionException
     * @author Bjørn Snoen <bjorn.snoen@gmail.com>
     */
    protected function isRealParser(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }
        $reflector = new \ReflectionClass($className);

        return $reflector->implementsInterface(ParserInterface::class) && !$reflector->isAbstract();
    }
}
