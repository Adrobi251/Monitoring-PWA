<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaTest extends TestCase
{
    public function test_manifest_is_valid_and_complete(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertIsArray($manifest);

        foreach (['name', 'short_name', 'description', 'start_url', 'scope', 'display', 'background_color', 'theme_color', 'icons'] as $key) {
            $this->assertArrayHasKey($key, $manifest);
        }

        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('/dashboard', $manifest['start_url']);
        $this->assertSame('/', $manifest['scope']);

        $sizes = array_column($manifest['icons'], 'sizes');
        $this->assertContains('192x192', $sizes);
        $this->assertContains('512x512', $sizes);

        foreach ($manifest['icons'] as $icon) {
            $file = public_path(ltrim($icon['src'], '/'));

            $this->assertFileExists($file);
            [$width, $height] = getimagesize($file);
            $this->assertSame($icon['sizes'], $width.'x'.$height, $icon['src']);
        }
    }

    public function test_service_worker_caches_only_public_assets(): void
    {
        $sw = file_get_contents(public_path('service-worker.js'));

        $this->assertStringContainsString('konsultasi-pwa-v', $sw);
        $this->assertStringContainsString("request.method !== 'GET'", $sw);
        $this->assertStringContainsString("request.mode === 'navigate'", $sw);
        $this->assertStringContainsString("caches.match('/offline.html')", $sw);

        foreach (['/dashboard', '/profile', '/notifications', '/mahasiswa', '/dosen'] as $privatePath) {
            $this->assertStringNotContainsString("'".$privatePath."'", $sw, "Path privat tidak boleh masuk precache: {$privatePath}");
        }
    }

    public function test_offline_fallback_is_informative(): void
    {
        $html = file_get_contents(public_path('offline.html'));

        $this->assertStringContainsString('Tidak ada koneksi internet', $html);
        $this->assertStringContainsString('Coba Lagi', $html);
        $this->assertStringContainsString("addEventListener('online'", $html);
    }

    public function test_layouts_and_js_register_pwa_support(): void
    {
        foreach (['layouts/app.blade.php', 'layouts/guest.blade.php', 'welcome.blade.php'] as $view) {
            $contents = file_get_contents(resource_path('views/'.$view));

            $this->assertStringContainsString('rel="manifest"', $contents, $view);
            $this->assertStringContainsString('apple-touch-icon', $contents, $view);
            $this->assertStringContainsString('<x-offline-banner', $contents, $view);
        }

        $js = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("serviceWorker.register('/service-worker.js')", $js);
        $this->assertStringContainsString('isSecureContext', $js);
        $this->assertStringContainsString('[data-offline-banner]', $js);
    }

    public function test_manifest_is_served_with_manifest_mime_type(): void
    {
        $htaccess = file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('AddType application/manifest+json .webmanifest', $htaccess);
    }
}
