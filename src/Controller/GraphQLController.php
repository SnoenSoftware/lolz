<?php
/**
 * @author Bjørn Snoen <bjorn.snoen@gmail.com>
 * @license proprietary
 */

namespace App\Controller;


use App\Entity\Lol;
use App\Service\LolzProvider;
use Doctrine\ORM\EntityManager;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GraphQLController extends AbstractController
{
    /**
     * @Route("/graphql", methods={"POST"})
     * @param Request $request
     * @param LolzProvider $lolzProvider
     * @return JsonResponse
     */
    public function query(Request $request, LolzProvider $lolzProvider): JsonResponse
    {
        $lolType = Lol::getGraphQlDefinition();
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'lolz' => [
                    'type' => Type::listOf($lolType),
                    'args' => [
                        'page' => Type::nonNull(Type::int()),
                        'pageSize' => Type::nonNull(Type::int())
                    ],
                    'resolve' => function ($root, $args) use ($lolzProvider) {
                        $lolz = $lolzProvider->next($args['pageSize'], $args['page']);
                        $lolArray = [];
                        /** @var Lol $lol */
                        foreach ($lolz as $lol) {
                            $data = $lol->jsonSerialize();
                            $data['fetched'] = $lol->getFetched()->getTimestamp();
                            $lolArray[] = $data;
                        }
                        return $lolArray;
                    }
                ],
            ],
        ]);

        $schema = new Schema([
            'query' => $queryType
        ]);

        $rawRequest = $request->getContent();
        $input = json_decode($rawRequest, true);
        $query = $input['query'];
        $result = GraphQL::executeQuery($schema, $query);
        $output = $result->toArray();
        return new JsonResponse([$output]);
    }
}