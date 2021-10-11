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

class ExportXML extends Component
{
/*
    public static function generateXML()
    {

        $categoriesModel = GoodTypes::findAll(['id'=>GoodTypes::USUALLY_CATEGORY]);
        $goodsModel = Good::findAll(['type_id'=>GoodTypes::USUALLY_CATEGORY]);


        $rootXml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><!DOCTYPE dc_catalog SYSTEM "http://www.delivery-club.ru/xml/dc.dtd"><dc_catalog last_update="'.date('Y-m-d H:i',time()).'"></dc_catalog>');


        $delivery_service = $rootXml->addChild('delivery_service');


        $categories = $delivery_service->addChild('categories');

        foreach ($categoriesModel as $item) {
            $category = $categories->addChild('category', $item->name);
            $category->addAttribute('id', $item->id);

        }

        $products = $delivery_service->addChild('products');

        foreach ($goodsModel as $item) {


            $product = $products->addChild('product');

            $product->addAttribute('id', $item->id);

            $product->addChild('category_id', $item->type_id);

            if (in_array($item->type_id , [9,10,13])){

                $product->addChild('name',  mb_substr($item->type->name, 0, mb_strlen ($item->type->name)-1) . ' ' . $item->name);

            }elseif (in_array($item->type_id , [12,8,11])){
                $product->addChild('name',  $item->type->name . ' ' . $item->name);

            }else{
                $product->addChild('name', $item->name);
            }

            if (!empty($item->description)){
                $product->addChild('description', $item->description);

            }


            $product->addChild('price', $item->price);
            $product->addChild('picture', Url::home(true).$item->getImage()->getPath('1200x900'));
            $product->addChild('weight', $item->weight);

        }
       return $rootXml->asXML();
    }
*/

    public static function generateSitemap()
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');


        /*main page*/
        $url = $dom->createElement('url');
        $loc = $dom->createElement('loc');
        $locText = $dom->createTextNode(
            htmlentities(Yii::$app->request->hostInfo . "/", ENT_QUOTES)
        );

        $loc->appendChild($locText);
        $url->appendChild($loc);


        $urlset->appendChild($url);
        /*main page*/




        /*PAGES*/
        $pages = Page::find()->where(['is_delete' => 0, 'visibility' => 1])->all();

        foreach ($pages as $page) {

            $url = $dom->createElement('url');
            $loc = $dom->createElement('loc');
            $locText = $dom->createTextNode(
                htmlentities(Yii::$app->request->hostInfo . "/" . $page->slug, ENT_QUOTES)
            );

            $loc->appendChild($locText);
            $url->appendChild($loc);


            $urlset->appendChild($url);
        }


        /*CATEGORY*/
        $pages = Category::find()->all();

        foreach ($pages as $page) {
            $url = $dom->createElement('url');
            $loc = $dom->createElement('loc');
            $locText = $dom->createTextNode(
                htmlentities(Yii::$app->request->hostInfo . "/" . $page->slug, ENT_QUOTES)
            );

            $loc->appendChild($locText);
            $url->appendChild($loc);


            $urlset->appendChild($url);
        }

        /*ARTICLE*/
        $pages = Article::find()->where(['is_delete' => 0, 'visibility' => 1])->all();

        foreach ($pages as $page) {
            $url = $dom->createElement('url');
            $loc = $dom->createElement('loc');
            $locText = $dom->createTextNode(
                htmlentities(Yii::$app->request->hostInfo . "/article/" . $page->slug, ENT_QUOTES)
            );

            $loc->appendChild($locText);
            $url->appendChild($loc);


            $urlset->appendChild($url);
        }


        /*GOOD*/
        $pages = \common\models\Good::find()->where(['is_delete' => 0])->all();

        foreach ($pages as $page) {
            $url = $dom->createElement('url');
            $loc = $dom->createElement('loc');
            $locText = $dom->createTextNode(
                htmlentities(Yii::$app->request->hostInfo . "/good/" . $page->slug, ENT_QUOTES)
            );

            $loc->appendChild($locText);
            $url->appendChild($loc);


            $urlset->appendChild($url);
        }


        $dom->appendChild($urlset);

