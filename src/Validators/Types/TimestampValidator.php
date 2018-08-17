<?php

namespace Dto\Validators\Types;

use Dto\Exceptions\InvalidDataTypeException;
use Dto\Exceptions\InvalidNumberValueException;
use Dto\Exceptions\InvalidScalarValueException;
use Dto\JsonSchemaAccessorInterface;
use Dto\TypeDetectorInterface;
use Dto\Validators\AbstractValidator;
use Dto\Validators\ValidatorInterface;

class TimestampValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * Check multipleOf, maximum, exclusiveMaximum, minimum, exclusiveMinimum
     * @link http://json-schema.org/latest/json-schema-validation.html#rfc.section.5.1
     * @param $timestamp mixed
     * @param $schema array
     * @return mixed
     * @throws InvalidScalarValueException
     */
    public function validate($timestamp, array $schema)
    {
        $this->schemaAccessor = $this->container->make(JsonSchemaAccessorInterface::class)->factory($schema);

        $this->checkDataType($timestamp);
        return $timestamp;
    }

    protected function checkDataType($number)
    {
        if (!$this->container->make(TypeDetectorInterface::class)->isCarbon($number)) {
            throw new InvalidDataTypeException('Timestamp could not be resolved to a carbon object.');
        }
    }

}