<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageTest extends WebTestCase
{
    public function getPublicUrls()
    {
        yield ['http://localhost:8000/user/35'];
    }

    
    public function testAnonymousAccess($url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame('200');
    }

    public function testSendmessage(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('toto@toto.com');
        $client->loginUser($testUser);
        $client->request('GET', '/message/send');

        $crawler = $client->submitForm('Envoyer', [
            'message[title]' => 'test',
            'message[content]' => 'yoyoyoyyoyoyoyoy',
            'message[recipient]' => '27',
        ]);

        $this->assertResponseStatusCodeSame('302');
    }

    public function testSignup(): void
    {
        $client = static::createClient();
        $client->request('GET', '/signup');

        $crawler = $client->submitForm('Ajouter', [
            'sign_up[email]' => 'toto@toto.com',
            'sign_up[username]' => 'toto',
            'sign_up[password]' => 'toto',
        ]);

        //if email already exist it will return code 422
        $this->assertResponseStatusCodeSame('302');
    }


    public function testLoginUser()
    {
        $client = self::createClient();

        // @see https://symfony.com/doc/5.4/testing.html#logging-in-users-authentication

        // The Users's Repo
        $userRepository = static::getContainer()->get(UserRepository::class);
        // we collect user@user.com
        $testUser = $userRepository->findOneByEmail('toto@toto.com');
        // simulate $testUser being logged in
        $client->loginUser($testUser);
        // we verify that user toto can access his private profile page
        $client->request('GET', '/user/35');

        // we will have 200 code
        $this->assertResponseStatusCodeSame(200);
    }
    

    /*public function testVisitorAccess(): void
    {
        $client = static::createClient();
        $client->request('GET', '/album/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Toutes les Collections');
        $this->assertResponseStatusCodeSame('200');
    }*/
}
