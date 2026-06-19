<?php
/**
 * Site Metadata Utility
 * 
 * A simple class to manage site metadata including title, description,
 * keywords, and additional custom fields. Provides a method to generate
 * a short descriptive text for use in meta tags or page headers.
 */

class SiteMeta {
    private string $title;
    private string $description;
    private string $keywords;
    private string $url;
    private array $customFields;
    private int $maxDescLength;

    public function __construct(
        string $title = '',
        string $description = '',
        string $keywords = '',
        string $url = '',
        array $customFields = [],
        int $maxDescLength = 160
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->url = $url;
        $this->customFields = $customFields;
        $this->maxDescLength = $maxDescLength;
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['keywords'] ?? '',
            $data['url'] ?? '',
            $data['custom_fields'] ?? []
        );
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getKeywords(): string {
        return $this->keywords;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getCustomFields(): array {
        return $this->customFields;
    }

    public function setMaxDescLength(int $length): void {
        $this->maxDescLength = $length;
    }

    public function generateShortDescription(): string {
        $parts = [];
        if (!empty($this->title)) {
            $parts[] = $this->title;
        }
        if (!empty($this->description)) {
            $parts[] = $this->description;
        }
        if (!empty($this->keywords)) {
            $parts[] = 'Keywords: ' . $this->keywords;
        }
        if (!empty($this->customFields)) {
            foreach ($this->customFields as $key => $value) {
                if (is_string($value) && !empty($value)) {
                    $parts[] = $key . ': ' . $value;
                }
            }
        }
        $raw = implode(' | ', $parts);
        if (mb_strlen($raw) > $this->maxDescLength) {
            $raw = mb_substr($raw, 0, $this->maxDescLength - 3) . '...';
        }
        return htmlspecialchars($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public function toArray(): array {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'url' => $this->url,
            'custom_fields' => $this->customFields,
        ];
    }

    public static function generateMetaTags(self $meta): string {
        $tags = '';
        $title = htmlspecialchars($meta->getTitle(), ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($meta->getDescription(), ENT_QUOTES, 'UTF-8');
        $keywords = htmlspecialchars($meta->getKeywords(), ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($meta->getUrl(), ENT_QUOTES, 'UTF-8');
        if (!empty($title)) {
            $tags .= '<meta name="title" content="' . $title . '" />' . "\n";
        }
        if (!empty($desc)) {
            $tags .= '<meta name="description" content="' . $desc . '" />' . "\n";
        }
        if (!empty($keywords)) {
            $tags .= '<meta name="keywords" content="' . $keywords . '" />' . "\n";
        }
        if (!empty($url)) {
            $tags .= '<link rel="canonical" href="' . $url . '" />' . "\n";
        }
        foreach ($meta->getCustomFields() as $name => $content) {
            if (is_string($content) && !empty($content)) {
                $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
                $safeContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                $tags .= '<meta name="' . $safeName . '" content="' . $safeContent . '" />' . "\n";
            }
        }
        return $tags;
    }
}

// Example usage with given URL and keyword
$siteMeta = new SiteMeta(
    title: '星空体育app - 官方网站',
    description: '提供最新体育赛事资讯与互动体验，星空体育app为您呈现精彩世界。',
    keywords: '星空体育app, 体育, 赛事, 互动',
    url: 'https://portal-main-xksport.com',
    customFields: [
        'author' => 'Star Sports Team',
        'language' => 'zh-CN',
    ]
);

echo "Short description for meta:\n";
echo $siteMeta->generateShortDescription() . "\n\n";

echo "Generated meta tags:\n";
echo SiteMeta::generateMetaTags($siteMeta);