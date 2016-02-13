<?php

namespace App\Services;

use App\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;

class RegistrationService
{
    private $events;
    private $db;

    /**
     * Create a new instance of RegistrationService.
     *
     * @param Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events, DatabaseManager $db)
    {
        $this->events = $events;
        $this->db = $db;
    }

    /**
     * Register user.
     *
     * @param  array  $data
     * @param  bool  $usingThirdPartyAuth
     * @param  array  $thirdPartyAuthData
     * @return App\User
     */
    public function register(array $data, $usingThirdPartyAuth = false, array $thirdPartyAuthData = [])
    {
        if (!$usingThirdPartyAuth) {
            $user = $this->createUser($data);
        } else {
            $user = $this->createUserWithThirdPartyAuth($data, $thirdPartyAuthData);
        }

        $this->fireRegisterEvent($user);

        return $user;
    }

    /**
     * Create a new instance of user and store it in the database.
     *
     * @param  array  $data
     * @return App\User
     */
    private function createUser(array $data)
    {
        return User::create($data);
    }

    /**
     * Store user and their third party auth info.
     *
     * @param  array  $data
     * @param  array  $thirdPartyAuthData
     * @return App\User
     */
    private function createUserWithThirdPartyAuth(array $data, array $thirdPartyAuthData)
    {
        try {
            $this->db->beginTransaction();
            $user = $this->createUser($data);
            $this->addThirdPartyAuthInfoToUser($user, $thirdPartyAuthData);
            $this->db->commit();
            return $user;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Store user's third party auth info.
     *
     * @param App\User  $user
     * @param array $thirdPartyAuthData
     * @return  void
     */
    private function addThirdPartyAuthInfoToUser(User $user, array $thirdPartyAuthData)
    {
        $user->thirdPartyAuths()->create($thirdPartyAuthData);
    }

    /**
     * Fire the register event.
     *
     * @param  App\User   $user
     * @return void
     */
    private function fireRegisterEvent(User $user)
    {

    }
}
