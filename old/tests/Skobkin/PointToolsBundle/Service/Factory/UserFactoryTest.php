<?php
declare(strict_types=1);

namespace Tests\Skobkin\PointToolsBundle\Service\Factory;

use App\DTO\Api\User as UserDTO;
use App\Exception\Factory\InvalidUserDataException;
use App\Factory\UserFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Repository\UserRepository;

class UserFactoryTest extends TestCase
{
    private const LOCAL_USER_ID = 1;
    private const LOCAL_USER_LOGIN = 'test-local';
    private const LOCAL_USER_NAME = 'Test Local Name';
    private const LOCAL_USER_CREATED = '1999-01-01_01:02:03';

    private const REMOTE_USER_ID = 1;
    private const REMOTE_USER_LOGIN = 'test-remote';
    private const REMOTE_USER_NAME = 'Test Remote Name';
    private const REMOTE_USER_CREATED = '2000-01-01_01:02:03';

    public function testCreateFactory(): UserFactory
    {
        $testUser = new User(
            self::LOCAL_USER_ID,
            self::LOCAL_USER_LOGIN,
            \DateTime::createFromFormat(UserFactory::DATE_FORMAT, self::LOCAL_USER_CREATED),
            self::LOCAL_USER_NAME
        );

        $logger = $this->createMock(LoggerInterface::class);
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->any())
            ->method('find')
            ->willReturnCallback(
                function ($id) use ($testUser) {
                    if (1 === $id) {
                        return $testUser;
                    }

                    return null;
                }
            );

        $userFactory = new UserFactory($logger, $userRepository);

        $this->assertInstanceOf(UserFactory::class, $userFactory);

        return $userFactory;
    }

    /**
     * @dataProvider userDtoProvider
     * @depends testCreateFactory
     */
    public function testFindOrCreateFromDTO(UserDTO $userDto, UserFactory $userFactory)
    {
        $foundUser = $userFactory->findOrCreateFromDTO($userDto);

        $this->assertInstanceOf(User::class, $foundUser);

        $this->assertEquals(self::LOCAL_USER_ID, $foundUser->getId());
        $this->assertEquals(self::REMOTE_USER_NAME, $foundUser->getName());
        $this->assertEquals(self::REMOTE_USER_LOGIN, $foundUser->getLogin());

        $testDate = \DateTime::createFromFormat(UserFactory::DATE_FORMAT, self::REMOTE_USER_CREATED);

        $this->assertEquals($testDate, $foundUser->getCreatedAt());
    }

    /**
     * @dataProvider userDtoArrayProvider
     * @depends testCreateFactory
     */
    public function testFindOrCreateFromDTOArray(array $userData, UserFactory $userFactory)
    {
        $foundUsers = $userFactory->findOrCreateFromDTOArray($userData);

        $this->assertCount(2, $foundUsers);
        $this->assertContainsOnlyInstancesOf(User::class, $foundUsers);

        $testDate = \DateTime::createFromFormat(UserFactory::DATE_FORMAT, self::REMOTE_USER_CREATED);

        $this->assertEquals(self::REMOTE_USER_ID, $foundUsers[0]->getId());
        $this->assertEquals(self::REMOTE_USER_LOGIN, $foundUsers[0]->getLogin());
        $this->assertEquals(self::REMOTE_USER_NAME, $foundUsers[0]->getName());
        $this->assertEquals($testDate, $foundUsers[0]->getCreatedAt());

        $this->assertEquals(self::REMOTE_USER_ID + 1, $foundUsers[1]->getId());
        $this->assertEquals(self::REMOTE_USER_LOGIN, $foundUsers[1]->getLogin());
        $this->assertEquals(self::REMOTE_USER_NAME, $foundUsers[1]->getName());
        $this->assertEquals($testDate, $foundUsers[1]->getCreatedAt());
    }

    /**
     * @dataProvider invalidUserDtoProvider
     * @depends testCreateFactory
     */
    public function testFindOrCreateFromDTOWithInvalidDTO(UserDTO $userDto, UserFactory $userFactory)
    {
        $this->expectException(InvalidUserDataException::class);

        $foundUser = $userFactory->findOrCreateFromDTO($userDto);
    }

    public function userDtoProvider(): array
    {
        $userDto = new UserDTO();
        $userDto->setId(self::REMOTE_USER_ID);
        $userDto->setLogin(self::REMOTE_USER_LOGIN);
        $userDto->setName(self::REMOTE_USER_NAME);
        $userDto->setCreated(self::REMOTE_USER_CREATED);

        return [[$userDto]];
    }

    public function userDtoArrayProvider(): array
    {
        $userDto1 = new UserDTO();
        $userDto1->setId(self::REMOTE_USER_ID);
        $userDto1->setLogin(self::REMOTE_USER_LOGIN);
        $userDto1->setName(self::REMOTE_USER_NAME);
        $userDto1->setCreated(self::REMOTE_USER_CREATED);

        $userDto2 = new UserDTO();
        $userDto2->setId(self::REMOTE_USER_ID + 1);
        $userDto2->setLogin(self::REMOTE_USER_LOGIN);
        $userDto2->setName(self::REMOTE_USER_NAME);
        $userDto2->setCreated(self::REMOTE_USER_CREATED);

        return [[[$userDto1, $userDto2]]];
    }

    public function invalidUserDtoProvider(): array
    {
        return [[new UserDTO()]];
    }
}
