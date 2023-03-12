<?php

namespace RuBAC;

require __DIR__ . '/rules.php';

use InvalidArgumentException;

final class RuBAC
{
    private array $workflow;

    public function __construct(string $workflow)
    {
        $this->setWorkflow($workflow);
    }

    public function execute(UserInterface $user, RequestInterface $request): bool
    {
        if (!$this->isPathRestricted($request->getPath())) {
            return true;
        }

        return $this->checkRules($user, $request);
    }

    private function isPathRestricted(string $path): bool
    {
        return fnmatch($this->workflow['Path'], $path);
    }

    private function setWorkflow(string $workflow): void
    {
        $workflow = json_decode($workflow, true);

        if (empty($workflow)) {
            throw new InvalidArgumentException('Workflow must be JSON.');
        }

        $this->workflow = $workflow;
    }

    private function checkRules(UserInterface $user, RequestInterface $request): bool
    {
        $result = true;

        foreach ($this->workflow['Params'] as $param) {
            $expression = str_replace('.', '->', $param['Expression']) . '()';
            $code = "\${$param['Name']}=$expression;";

            eval($code);
        }

        foreach ($this->workflow['Rules'] as $rules) {
            $rulesResult = false;
            eval("\$rulesResult={$rules['Expression']};");
            $result = $result && $rulesResult;
        }

        return $result;
    }
}
