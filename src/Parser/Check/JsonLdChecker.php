<?php
/**
 * Created by PhpStorm.
 * User: bleduc
 * Date: 12/04/2018
 * Time: 15:45
 */

namespace Weglot\Parser\Check;

use Weglot\Client\Api\Enum\WordType;
use Weglot\Client\Api\Exception\InvalidWordTypeException;
use Weglot\Client\Api\WordEntry;
use Weglot\Parser\Util\JsonLd;

class JsonLdChecker extends AbstractChecker
{
    /**
     * @return array
     * @throws InvalidWordTypeException
     */
    public function handle()
    {
        $jsons = [];

        foreach ($this->dom->find('script[type="application/ld+json"]') as $k => $row) {
            $json = json_decode($row->innertext, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = JsonLd::get($json, 'description');

                if ($value !== null) {
                    JsonLd::add($this->getParser()->getWords(), $value);
                    $jsons[] = [
                        'node' => $row,
                        'json' => $json
                    ];
                }
            }
        }

        return $jsons;
    }
}
