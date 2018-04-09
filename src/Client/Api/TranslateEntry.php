<?php
/**
 * Created by PhpStorm.
 * User: bleduc
 * Date: 09/04/2018
 * Time: 10:09
 */

namespace Weglot\Client\Api;

use JsonSerializable;
use Weglot\Client\Api\Exception\MissingRequiredParamException;

/**
 * Enum BotType
 * Used to define which bot is parsing the page.
 * Basically, most of time we recommend to use as "human"
 *
 * @package Weglot\Client\Api
 */
abstract class BotType
{
    const HUMAN = 0;
    const OTHER = 1;
    const GOOGLE = 2;
    const BING = 3;
    const YAHOO = 4;
    const BAIDU = 5;
    const YANDEX = 6;
}

if (!function_exists('array_keys_exists')) {
    /**
     * Used to check if multiple keys are defined in given array
     *
     * @param array $keys
     * @param array $arr
     * @return bool
     */
    function array_keys_exists(array $keys, array $arr)
    {
        return !array_diff_key(array_flip($keys), $arr);
    }
}

/**
 * Class TranslateEntry
 * @package Weglot\Client\Api
 */
class TranslateEntry implements JsonSerializable
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var WordCollection
     */
    protected $words;

    /**
     * TranslateEntry constructor.
     * @param array $params                     Params of the translate entry, required fields: language_from, language_to, bot, request_url & optional: title ("Empty title" by default)
     * @param WordCollection|null $words        Collection of words
     * @throws MissingRequiredParamException    If params are missing we throw this exception
     */
    public function __construct(array $params, WordCollection $words = null)
    {
        $this->setParams($params);
        $this->setWords($words);
    }

    /**
     * Default params values
     *
     * @return array
     */
    protected function defaultParams()
    {
        return [
            'title' => 'Empty title'
        ];
    }

    /**
     * Required params field names
     *
     * @return array
     */
    protected function requiredParams()
    {
        return [
            'language_from',
            'language_to',
            'bot',
            'request_url'
        ];
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @throws MissingRequiredParamException    If params are missing we throw this exception
     */
    public function setParams(array $params)
    {
        // merging default params with user params
        $this->params = array_merge($this->defaultParams(), $params);

        if (!array_keys_exists($this->requiredParams(), $this->params)) {
            throw new MissingRequiredParamException();
        }
    }

    /**
     * @return WordCollection
     */
    public function getWords()
    {
        return $this->words;
    }

    /**
     * Used to fill words collection
     * If $words is null, it would put an empty word collection
     *
     * @param WordCollection|null $words
     */
    public function setWords($words)
    {
        if ($words === null) {
            $this->words = new WordCollection();
        } else {
            $this->words = $words;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'l_from' => $this->params['language_from'],
            'l_to' => $this->params['language_to'],
            'bot' => $this->params['bot'],
            'title' => $this->params['title'],
            'request_url' => $this->params['request_url'],
            'words' => $this->words->jsonSerialize()
        ];
    }
}
