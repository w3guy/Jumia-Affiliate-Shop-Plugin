<?php

namespace Jumia;

use Symfony\Component\DomCrawler\Crawler;


set_time_limit(0);

class Scrapper
{

    private $shop;

    public function __construct($shop_url)
    {
        $this->shop = $shop_url;
        $db_data = ju_db_data();

        if (!defined('JU_AFF_LINK')) {
            define('JU_AFF_LINK', $db_data['aff_url']);
        }
    }


    /**
     * Domcrwaler
     *
     * @param string $cat product WooCommerce product category.
     *
     * @return array
     */
    public function crawl_shop($cat)
    {
        if (!ju_is_licence_active()) return;
        $request = wp_remote_get($this->shop);
        $response = wp_remote_retrieve_body($request);

        // instantiate DomCrawler
        $crawlerObject = new Crawler($response);

        /** @var array $crawler list of products */
        $crawler = $crawlerObject->filter('div.sku')->each(function (Crawler $node, $i) {
            return $node->html();
        });


        foreach ($crawler as $content) {

            try {
                $obj = new Crawler($content);
                $brand = $obj->filter('h2.title span.brand')->text();
                $spec = $obj->filter('h2.title span.name')->text();

                $title = $this->remove_non_printable_characters(trim($brand) . ' ' . trim($spec));

                $product_link = $obj->filter('a')->attr('href');

                $old_price = str_replace(',', '', $obj->filter('span.price-box > span.price + span.price > span[data-price]')->text());
                $new_price = str_replace(',', '', $obj->filter('span.price-box > span.price > span[data-price]')->text());

                $description = $this->get_description_details($product_link)[0];

                // the main product image.
                $image = $this->get_feature_image($product_link);

                $details = $this->get_description_details($product_link)[1];


                $product = array(
                    'title' => trim($title),
                    'image_url' => trim($image),
                    'product_url' => JU_AFF_LINK . trim($product_link),
                    'regular_price' => trim($old_price),
                    'sale_price' => trim($new_price),
                    'description' => trim($description),
                    'detail' => trim($details)
                );

                // insert product to DB
                Woo_Product::insert($product, $cat);

            } catch (\InvalidArgumentException $e) {
                // do nothing
            }
        }
    }


    /**
     * Product description and details
     *
     * @param string $product_link
     *
     * @return array
     */
    public function get_description_details($product_link)
    {
        $request = wp_remote_get($product_link);
        $response = wp_remote_retrieve_body($request);
        $obj = new Crawler($response);

        return array_map('trim', array(
            $obj->filter('div.product-description')->html(),
            $obj->filter('div#product-details div.list')->html()
        ));
    }

    /**
     * Product feature image.
     *
     * @param string $product_link
     *
     * @return string
     */
    public function get_feature_image($product_link)
    {
        $request = wp_remote_get($product_link);
        $response = wp_remote_retrieve_body($request);
        $obj = new Crawler($response);

        return $obj->filter('div.product-preview > img#productImage')->attr('data-src');
    }

    /**
     * Remove non printable characters fro string
     *
     * @param $string
     *
     * @return mixed
     */
    public function remove_non_printable_characters($string)
    {
        // strip unicode out of string
        $output = preg_replace('/[\x00-\x1F\x80-\xFF]/', ' ', $string);

        // ensure no double space exist
        return preg_replace('/\s+/', ' ', $output);
    }
}