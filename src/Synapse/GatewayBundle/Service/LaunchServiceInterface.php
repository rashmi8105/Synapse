<?php

namespace Synapse\GatewayBundle\Service;

interface LaunchServiceInterface {

    public function createLaunch($personId);

    public function redirectLaunch($personId, $accessToken);

}
