<?php

namespace Yarsha\JobSeekerBundle\Security\Core;


use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class JobSeekerUserProvider extends FOSUBUserProvider
{

    /**
     * {@inheritdoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        $service = $response->getResourceOwner()->getName();

        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy([$property => $username])) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);

    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        $data = $response->getResponse();

        if (array_key_exists('firstName', $data)) {
            $firstname = $data['firstName'];
        } else {
            $firstname = $response->getFirstName();
        }

        if (array_key_exists('lastName', $data)) {
            $lastname = $data['lastName'];
        } else {
            $lastname = $response->getLastName();
        }


        if (array_key_exists('pictureUrls', $data)) {
            $profile = $data['pictureUrls']['values'][0];
        } else {
            $profile = $response->getProfilePicture();
        }

        if (array_key_exists('gender', $data)) {
            $gender = $data['gender'];
        } else {
            $gender = '';
        }

        $username = $response->getUsername();
        $email = $response->getEmail() ? $response->getEmail() : $response->getUsername();
        $user = $this->userManager->findUserBy([
            $this->getProperty($response) => $username
        ]);

        $service = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($service);
        $setter_id = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';


        if (null === $user) {

            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());

            $user->setUsername($username);
            $user->setEmail($email);
            $user->setFirstName($firstname);
            $user->setLastName($lastname);
            $user->setPassword($username);
            $user->setGender($gender);
            //$user->setContactEmail($email);

            $file = $profile;
            if ($file instanceof UploadedFile) {
                $user->upload();
            }
            $user->setPath($profile);
            $user->setEnabled(true);
            $this->userManager->updateUser($user);

            return $user;
        }

        $user = parent::loadUserByOAuthUserResponse($response);

        $user->$setter_token($response->getAccessToken());

        return $user;


    }

}
