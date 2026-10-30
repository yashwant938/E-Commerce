<?php
session_start();

use Slim\Http\Response as Response;

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

use Slim\Routing\RouteCollectorProxy;

use Slim\Psr7\Response as Psr7Response;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use DI\Container;

require __DIR__ . '/api/autoload/init.php';
require_once __DIR__ . '/api/classes/AuthorizationGate.php';
require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/files/include/php-csrf.php';

$csrf = new CSRF(
    'csrf-hashes',
    'token',   
    5*60,         
    64              
);

define('app', TRUE);

$container = new Container();
$container->set('renderer', function () {
    $phpView = new PhpRenderer("pages");
    $phpView->setLayout("layout.php");
    return $phpView;
});

AppFactory::setContainer($container);

$app = AppFactory::create();
$app->setBasePath("/FCS");
$app->addErrorMiddleware(false, false, false);
$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();


$authMiddleware = function (Request $request, RequestHandler $handler) use ($app) {
    $loadUser = function ($userId) {
        $db = Database::getInstance();
        User::constructStatic($db);
        return User::fetchUser($userId);
    };
    $redirect = function ($route) use ($app) {
        $response = new Psr7Response();
        return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor($route))->withStatus(302);
    };
    return dispatchAuthorized($request, $handler, $_SESSION, $loadUser, $redirect);
};

// Payment routes are deliberately unavailable until order binding, atomic
// transactions, retries, and reconciliation have been implemented and tested.
$paymentDisabledMiddleware = function (Request $request, RequestHandler $handler) {
    $response = new Psr7Response();
    $response->getBody()->write('Payments are disabled in this prototype.');
    return $response->withHeader('Content-Type', 'text/plain; charset=utf-8')->withStatus(503);
};


$app->get('/', function (Request $request, Response $response) use ($app,$csrf){    
    
    if(isset($_SESSION["isLoggedIn"]) && $_SESSION["isLoggedIn"]==true){
        if($_SESSION['isActive']==0) {
            $renderer = new PhpRenderer('pages');
            return $renderer->render($response, "disabled.php");
        }elseif($_SESSION['isEmailVerified']==0) {
            $response = new Psr7Response();
            return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('verifyEmailForm'))->withStatus(302);
        }elseif($_SESSION["isKYC"]==0){            
            $response = new Psr7Response();
            return $response->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('ekyc'))->withStatus(302);
        }else{
            return $this->get('renderer')->render($response, 'listing.php',['route'=>$app->getRouteCollector()->getRouteParser()]);
        }
    }else{
        $renderer = new PhpRenderer('pages');
        return $renderer->render($response, "landing.php",['route'=>$app->getRouteCollector()->getRouteParser(),'csrf'=>$csrf]);
    }
})->setName('home');

$app->get('/index.php', function (Request $request, Response $response) use ($app) {
    return $response->withStatus(302)->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('home'));
});
  

require_once "routes/user.php";
require_once "routes/property.php";

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app) {
    /*
    if ($logger) {
        $logger->error($exception->getMessage());
    }
*/
//    $payload = ['error' => $exception->getMessage()];
$response = $app->getResponseFactory()->createResponse();

    if($exception->getCode()==404){
        $renderer = new PhpRenderer('pages');
        return $renderer->render($response, "404.php",['route'=>$app->getRouteCollector()->getRouteParser()]);
    }
    
    
    $renderer = new PhpRenderer('pages');
    return $renderer->render($response, "error.php",['route'=>$app->getRouteCollector()->getRouteParser(),'code'=>$exception->getCode()]);

};

$errorMiddleware = $app->addErrorMiddleware(false, false, false);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);



try {
    $app->run();     
} catch (Exception $e){
  die( json_encode(array("status" => "failed", "message" => "This action is not allowed"))); 
}

