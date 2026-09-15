<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * Product help guide for staff, rendered from the markdown files the docs
 * team keeps in docs/help/. Updating the guide is a markdown commit; nothing
 * lives in the database.
 *
 * Contract with docs/help/:
 *   - README.md        landing intro, rendered above the list of guides
 *   - NN-slug.md       one guide per product, ordered by the numeric prefix;
 *                      the title is the first "# " line and the summary is the
 *                      first paragraph after it
 *   - URL slug         filename without prefix and extension (01-uae-evisa.md
 *                      is /manager/help/uae-evisa)
 */
class ManagerHelpController extends Controller
{
    private const FILE_PATTERN = '/^(\d{1,3})-([a-z0-9]+(?:-[a-z0-9]+)*)\.md$/';

    public function index(): View
    {
        $guides = $this->guides();
        $intro = $this->readme();

        return view('manager.help.index', [
            'guides' => $guides,
            'introHtml' => $intro !== null ? $this->render($intro) : null,
        ]);
    }

    public function show(string $slug): View
    {
        $guides = $this->guides();
        $index = $guides->search(fn (array $guide) => $guide['slug'] === $slug);
        abort_if($index === false, 404);

        $guide = $guides[$index];
        $markdown = (string) file_get_contents($guide['path']);

        return view('manager.help.show', [
            'guide' => $guide,
            'html' => $this->render($markdown),
            'previous' => $guides[$index - 1] ?? null,
            'next' => $guides[$index + 1] ?? null,
        ]);
    }

    /**
     * Every NN-slug.md in docs/help, ordered by the numeric prefix. Paths are
     * always built from the directory listing, never from the request, so a
     * slug can only ever resolve to a file that is actually in the folder.
     *
     * @return Collection<int, array{slug:string, order:int, title:string, summary:string, path:string}>
     */
    private function guides(): Collection
    {
        $directory = $this->directory();
        if (!is_dir($directory)) {
            return collect();
        }

        return collect(scandir($directory) ?: [])
            ->filter(fn (string $file) => preg_match(self::FILE_PATTERN, $file) === 1)
            ->map(function (string $file) use ($directory) {
                preg_match(self::FILE_PATTERN, $file, $match);
                $path = $directory . DIRECTORY_SEPARATOR . $file;
                [$title, $summary] = $this->titleAndSummary((string) file_get_contents($path));

                return [
                    'slug' => $match[2],
                    'order' => (int) $match[1],
                    'title' => $title ?: Str::headline($match[2]),
                    'summary' => $summary,
                    'path' => $path,
                ];
            })
            ->sortBy([['order', 'asc'], ['slug', 'asc']])
            ->values();
    }

    private function readme(): ?string
    {
        $path = $this->directory() . DIRECTORY_SEPARATOR . 'README.md';

        return is_file($path) ? (string) file_get_contents($path) : null;
    }

    private function directory(): string
    {
        return base_path('docs/help');
    }

    /**
     * First "# " line is the title; the first non-empty, non-heading block
     * after it is the summary (joined into one line, markdown emphasis
     * stripped so it reads cleanly in a card).
     *
     * @return array{0:string, 1:string}
     */
    private function titleAndSummary(string $markdown): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $markdown) ?: [];
        $title = '';
        $summary = [];
        $seenTitle = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (!$seenTitle) {
                if (preg_match('/^#\s+(.+?)\s*#*$/', $trimmed, $match)) {
                    $title = $match[1];
                    $seenTitle = true;
                }
                continue;
            }

            if ($trimmed === '') {
                if ($summary) {
                    break;
                }
                continue;
            }

            // Skip sub-headings, front-matter style rules, and list/table lines
            // until real prose starts.
            if ($summary === [] && preg_match('/^(#{1,6}\s|---|\||[-*+]\s|\d+\.\s|>|```)/', $trimmed)) {
                continue;
            }

            if (preg_match('/^(#{1,6}\s|```)/', $trimmed)) {
                break;
            }

            $summary[] = $trimmed;
        }

        $summaryText = trim(preg_replace('/[*_`]+/', '', implode(' ', $summary)) ?? '');
        $summaryText = preg_replace('/\[([^\]]+)\]\([^)]*\)/', '$1', $summaryText) ?? $summaryText;

        return [$title, Str::limit($summaryText, 220)];
    }

    /**
     * GitHub-flavoured markdown (tables, task lists, strikethrough, autolinks)
     * on top of CommonMark. Unsafe links are dropped and raw HTML is escaped:
     * the guides are plain markdown, so nothing is lost, and a block of client
     * supplied HTML pasted into a guide can never execute in the portal.
     */
    private function render(string $markdown): string
    {
        $environment = new Environment([
            'allow_unsafe_links' => false,
            'html_input' => 'escape',
            'max_nesting_level' => 20,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        return (string) (new MarkdownConverter($environment))->convert($markdown);
    }
}
