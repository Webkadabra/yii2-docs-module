<?php


if ($availableTranslations) { ?>
<div class="col-md-4 pull-right">
<div class="panel">
<div class="panel-body">

    <?php
    echo Yii::t('app', 'This page is available in other languages') . ':';
    $navItems = [];
    foreach ($availableTranslations as $language => $link) {
        $navItems[] = [
          'label' => $language,
          'url' => $link,
        ];
    }
    echo \yii\bootstrap\Nav::widget(['items' => $navItems]); ?>
</div>
</div>
</div>
    <?php
}
echo $content;
