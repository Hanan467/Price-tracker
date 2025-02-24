<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Notifications\PriceDropNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Panther\Client;

class ScrapeController extends Controller
{
    public function showForm()
    {
        return view('addItem');
    }

    public function store(Request $request)
    {
        $client = Client::createChromeClient(null, null, [
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        ]);

        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->url;
    
        $crawler = $client->request('GET', $url);
    
        $client->waitFor('h1');
        $client->waitFor('div.price');
    
        try {
            $title = $crawler->filter('h1')->text();
    
            $price = $crawler->filter('div.price')->text();
    
            Product::create([
                'user_id' => Auth::id(),
                'title' => $title,
                'price' => floatval(preg_replace('/[^\d.]/', '', $price)), 
                'url' => $url, 
            ]);

            return redirect()->back()->with('success', 'Product added successfully!');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to scrape Alibaba: ' . $e->getMessage(),
            ], 500);
        } finally {
            $client->quit();
        }
    }

     public static function checkPriceDrop()
     {
        $products = Product::all();

        $client = Client::createChromeClient();

        foreach ($products as $product) {
            try {
                $crawler = $client->request('GET', $product->url);
                $currentPrice = $crawler->filter('.price')->text();
                $currentPrice = floatval(preg_replace('/[^\d.]/', '', $currentPrice));

                if ($currentPrice < $product->price) {
                    $product->update(['price' => $currentPrice]);

                    $product->user->notify(new PriceDropNotification($product));
                }
            } catch (\Exception $e) {
                Log::error("Failed to check price for {$product->url}: {$e->getMessage()}");
            }
        }

        return response()->json(['message' => 'Price check completed']);
    }
}