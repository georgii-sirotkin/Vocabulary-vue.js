<?php

namespace App\Services;

use App\Events\UserRegistered;
use App\Repositories\UserRepository;
use App\ThirdPartyAuthInfo;
use App\User;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;

class RegistrationService
{
    private $events;
    private $db;
    private $repository;

    /**
     * Create a new instance of RegistrationService.
     *
     * @param Illuminate\Contracts\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events, DatabaseManager $db, UserRepository $repository)
    {
        $this->events = $events;
        $this->db = $db;
        $this->repository = $repository;
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

        $this->fireUserRegisteredEvent($user);

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
            $user = $this->findOrCreateUser($data);
            $user->addThirdPartyAuthInfo(new ThirdPartyAuthInfo($thirdPartyAuthData));
            $this->db->commit();
            return $user;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Find the user with the given email in the database or create a new user.
     *
     * @param  array  $data
     * @return App\User
     */
    private function findOrCreateUser(array $data)
    {
        if (isset($data['email']) && ($user = $this->repository->findUserByEmail($data['email']))) {
            return $user;
        }

        return $this->createUser($data);
    }

    /**
     * Fire UserRegistered event.
     *
     * @param  App\User   $user
     * @return void
     */
    private function fireUserRegisteredEvent(User $user)
    {
        $this->events->fire(new UserRegistered($user));
    }
}
