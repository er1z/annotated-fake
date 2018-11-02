<?php


namespace Er1z\FakeMock\Condition;


use Er1z\FakeMock\Annotations\AnnotationCollection;
use Er1z\FakeMock\Annotations\FakeMockField;

interface ProcessorInterface
{
    public function processConditions($object, FakeMockField $configuration, AnnotationCollection $annotations);
}