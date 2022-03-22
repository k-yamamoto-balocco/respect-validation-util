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

namespace GitBalocco\RespectValidationUtil\Structure;

use GitBalocco\CommonStructures\Collection\AbstractValueCollection;
use GitBalocco\CommonStructures\Collection\ValueCollectionInterface;

/**
 * ValidationResultCollection
 *
 * @package GitBalocco\RespectValidationUtil\Structure
 */
class ValidationResultCollection extends AbstractValueCollection implements ValidationResultInterface
{
    private bool $isValid = true;

    public function __construct(private string $specKey, private string $specName)
    {
        parent::__construct([]);
    }

    protected static function valueClass(): string
    {
        return ValidationResultInterface::class;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getSpecKey(): string
    {
        return $this->specKey;
    }

    public function getSpecName(): string
    {
        return $this->specName;
    }

    public function getMessages(): array
    {
        $messages = [];
        foreach ($this->getIterator() as $key => $validationResultInterface) {
            $messages[$key] = $validationResultInterface->getMessages();
        }
        return $messages;
    }

    public function put($key, $item): ValueCollectionInterface
    {
        parent::put($key, $item);
        //コレクションに追加した後に、バリデーション結果をマージ
        $this->isValid = ($this->isValid && $item->isValid());
        return $this;
    }

    public function add($item): ValueCollectionInterface
    {
        parent::add($item);
        //コレクションに追加した後に、バリデーション結果をマージ
        $this->isValid = ($this->isValid && $item->isValid());
        return $this;
    }

    /**
     * get
     *
     * @param string ...$keys
     * @return ValidationResultInterface
     * @author kenji yamamoto <k.yamamoto@balocco.info>
     */
    public function get(string ...$keys): ValidationResultInterface
    {
        if (count($keys) === 0) {
            return $this;
        }
        $key = array_shift($keys);
        return ($this->collection[$key] ?? (new NullValidationResult))->get(...$keys);
    }

    public function getMessage(): string
    {
        /** @var ValidationResultInterface $validationResultInterface */
        foreach ($this as $validationResultInterface) {
            $message = $validationResultInterface->getMessage();
            if ($message !== "") {
                return $message;
            }
        }
        return '';
    }


}