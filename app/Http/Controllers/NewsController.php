<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    private const FEED_URL = 'https://cointelegraph.com/rss';
    private const CACHE_KEY = 'dashboard_news_cointelegraph';

    public function index()
    {
        $news = $this->cachedNews();

        return view('dashboard.user.news', compact('news'));
    }

    /**
     * News from cache when available; otherwise fetch and cache it for 15 minutes.
     * An empty result is not cached, so the next visit tries the feed again.
     */
    private function cachedNews(): array
    {
        try {
            $news = Cache::get(self::CACHE_KEY);
            if (is_array($news) && !empty($news)) {
                return $news;
            }
        } catch (\Throwable $e) {
            Log::warning('News cache unavailable', ['message' => $e->getMessage()]);
        }

        $news = $this->fetchNews();

        if (!empty($news)) {
            try {
                Cache::put(self::CACHE_KEY, $news, now()->addMinutes(15));
            } catch (\Throwable $e) {
                Log::warning('News cache write failed', ['message' => $e->getMessage()]);
            }
        }

        return $news;
    }

    /**
     * Fetch and parse the RSS feed. Never throws: any failure is logged and
     * an empty list is returned so the page still renders.
     */
    private function fetchNews(): array
    {
        try {
            if (!function_exists('simplexml_load_string')) {
                Log::error('News feed: the PHP SimpleXML extension is not enabled on this server.');
                return [];
            }

            // Short timeouts so a slow or blocked feed can never hit PHP's max_execution_time.
            $response = Http::timeout(8)
                ->connectTimeout(5)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; NewsReader/1.0)'])
                ->get(self::FEED_URL);

            if (!$response->successful()) {
                Log::warning('News feed request failed', ['status' => $response->status()]);
                return [];
            }

            $xmlString = $response->body();
            if (empty($xmlString)) {
                return [];
            }

            $previous = libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOCDATA);
            libxml_clear_errors();
            libxml_use_internal_errors($previous);

            if ($xml === false || !isset($xml->channel->item)) {
                Log::warning('News feed returned something that is not valid RSS');
                return [];
            }

            $news = [];
            foreach ($xml->channel->item as $item) {
                $image = null;
                if (isset($item->enclosure) && isset($item->enclosure['url'])) {
                    $image = (string) $item->enclosure['url'];
                }

                $news[] = [
                    'title' => (string) $item->title,
                    'body' => (string) $item->description,
                    'url' => (string) $item->guid ?: (string) $item->link,
                    'imageurl' => $image,
                    'published_on' => strtotime((string) $item->pubDate) ?: time(),
                ];
            }

            return $news;
        } catch (\Throwable $e) {
            Log::error('News feed error', ['message' => $e->getMessage()]);
            return [];
        }
    }
}
