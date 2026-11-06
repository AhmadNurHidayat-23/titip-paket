<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use App\Services\PackageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    private PackageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
        $this->staff = User::factory()->create();
        $this->service = new PackageService;
    }

    private function receive(): array
    {
        return $this->service->receive($this->staff, ['recipient' => 'Penerima Uji', 'unit' => 'A-01', 'courier' => 'JNE', 'tracking' => strtoupper(Str::random(12))], 'packages/parcel.jpg');
    }

    public function test_code_is_random_and_only_hash_is_stored(): void
    {
        [$p,$code] = $this->receive();
        $this->assertMatchesRegularExpression('/^TP-[A-Z0-9]{10}$/', $code);
        $this->assertSame(hash('sha256', $code), $p->pickup_hash);
        $this->assertArrayNotHasKey('pickup_hash', $p->toArray());
        $this->assertSame(1, $p->events()->count());
    }

    public function test_codes_are_unique_per_package(): void
    {
        [$a,$codeA] = $this->receive();
        [$b,$codeB] = $this->receive();
        $this->assertNotSame($codeA, $codeB);
        $this->assertNotSame($a->pickup_hash, $b->pickup_hash);
    }

    public function test_collection_consumes_code_and_records_actor(): void
    {
        [$p,$code] = $this->receive();
        $this->service->collect($this->staff, $p, $code, 'Pengambil Uji');
        $p->refresh();
        $this->assertSame('collected', $p->status);
        $this->assertNull($p->pickup_hash);
        $this->assertSame('Pengambil Uji', $p->collected_by);
        $this->assertNotNull($p->collected_at);
        $this->assertSame($this->staff->id, $p->events()->latest('id')->first()->actor_id);
    }

    public function test_code_cannot_be_reused(): void
    {
        [$p,$code] = $this->receive();
        $this->service->collect($this->staff, $p, $code, 'Pengambil');
        $this->actingAs($this->staff)->post('/paket/'.$p->slug.'/ambil', ['pickup_code' => $code, 'collector' => 'Pengambil kedua', 'identity_checked' => 1])->assertSessionHasErrors('pickup_code');
        $this->assertSame('Pengambil', $p->fresh()->collected_by);
        $this->assertSame(2, $p->events()->count());
    }

    public function test_wrong_code_does_not_change_status(): void
    {
        [$p] = $this->receive();
        $this->actingAs($this->staff)->post('/paket/'.$p->slug.'/ambil', ['pickup_code' => 'TP-WRONGCODE0', 'collector' => 'Pengambil', 'identity_checked' => 1])->assertSessionHasErrors('pickup_code');
        $this->assertSame('received', $p->fresh()->status);
        $this->assertSame(1, $p->events()->count());
    }

    public function test_identity_confirmation_required(): void
    {
        [$p,$code] = $this->receive();
        $this->actingAs($this->staff)->post('/paket/'.$p->slug.'/ambil', ['pickup_code' => $code, 'collector' => 'Pengambil'])->assertSessionHasErrors('identity_checked');
        $this->assertSame('received', $p->fresh()->status);
    }

    public function test_code_of_another_package_is_rejected(): void
    {
        [$a,$codeA] = $this->receive();
        [$b,$codeB] = $this->receive();
        $this->actingAs($this->staff)->post('/paket/'.$a->slug.'/ambil', ['pickup_code' => $codeB, 'collector' => 'Pengambil', 'identity_checked' => 1])->assertSessionHasErrors('pickup_code');
        $this->assertSame('received', $a->fresh()->status);
    }

    public function test_guests_cannot_view_records_photos_or_reports(): void
    {
        [$p] = $this->receive();
        foreach (['/', '/paket/'.$p->slug, '/paket/'.$p->slug.'/foto', '/laporan'] as $u) {
            $this->get($u)->assertRedirect('/masuk');
        }
    }

    public function test_receipt_creation_saves_private_photo_and_flash_code(): void
    {
        $response = $this->actingAs($this->staff)->post('/paket', ['recipient' => 'Penerima', 'unit' => 'B-2', 'courier' => 'JNE', 'tracking' => 'RESIUJI123', 'photo' => UploadedFile::fake()->image('paket.jpg')]);
        $response->assertRedirect()->assertSessionHas('pickup_code');
        $p = Package::first();
        Storage::disk('local')->assertExists($p->photo_path);
        $this->assertStringStartsWith('packages/', $p->photo_path);
        $this->get('/paket/'.$p->slug.'/foto')->assertOk()->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_duplicate_tracking_and_non_image_are_rejected(): void
    {
        $p = $this->receive()[0];
        $this->actingAs($this->staff)->post('/paket', ['recipient' => 'Penerima lain', 'courier' => 'JNE', 'tracking' => $p->tracking, 'photo' => UploadedFile::fake()->create('code.php', 1, 'application/x-php')])->assertSessionHasErrors(['tracking', 'photo']);
        $this->assertSame(1, Package::count());
    }

    public function test_search_and_history_use_real_records(): void
    {
        [$p] = $this->receive();
        $this->actingAs($this->staff)->get('/?q=Penerima')->assertSee($p->tracking);
        $this->get('/?status=collected')->assertDontSee($p->tracking);
        $this->get('/laporan')->assertSee($p->tracking);
        $this->get('/laporan?from=2000-01-01&to=2000-01-02')->assertDontSee($p->tracking);
    }

    public function test_report_csv_contains_both_events_without_codes(): void
    {
        [$p,$code] = $this->receive();
        $this->service->collect($this->staff, $p, $code, 'Pengambil');
        $r = $this->actingAs($this->staff)->get('/laporan?export=csv');
        $r->assertDownload('aktivitas-paket.csv');
        $text = $r->streamedContent();
        $this->assertStringContainsString('received', $text);
        $this->assertStringContainsString('collected', $text);
        $this->assertStringNotContainsString($code, $text);
        $this->assertStringNotContainsString(hash('sha256', $code), $text);
    }

    public function test_tracking_is_case_normalized_before_uniqueness_check(): void
    {
        [$p] = $this->receive();
        $this->actingAs($this->staff)->post('/paket', ['recipient' => 'Penerima', 'courier' => 'JNE', 'tracking' => strtolower($p->tracking), 'photo' => UploadedFile::fake()->image('paket.jpg')])->assertSessionHasErrors('tracking');
        $this->assertSame(1, Package::count());
    }
}
