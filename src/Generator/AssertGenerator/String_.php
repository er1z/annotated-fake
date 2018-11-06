<?php


namespace Er1z\FakeMock\Generator\AssertGenerator;


use Er1z\FakeMock\Annotations\AnnotationCollection;
use Er1z\FakeMock\Annotations\FakeMockField;
use phpDocumentor\Reflection\Type;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\Language;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Constraints\Regex;

class String_ implements GeneratorInterface
{

    public function generateForType(\ReflectionProperty $property, FakeMockField $configuration, AnnotationCollection $annotations, ?Type $type = null): FakeMockField
    {

        if(!$configuration->useAsserts){
            return $configuration;
        }

        $configuration = $this->getDateTimeTypes($configuration, $annotations);

        $configuration = $this->getIpTypes($configuration, $annotations);

        $configuration = $this->getSingleChoiceTypes($configuration, $annotations);

        if($emailConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Email::class)){
            $configuration->faker = 'email';
        }

        if($emailConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Url::class)){
            $configuration->faker = 'url';
        }

        if($regexConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Regex::class)){
            /**
             * @var Regex $regexConfig
             */
            $configuration->faker = null;
            $configuration->regex = $regexConfig->pattern;
        }

        if($uuidConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Uuid::class)){
            $configuration->faker = 'uuid';
        }

        if($ibanConfig = $annotations->findOneBy(Iban::class)){
            $configuration->faker = 'iban';
        }

        if($languageConfig = $annotations->findOneBy(Language::class)){
            $configuration->faker = 'languageCode';
        }

        if($localeConfig = $annotations->findOneBy(Locale::class)){
            $configuration->faker = 'locale';
        }

        if($countryConfig = $annotations->findOneBy(Country::class)){
            $configuration->faker = 'countryCode';
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
                    $configuration->faker = 'ipv6';
                    break;
                default:
                case '4':
                    $configuration->faker = 'ipv4';
                    break;
                case 'a':
                    $configuration->faker = mt_rand(0,1) ? 'ipv4' : 'ipv6';
                    break;
            }
        }

        return $configuration;
    }

    public function getDateTimeTypes(FakeMockField $configuration, AnnotationCollection $annotations): FakeMockField
    {
        // todo: class_exists
        if($datetimeConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\DateTime::class)){
            $configuration->faker = 'iso8601';
        }

        if($dateConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Date::class)){
            $configuration->faker = 'date';
        }

        if($dateConfig = $annotations->findOneBy(\Symfony\Component\Validator\Constraints\Time::class)){
            $configuration->faker = 'time';
        }

        return $configuration;
    }

    public function getSingleChoiceTypes(FakeMockField $configuration, AnnotationCollection $annotations): FakeMockField
    {
        if($choiceConfig = $annotations->findOneBy(Choice::class)){
            $choices = $choiceConfig->choices;
            $configuration->regex = preg_quote(array_rand($choices));
        }

        return $configuration;

    }
}