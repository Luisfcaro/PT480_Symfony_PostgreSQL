<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Repository\UserRepository;
use Exception;

class MiddlewareAuthListener {

    private $jwtEncoder;
    private $userRepository;


    public function __construct(
        JWTEncoderInterface $jwtEncoder,
        UserRepository $userRepository,
    ){
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
        
    }


    public function onKernelRequest(RequestEvent $event) {
        $request = $event->getRequest();

        if (!$this->RouteUnlocked($request)) {

            try {

                if (!$request->headers->has('Token')) {  
                    throw new AuthenticationCredentialsNotFoundException("No token found");
                } else {
                    $token = $request->headers->get('Token');
                    $payload = $this->jwtEncoder->decode($token);
                    $user = $this->userRepository->findOneBy(['email' => $payload['email']]);
                }

                if (!$user) {
                    throw new Exception("User not found");
                }


            } catch (\Exception $e) {
                $event->setResponse(new JsonResponse(['error' => $e->getMessage()], 401));
            }

        }

    }

    public function RouteUnlocked($request){
        $route = $request->attributes->get('_route');
        $UnlockedRoutes = ['login', 'register'];

        return in_array($route, $UnlockedRoutes);
    }


}
