<?php

/**
 * Balocco Inc.
 * ～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～
 * 株式会社バロッコはシステム設計・開発会社として、
 * 社員・顧客企業・パートナー企業と共に企業価値向上に全力を尽くします
 *
 * 1. プロフェッショナル集団として人間力・経験・知識を培う
 * 2. システム設計・開発を通じて、顧客企業の成長を活性化する
 * 3. 顧客企業・パートナー企業・弊社全てが社会的意義のある事業を営み、全てがwin-winとなるビジネスをする
 * ～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～～
 * 本社所在地
 * 〒101-0032　東京都千代田区岩本町2-9-9 TSビル4F
 * TEL:03-6240-9877
 *
 * 大阪営業所
 * 〒530-0063　大阪府大阪市北区太融寺町2-17 太融寺ビル9F 902
 *
 * https://www.balocco.info/
 * © Balocco Inc. All Rights Reserved.
 */

namespace GitBalocco\RespectValidationUtil\Driver;

use GitBalocco\RespectValidationUtil\Structure\ValidationResult;
use GitBalocco\RespectValidationUtil\Structure\ValidationResultInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validatable;

class RespectValidationDriver implements ValidationDriverInterface
{
    public function __construct(
        private Validatable $definition,
        private string $specKey,
        private string $specName,
        private array $customMessages
    ) {
    }

    public function validate(mixed $candidate): ValidationResultInterface
    {
        $messages = [];
        $message = '';
        try {
            $this->definition->assert($candidate);
            $isValid = true;
        } catch (ValidationException $e) {
            $isValid = false;

            if (is_a($e, NestedValidationException::class)) {
                $messages = $e->getMessages($this->customMessages);
                $message = $messages[array_key_first($messages)];
            } else {
                $messages = [$e->getMessage()];
                $message = $e->getMessage();
            }
        }
        return new ValidationResult($isValid, $this->specKey, $this->specName, $messages, $message);
    }

}