<?php
namespace common\behaviors;


use common\models\Seo;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;



/**
 *
 * @property-read Seo $prepareSeo
 * @property-read mixed $trimClassNameOwner
 * @property-read Seo $seo
 */
class  SeoBehavior extends Behavior
{

    public function events() : array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveSeo',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveSeo',
        ];
    }


    public function getSeo()
    {
        $seo = Seo::find()->where(['entity_id' => $this->owner->id, 'entity_name' => $this->getTrimClassNameOwner()])->limit(1)->one();

        if (!isset($seo)) {
            $seo = new Seo;
        }

        return $seo;
    }


    private function getTrimClassNameOwner()
    {
        return explode('\\', $this->owner::className()) [count(explode('\\', $this->owner::className())) - 1];
    }


    public function saveSeo()
    {
        $seo = $this->getSeo();
        $seo->load( Yii::$app->request->post() );

        /*Задаю дефолтное значение тдк = $owner->name */
        foreach ($seo->getAttributes() as $propName => $prop){
            if (in_array($propName, ['id', 'entity_name', 'entity_id'])) continue;
            if (empty($prop)){
                $seo->$propName = $this->owner->name;
            }
        }

        if ( $seo->isNewRecord ) {
            $seo->entity_name = $this->getTrimClassNameOwner();
            $seo->entity_id = $this->owner->id;
        }

        return $seo->save();
    }



    /*Заменить переменные на аттрибуты (Если нужных аттрибутов нет, надо переопределить тогда метод в модели*/
    public function getPrepareSeo()
    {
        $pattern = '/\{\$\w+\}/';
        $seo = $this->getSeo();
        $owner = $this->owner;

        foreach ($seo->getAttributes() as $propName => $prop){
            if (in_array($propName, ['id', 'entity_name', 'entity_id'])) continue;

            $seo->$propName = preg_replace_callback(
                $pattern,
                function( $matches ) use ($prop, $owner) {

                    $attributeNameOnModel = trim( $matches[0], '{$}' );

                    if ( !empty( $owner->$attributeNameOnModel ) ) {
                        return $owner->$attributeNameOnModel ;
                    }

                    return $matches[0];

                },
                $seo->$propName
            );
        }

        return $seo;
    }




}