        return $dom->saveXML();


    }

    public static function generateTurboContent()
    {
        $start = '<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:yandex="http://news.yandex.ru"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:turbo="http://turbo.yandex.ru"
     version="2.0">
    <channel>
        <!-- Информация о сайте-источнике -->
        <title>Evvkaa</title>
        <link>https://evkka.ru/</link>
        <description>Краткое описание канала</description>
        <language>ru</language>
        <turbo:analytics></turbo:analytics>
        <turbo:adNetwork></turbo:adNetwork>';


        $end = '  </channel>
</rss>';


        $content = '';

        foreach (Article::find()->all() as $article){
            $content .= '
			<item turbo="true">		
				<link>https://evkka.ru/article/' . $article->slug . '</link>
				<turbo:content>
					<![CDATA[';



            $headerContent = Yii::$app->view->renderFile('../../frontend/views/layouts/_header.php');

            $content .= $headerContent;

            $content .= $article->content;






		    $content .= ']]>
				</turbo:content>			
			</item>\'';




        }

        return $start . $content . $end;
    }

    public static function generateTurboShop()
    {
       $start = '<?xml version="1.0" encoding="UTF-8"?>
<yml_catalog date="2019-11-01 17:22">
    <shop>
        <name>BestSeller</name>
        <company>Tne Best inc.</company>
        <url>http://best.seller.ru</url>
        <platform>uCoz</platform>
        <version>1.0</version>
        <agency>Технологичные решения</agency>
        <email>example-email@gmail.com</email>
        <currencies>
            <currency id="RUR" rate="1"/>
            <currency id="USD" rate="60"/>
        </currencies>
        <categories>
            <category id="1">Бытовая техника</category>
            <category id="10" parentId="1">Мелкая техника для кухни</category>
            <category id="101" parentId="10">Сэндвичницы и приборы для выпечки</category>
        </categories>
        <delivery-options>
            <option cost="200" days="1"/>
        </delivery-options>
        <pickup-options>
            <option cost="200" days="1"/>
        </pickup-options>
        <offers>';





        $content = '<offer id="9012" bid="80">
                <name>Мороженица Brand 3811</name>
                <vendor>Brand</vendor>
                <vendorCode>A1234567B</vendorCode>
                <url>http://best.seller.ru/product_page.asp?pid=12345</url>
                <price>8990</price>
                <oldprice>9990</oldprice>
                <enable_auto_discounts>true</enable_auto_discounts>
                <currencyId>RUR</currencyId>
                <categoryId>101</categoryId>
                <vat>VAT_20</vat>
                <picture>http://best.seller.ru/img/model_12345.jpg</picture>
                <delivery>true</delivery>
                <pickup>true</pickup>
                <delivery-options>
                    <option cost="300" days="1" order-before="18"/>
                </delivery-options>
                <pickup-options>
                    <option cost="300" days="1-3"/>                        
                </pickup-options>
                <store>true</store>
                <description>
                    <![CDATA[          
                        <h3>Мороженица Brand 3811</h3>
                        <p>Это прибор, который придётся по вкусу всем любителям десертов и сладостей, ведь с его помощью вы сможете делать вкусное домашнее мороженое из натуральных ингредиентов.</p>
                ]]>
                </description>                
                <sales_notes>Необходима предоплата.</sales_notes>
                <manufacturer_warranty>true</manufacturer_warranty>
                <country_of_origin>Китай</country_of_origin>
                <barcode>4601546021298</barcode>
                <param name="Цвет">белый</param>
                <condition type="likenew">
                    <reason>Повреждена упаковка</reason>
                </condition>                
                <credit-template id="20034"/>
                <weight>3.6</weight>
                <dimensions>20.1/20.551/22.5</dimensions>
                <count>23</count>
                </offer>';



















       $end = '       <gifts>
            <gift id="33">
                <name>Кружка 300 мл Brand 16</name>
                <picture>https://best.seller.ru/promos/33.jpg</picture>
            </gift>            
        </gifts>
        <promos>
            <promo id="PromoGift" type="gift with purchase">
                <start-date>2020-02-01 09:00:00</start-date>
                <end-date>2020-03-01 22:00:00</end-date>
                <description>Купите бытовую технику марки Brand и получите кружку в подарок.</description>
                <url>http://best.seller.ru/promos/gift</url>
                <purchase>
                    <product offer-id="9012"/>
                    <product offer-id="12346"/>
                </purchase>
                <promo-gifts>
                    <promo-gift offer-id="9012"/>
                    <promo-gift gift-id="33"/>
                </promo-gifts>
            </promo>
        </promos>
    </shop>
</yml_catalog>';


       return $start . $content . $end;
    }

}