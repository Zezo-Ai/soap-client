<?php

declare(strict_types=1);

namespace Phpro\SoapClient\CodeGenerator\Assembler;

use Laminas\Code\Generator\MethodGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Phpro\SoapClient\Caller\Caller;
use Phpro\SoapClient\CodeGenerator\Context\ClientContext;
use Phpro\SoapClient\CodeGenerator\Context\ContextInterface;
use Phpro\SoapClient\Exception\AssemblerException;

class ClientConstructorAssembler implements AssemblerInterface
{
    public function canAssemble(ContextInterface $context): bool
    {
        return $context instanceof ClientContext;
    }

    public function assemble(ContextInterface $context)
    {
        if (!$context instanceof ClientContext) {
            throw new AssemblerException(
                __METHOD__.' expects an '.ClientContext::class.' as input '.get_class($context).' given'
            );
        }

        $class = $context->getClass();
        try {
            $property = (new PropertyGenerator('caller'))
                ->setVisibility(PropertyGenerator::VISIBILITY_PRIVATE)
                ->omitDefaultValue(true);

            $property->setType(TypeGenerator::fromTypeString(Caller::class));

            $class->addPropertyFromGenerator($property);

            $class->addMethodFromGenerator(
                (new MethodGenerator('__construct'))
                    ->setParameter(new ParameterGenerator('caller', Caller::class))
                    ->setVisibility(MethodGenerator::VISIBILITY_PUBLIC)
                    ->setBody('$this->caller = $caller;')
            );
        } catch (\Exception $e) {
            throw AssemblerException::fromException($e);
        }

        return true;
    }
}
