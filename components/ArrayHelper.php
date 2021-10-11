<?php
namespace common\components;


use common\models\Article;
use common\models\Category;
use common\models\Page;

use DOMDocument;
use SimpleXMLElement;
use Yii;
use yii\base\Component;
use yii\helpers\Url;
use yii\web\View;

class ArrayHelper extends Component
{

    public static function convertArrayObjectsToArrayArrays(array $arrayObjects)
    {
        foreach ($arrayObjects as $objectKey => $object){
            $arrayObjects[$objectKey] = $object->getAttributes();
        }
        return $arrayObjects;
    }


}