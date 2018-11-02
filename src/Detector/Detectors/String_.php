<?php


namespace Er1z\FakeMock\Detector\Detectors;


use Er1z\FakeMock\Annotations\AnnotationCollection;
use Er1z\FakeMock\Annotations\FakeMockField;
use phpDocumentor\Reflection\Type;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\Regex;

class String_ implements DetectorInterface
{

    public function getConfigurationForType(\ReflectionProperty $property, FakeMockField $configuration, AnnotationCollection $annotations, ?Type $type = null): FakeMockField
    {

        if(!$configuration->useAsserts){
            return $configuration;
        }

        $configuration = $this->getDateTimeTypes($configuration, $annotations);

        $configuration = $this->getIpTypes($configuration, $annotations);

        if($emailConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Email::class)){
            $configuration->method = 'email';
        }

        if($emailConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Url::class)){
            $configuration->method = 'url';
        }

        if($regexConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Regex::class)){
            /**
             * @var Regex $regexConfig
             */
            $configuration->method = null;
            $configuration->regex = $regexConfig->pattern;
        }

        if($uuidConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Uuid::class)){
            $configuration->method = 'uuid';
        }

        return $configuration;
    }

    public function getIpTypes(FakeMockField $configuration, AnnotationCollection $annotations): FakeMockField
    {
        if($ipConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Ip::class)){
            /**
             * @var $ipConfig Ip
             */
            switch($ipConfig->version[0]){
                case '6':
                    $configuration->method = 'ipv6';
                    break;
                default:
                case '4':
                    $configuration->method = 'ipv4';
                    break;
                case 'a':
                    $configuration->method = mt_rand(0,1) ? 'ipv4' : 'ipv6';
                    break;
            }
        }

        return $configuration;
    }

    public function getDateTimeTypes(FakeMockField $configuration, AnnotationCollection $annotations): FakeMockField
    {
        // todo: class_exists
        if($datetimeConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\DateTime::class)){
            $configuration->method = 'iso8601';
        }

        if($dateConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Date::class)){
            $configuration->method = 'date';
        }

        if($dateConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Time::class)){
            $configuration->method = 'time';
        }

        return $configuration;
    }
}