<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\Panther\Client;

class ScrapeController extends Controller
{
    public function handle()
    {
        $client = Client::createChromeClient(null, null, [
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ]);
    
        // Target URL
        $url = 'https://www.alibaba.com/product-detail/Practical-Fashion-Waterproof-Cow-Leather-Handbag_1601011201470.html';
    
        $crawler = $client->request('GET', $url);
    
        $client->waitFor('h1');
        $client->waitFor('div.price');
    
        try {
            $title = $crawler->filter('h1')->text();
    
            $price = $crawler->filter('div.price')->text();
    
            $product = Product::create([
                'title' => $title,
                'price' => $price,
                'url' => $url, 
            ]);

            return response()->json([
                'message' => 'Product scraped and saved successfully!',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to scrape Alibaba: ' . $e->getMessage(),
            ], 500);
        } finally {
            $client->quit();
        }
    }
}