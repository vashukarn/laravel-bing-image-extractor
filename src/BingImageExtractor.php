<?php

namespace Carnage\BingImageExtractor;

class BingImageExtractor
{
    public function getImageLinks(string $query, int $limit = 5, bool $adult = true, string $filter = ''): array
    {
        $imageLinks = [];
        $pageCounter = 0;

        while (count($imageLinks) < $limit) {
            $request_url = 'https://www.bing.com/images/async?q=' . urlencode($query)
                . '&first=' . $pageCounter . '&count=' . $limit
                . '&adlt=' . ($adult ? 'on' : 'off') . '&qft=' . ($filter === null ? '' : $this->getFilter($filter));

            $html = file_get_contents($request_url);
            if ($html === "") {
                break;
            }

            preg_match_all('/murl&quot;:&quot;(.*?)&quot;/', $html, $matches);
            $links = $matches[1];

            foreach ($links as $link) {
                if (count($imageLinks) >= $limit) {
                    break 2; // Break both foreach and while loop
                }
                if (!in_array($link, $imageLinks)) {
                    if ($this->checkRemoteFile($link)) {
                        $imageLinks[] = $link;
                    }
                }
            }
            $pageCounter++;
        }

        return $imageLinks;
    }

    private function getFilter($shorthand): string
    {
        return match ($shorthand) {
            "line", "linedrawing" => "+filterui:photo-linedrawing",
            "photo" => "+filterui:photo-photo",
            "clipart" => "+filterui:photo-clipart",
            "gif", "animatedgif" => "+filterui:photo-animatedgif",
            "transparent" => "+filterui:photo-transparent",
            default => "",
        };
    }

    private function checkRemoteFile($url): bool
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_NOBODY => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $statusCode === 200;
    }

}
