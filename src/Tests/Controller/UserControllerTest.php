<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Service\UserService;
use App\Entity\User_;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class UserControllerTest extends WebTestCase
{
    private $client;
    private $userServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userServiceMock = $this->createMock(UserService::class);
        $this->client = static::createClient();
    }

    public function testListUsers()
    {
        $expectedUsers = [
            ['id' => 1, 'firstname' => 'Valentin', 'lastname' => 'Heeman'],
            ['id' => 2, 'firstname' => 'Pierre', 'lastname' => 'Jean'],
        ];
    
        $this->userServiceMock->expects($this->once())
            ->method('findUsers')
            ->with('', null)
            ->willReturn($expectedUsers);
    
        $this->client->getContainer()->set(UserService::class, $this->userServiceMock);
    
        $this->client->request('GET', '/users/list', []);
    
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    
        $this->assertJson($this->client->getResponse()->getContent());
    
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
    
        $this->assertCount(2, $responseData);
    
        $this->assertEquals($expectedUsers, $responseData);
    }

    #[ReturnTypeWillChange]
    public function testFindUser(): void
    {
        $userId = 1;
        $expectedUser = new User_(); 

        $expectedUser->setFirstname('Valentin');
        $expectedUser->setLastname('Heeman');

        $this->userServiceMock->expects($this->once())
            ->method('findUserById')
            ->with($userId)
            ->willReturn($expectedUser);

        $this->client->getContainer()->set(UserService::class, $this->userServiceMock);

        $this->client->request('GET', "/user/get/{$userId}");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($expectedUser->getFirstname(), $responseData['firstname']);
        $this->assertEquals($expectedUser->getLastname(), $responseData['lastname']);
    }

    public function testUpdateUserAction(): void
    {
        $userId = 1;
        $requestData = ['firstname' => 'Valentin', 'lastname' => 'Heeman'];
        
        $updatedUser = new User_();
        $updatedUser->setFirstname($requestData['firstname']);
        $updatedUser->setLastname($requestData['lastname']);
    
        $this->userServiceMock->expects($this->once())
            ->method('updateUser')
            ->with($userId, $requestData['firstname'], $requestData['lastname'])
            ->willReturn($updatedUser);
    
        $this->client->getContainer()->set(UserService::class, $this->userServiceMock);
        $this->client->request('PUT', "/user/update/{$userId}", [], [], [], json_encode($requestData));
    
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
    
        $this->assertEquals($updatedUser->getFirstname(), $responseData['firstname']);
        $this->assertEquals($updatedUser->getLastname(), $responseData['lastname']);
    }
    

    public function testDeleteUserAction(): void
    {
        $userId = 1;

        $this->userServiceMock->expects($this->once())
            ->method('deleteUser')
            ->with($userId);

        $this->client->getContainer()->set(UserService::class, $this->userServiceMock);

        $this->client->request('DELETE', "/user/delete/{$userId}");

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals(['message' => 'User deleted'], $responseData);
    }

    public function testNewUser(): void
    {
        $requestData = ['firstname' => 'Valentin', 'lastname' => 'Heeman'];

        $newUser = new User_();
        $newUser->setFirstname($requestData['firstname']);
        $newUser->setLastname($requestData['lastname']);

        $this->userServiceMock->expects($this->once())
            ->method('createUser')
            ->with($requestData['firstname'], $requestData['lastname'])
            ->willReturn($newUser);

        $this->client->getContainer()->set(UserService::class, $this->userServiceMock);
        $this->client->request('POST', '/user/create', [], [], [], json_encode($requestData));

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($newUser->getFirstname(), $responseData['firstname']);
        $this->assertEquals($newUser->getLastname(), $responseData['lastname']);
    }
}
