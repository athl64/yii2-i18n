<?php
/**
 * @var View $this
 * @var SourceMessage $model
 */

use yii\helpers\Html;
use yii\web\View;
use dvixi\yii2\I18n\models\SourceMessage;
use dvixi\yii2\I18n\Module;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Module::t('Update') . ': ' . $model->message;
echo Breadcrumb::widget(['links' => [
    ['label' => Module::t('Translations'), 'url' => ['index']],
    ['label' => $this->title]
]]);
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Редактирование</h3>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <?php foreach ($model->messages as $language => $message) : ?>
                <?= $form->field($model->messages[$language], '[' . $language . ']translation', ['options' => ['class' => 'form-group col-sm-6']])->textarea()->label($language) ?>
            <?php endforeach; ?>
        </div>
        <div class="form-group">
            <?=
            Html::submitButton(
                $model->getIsNewRecord() ? 'Cоздать' : 'Сохранить',
                ['class' => $model->getIsNewRecord() ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
        <?php $form::end(); ?>
    </div>
</div>
