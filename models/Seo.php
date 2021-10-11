<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\View;

/**
 * This is the model class for table "seo".
 *
 * @property int $id
 * @property string $h1
 * @property string $title
 * @property string $keywords
 * @property string $description
 */
class Seo extends ActiveRecord
{

    public static function tableName()
    {
        return 'seo';
    }

    public function rules()
    {
        return [
            [['entity_id'],'integer'],
            [['entity_name'], 'string', 'max' => 254],
            [['keywords','description', 'title', 'h1'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => 'H1',
            'title' => 'Title',
            'keywords' => 'Keywords',
            'description' => 'Description',
            'slug' => 'ЧПУ для формирования ссылки к разделу', 
            'text' => 'Текст',
        ];
    }


    public function registerSeo(View $view)
    {
        $view->title = $this->title;


        if ($this->keywords) {
            $view->registerMetaTag([
                'name' => 'keywords',
                'content' => $this->keywords
            ], 'keywords');
        }

        if ($this->description) {
            $view->registerMetaTag([
                'name' => 'description',
                'content' => $this->description
            ], 'description');
        }
    }






}
