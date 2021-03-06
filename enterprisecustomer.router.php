<?php
//Define interface class for router
use \Psr\Http\Message\ServerRequestInterface as Request;                    //PSR7 ServerRequestInterface   >> Each router file must contains this
use \Psr\Http\Message\ResponseInterface as Response;                        //PSR7 ResponseInterface        >> Each router file must contains this

//Define your modules class
use \modules\enterprise_customer\EnterpriseCustomer as EnterpriseCustomer;  //Your main modules class

//Define additional class for any purpose
use \classes\middleware\ApiKey as ApiKey;                                   //ApiKey Middleware             >> To authorize request by using ApiKey generated by reSlim

    
    // Get module information (include cache and for public user)
    $app->map(['GET','OPTIONS'],'/enterprise_customer/get/info/', function (Request $request, Response $response) {
        $ec = new EnterpriseCustomer($this->db);
        $body = $response->getBody();
        $response = $this->cache->withEtag($response, $this->etag2hour.'-'.trim($_SERVER['REQUEST_URI'],'/'));
        $body->write($ec->viewInfo());
        return classes\Cors::modify($response,$body,200,$request);
    })->add(new ApiKey);

    // Installation 
    $app->get('/enterprise_customer/install/{username}/{token}', function (Request $request, Response $response) {
        $ec = new EnterpriseCustomer($this->db);
        $ec->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $ec->username = $request->getAttribute('username');
        $ec->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($ec->install());
        return classes\Cors::modify($response,$body,200);
    });

    // Uninstall (This will clear all data) 
    $app->get('/enterprise_customer/uninstall/{username}/{token}', function (Request $request, Response $response) {
        $ec = new EnterpriseCustomer($this->db);
        $ec->lang = (empty($_GET['lang'])?$this->settings['language']:$_GET['lang']);
        $ec->username = $request->getAttribute('username');
        $ec->token = $request->getAttribute('token');
        $body = $response->getBody();
        $body->write($ec->uninstall());
        return classes\Cors::modify($response,$body,200);
    });