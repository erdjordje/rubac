<?php

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuBAC\RequestInterface;
use RuBAC\RuBAC;
use RuBAC\UserInterface;
use SebastianBergmann\Template\RuntimeException;

class RuBACTest extends TestCase
{
    private function createUserStub(string $userRole): UserInterface
    {
        try {
            $user = $this->createStub(UserInterface::class);
        } catch (Exception $e) {
            throw new RuntimeException("Can't create user", 1, $e);
        }

        $user->method('getRole')
            ->willReturn($userRole);

        return $user;
    }

    private function createRequestStub(string $ipAddress, string $path): RequestInterface
    {
        try {
            $request = $this->createStub(RequestInterface::class);
        } catch (Exception $e) {
            throw new RuntimeException("Can't create request", 1, $e);
        }

        $request->method('getIpAddress')
            ->willReturn($ipAddress);

        $request->method('getPath')
            ->willReturn($path);

        return $request;
    }

    /**
     * @dataProvider workflow1Provider
     */
    public function testExecuteWorkflow1(UserInterface $user, RequestInterface $request, bool $expected)
    {
        $workflow = file_get_contents(__DIR__ . '/../workflows/workflow1.json');
        $service = new RuBAC($workflow);

        $this->assertEquals($expected, $service->execute($user, $request));
    }

    public function testExecuteWorkflow2()
    {
    }

    public function workflow1Provider(): array
    {
        return [
            [
                $this->createUserStub('ADMIN'),
                $this->createRequestStub('100.100.100.100', '/admin/settings'),
                true
            ],
            [
                $this->createUserStub('SUPER_ADMIN'),
                $this->createRequestStub('100.100.100.100', '/admin/settings'),
                false
            ],
            [
                $this->createUserStub('ADMIN'),
                $this->createRequestStub('100.100.100.98', '/admin/settings'),
                false
            ],
            [
                $this->createUserStub('ADMIN'),
                $this->createRequestStub('100.100.100.98', '/users'),
                true
            ],
            [
                $this->createUserStub('ADMIN'),
                $this->createRequestStub('100.100.100.100', '/admin/users'),
                true
            ],
        ];
    }
}
