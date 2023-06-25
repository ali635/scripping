<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Spiders\LaravelDocsSpider;
use App\Spiders\LinkDocsSpider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use RoachPHP\Downloader\Middleware\UserAgentMiddleware;
use RoachPHP\Roach;
use RoachPHP\Spider\Configuration\Overrides;
use Goutte\Client;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;



class SpiderController extends Controller
{
    // protected $spider;
    // public function __construct(LaravelDocsSpider $spider)
    // {
    //     $this->spider = $spider;
    // }

   public function scripping(Request $request)
   {
    $client = new Client();
    $visitedUrls = [];
    $products = [];

    $filename = 'C:\laragon\www\escrapping-app\app\Http\Controllers\images.csv';

    // open csv file for writing
    $f = fopen($filename, 'a');
    
    if ($f === false) {
        die('Error opening the file ' . $filename);
    }

    $crawler = $client->request('GET', 'https://khamato.com');
    $crawler->filter('.nav-wrap ul li a')->each(function ($node) use ($client, &$visitedUrls, $f) {
        $url2 = $node->link()->getUri();
        if (!in_array($url2, $visitedUrls)) {
            $visitedUrls[] = $url2;
            $productsCrawler = $client->request('GET', $url2);

            $productsCrawler->filter('.product-item a')->each(function ($node) use ($client, &$visitedUrls, $f) {
                $url = $node->link()->getUri();
                if (!in_array($url, $visitedUrls)) {
                    $visitedUrls[] = $url;
                    $productCrawler = $client->request('GET', $url);

                    $productCrawler->filter('.products')->each(function ($node) use($f) {
                        $imgSrc = $node->filter('.carousel-inner .carousel-item img')->attr('data-src');
                        $title = $node->filter('.carousel-inner .carousel-item img')->attr('alt');
                        $sku = $node->filter('.product ul li')->text();

                        $product = [
                             $imgSrc,
                             $title,
                             $sku,
                        ];

                        fputcsv($f, $product);

                        // echo "</br>" . $imgSrc . "</br>" ."-" . "</br>" . $title . "</br>" . $sku . "</br>" . PHP_EOL;
                    });
                   
                }
            });
        }
    });
    fclose($f);
    // $export = new ProductsExport($products);
    // Excel::store($export, 'products.xlsx', 'local');
   }




}
