<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Panther\Client;

class ScrapePrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:alibaba';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
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
            // Scraping the product title
            $title = $crawler->filter('h1')->text();
    
            // Scraping the price
            $price = $crawler->filter('div.price')->text();
    
            $this->info("Product Title: " . $title);
            $this->info("Price: " . $price);
        } catch (\Exception $e) {
            $this->error("Failed to scrape Alibaba: " . $e->getMessage());
        }
    
        $client->quit();
    }
}
