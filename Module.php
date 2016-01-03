<?php

namespace dvixi\yii2\I18n;

use Yii;
use yii\i18n\MissingTranslationEvent;
use dvixi\yii2\I18n\models\SourceMessage;

class Module extends \yii\base\Module
{
    public $pageSize = 50;

    public static function t($message, $params = [], $language = null)
    {
        return Yii::t('dvixi\yii2\i18n', $message, $params, $language);
    }

    /**
     * @param MissingTranslationEvent $event
     */
    public static function missingTranslation(MissingTranslationEvent $event)
    {
        $sourceMessage = static::getSourceMessage($event);

        if (!$sourceMessage) {
            $sourceMessage = new SourceMessage;
            $sourceMessage->setAttributes([
                'category' => $event->category,
                'message' => $event->message
            ], false);
            $sourceMessage->save(false);
        }
        $sourceMessage->initMessages();
        $sourceMessage->saveMessages();
    }

    protected static function getSourceMessage(MissingTranslationEvent $event)
    {
        $sourceMessage = null;
        $key = [
            __CLASS__,
            $event->category,
            $event->language,
            $event->message,
        ];

        if( isset(Yii::$app->i18n->enableCaching) && Yii::$app->i18n->enableCaching ) {
            if( !$sourceMessage = Yii::$app->i18n->cache->get($key) ) {
                $sourceMessage = static::getSourceMessageFromDb($event);
                Yii::$app->i18n->cache->set($key, $sourceMessage, Yii::$app->i18n->cachingDuration);
            }
        } else {
            $sourceMessage = static::getSourceMessageFromDb($event);
        }

        return $sourceMessage;
    }

    protected static function getSourceMessageFromDb(MissingTranslationEvent $event)
    {
        $driver = Yii::$app->getDb()->getDriverName();
        $caseInsensitivePrefix = $driver === 'mysql' ? 'binary' : '';

        return SourceMessage::find()
            ->where('category = :category and message = ' . $caseInsensitivePrefix . ' :message', [
                ':category' => $event->category,
                ':message' => $event->message
            ])
            ->with('messages')
            ->one();
    }
}
