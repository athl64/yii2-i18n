<?php

namespace dvixi\yii2\I18n\components;

use yii\base\InvalidConfigException;
use yii\i18n\DbMessageSource;
use yii\helpers\ArrayHelper;
use yii\di\Instance;
use yii\caching\Cache;

class I18N extends \yii\i18n\I18N
{
    /** @var string */
    public $sourceMessageTable = '{{%source_message}}';
    /** @var string */
    public $messageTable = '{{%message}}';
    /** @var array */
    public $languages;
    /** @var array */
    public $missingTranslationHandler = ['dvixi\yii2\I18n\Module', 'missingTranslation'];

    public $cachingDuration = 0;
    public $enableCaching = false;
    public $cache = 'cache';

    public $db = 'db';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!$this->languages) {
            throw new InvalidConfigException('You should configure i18n component [language]');
        }

        if( $this->languages instanceof \Closure) {
            $this->languages = $this->languages->__invoke();
        }

        $cacheConfig = [];

        if( $this->enableCaching ) {
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheConfig = [
                'cache' => $this->cache,
                'cachingDuration' => $this->cachingDuration,
                'enableCaching' => true,
            ];
        }

        $translationConfig = [
            'class' => DbMessageSource::className(),
            'db' => $this->db,
            'sourceMessageTable' => $this->sourceMessageTable,
            'messageTable' => $this->messageTable,
            'on missingTranslation' => $this->missingTranslationHandler,
        ];

        $translationConfig = ArrayHelper::merge($translationConfig, $cacheConfig);

        if (!isset($this->translations['*'])) {
            $this->translations['*'] = $translationConfig;
        }
        if (!isset($this->translations['app']) && !isset($this->translations['app*'])) {
            $this->translations['app'] = $translationConfig;
        }

        parent::init();
    }
}
