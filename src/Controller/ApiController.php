<?php
/**
 * @license Proprietary
 * @author bjorn
 * @copyright Visma Digital Commerce 2019
 */

namespace App\Controller;


use App\Model\Imgur;
use App\Model\Twitter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $imageData = $imgur->getRealImageData($statusUrl);
        return new JsonResponse($imageData);
    }
}
