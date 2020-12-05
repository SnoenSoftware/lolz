<?php
/**
 * @license Proprietary
 * @author bjorn
 * @copyright BRBcoffee 2020
 */

namespace App\Controller;


use App\Model\Imgur;
use App\Model\Twitter;
use App\Service\LolzProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ApiController
 * @author bjorn
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/api/twitter", name="twitter", methods={"GET"}, condition="request.get('url') != null")
     * @param Request $request
     * @param Twitter $twitter
     * @return JsonResponse
     * @author bjorn
     */
    public function twitter(Request $request, Twitter $twitter): JsonResponse
    {
        $statusUrl = $request->get('url');
        $tweet = $twitter->getTweet($statusUrl);
        return new JsonResponse($tweet);
    }

    /**
     * @Route("/api/imgur", name="imgur", methods={"GET"}, condition="request.get('url') != null")
     * @param Request $request
     * @param Imgur $imgur
     * @return JsonResponse
     * @author bjorn
     */
    public function imgur(Request $request, Imgur $imgur): JsonResponse
    {
        $statusUrl = $request->get('url');
        $extension = pathinfo($statusUrl, PATHINFO_EXTENSION);
        if ($extension) {
            // It was already a real image url
            return new JsonResponse(['data' => ['link' => $statusUrl]]);
        }

        if (strpos($statusUrl, '/a/') === false) {
            $imageData = $imgur->getRealImageData($statusUrl);
        } else {
            $imageData = $imgur->getAlbumData($statusUrl);
        }
        return new JsonResponse($imageData);
    }

    /**
     * @Route("/api/more/page/{page}", name="more", requirements={"page"="\d+"})
     * @param int $page
     * @param LolzProvider $lolzProvider
     * @return JsonResponse
     */
    public function moreLolz(LolzProvider $lolzProvider, int $page = 0): JsonResponse
    {
        try {
            $lolz = $lolzProvider->next(30, $page);
        } catch (\Exception $e) {
            $lolz = [];
        }
        $asArray = [];
        foreach ($lolz as $lol) {
            $asArray[] = $lol;
        }
        return new JsonResponse($asArray);
    }
}
