<?php

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use RuBAC\RequestInterface;
use RuBAC\RuBAC;
use RuBAC\UserInterface;
use SebastianBergmann\Template\RuntimeException;

class RuBACTest extends TestCase
{
    private array $workflows;

    public function setUp(): void
    {
        parent::setUp();

        $this->setWorkflows();
    }

    private function setWorkflows()
    {
        $this->workflows = array_map(function (string $workflow) {
            return file_get_contents(__DIR__ . "/../workflows/$workflow.json");
        }, ['workflow1', 'workflow2']);
    }

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

    #[DataProvider('workflow1Provider')]
    public function testExecuteWorkflow1(string $role, string $ipAddress, string $path, bool $expected)
    {
        $user = $this->createUserStub($role);
        $request = $this->createRequestStub($ipAddress, $path);

        $service = new RuBAC($this->workflows[0]);

        $this->assertEquals($expected, $service->execute($user, $request));
    }

    public static function workflow1Provider(): array
    {
        return [
            'admin has access to settings' => ['ADMIN', '100.100.100.100', '/admin/settings', true],
            'super admin can not access' => ['SUPER_ADMIN', '100.100.100.100', '/admin/settings', false],
            'wrong IP request' => ['ADMIN', '100.100.100.98', '/admin/settings', false],
            'role123 can access to resource' => ['ROLE123', '100.100.100.98', '/users', true],
            'admin has access to users' => ['ADMIN', '100.100.100.100', '/admin/users', true],
        ];
    }

    #[DataProvider('workflow2Provider')]
    public function testExecuteWorkflow2(string $role, string $ipAddress, string $path, bool $expected)
    {
        $user = $this->createUserStub($role);
        $request = $this->createRequestStub($ipAddress, $path);

        $service = new RuBAC($this->workflows[1]);

        $this->assertEquals($expected, $service->execute($user, $request));
    }

    public static function workflow2Provider(): array
    {
        return [
            'admin has access' => ['ADMIN', '100.100.100.14', '/admin/settings', true],
            'super admin has access' => ['SUPER_ADMIN', '100.100.100.12', '/admin/settings', true],
            'wrong IP request' => ['ADMIN', '100.100.100.98', '/admin/settings', false],
            'role123 can not access to resource' => ['ROLE123', '100.100.100.14', '/admin/users', false],
            'role123 can access to resource' => ['ROLE123', '100.100.100.100', '/games', true],
        ];
    }

    public function testInvalidWorkflow()
    {
        $this->expectException(InvalidArgumentException::class);

        new RuBAC('{test123}');
    }
}
