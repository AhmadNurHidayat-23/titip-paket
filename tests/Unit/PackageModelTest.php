<?php

namespace Tests\Unit;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PackageModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_status_helpers_and_scopes(): void
    {
        $user = User::factory()->create();

        $receivedPackage = Package::create([
            'slug' => (string) Str::uuid(),
            'recipient' => 'Budi Santoso',
            'unit' => 'A-102',
            'courier' => 'JNE',
            'tracking' => 'JNE12345678',
            'photo_path' => 'packages/test1.jpg',
            'status' => 'received',
            'created_by' => $user->id,
        ]);

        $collectedPackage = Package::create([
            'slug' => (string) Str::uuid(),
            'recipient' => 'Siti Rahma',
            'unit' => 'B-205',
            'courier' => 'SiCepat',
            'tracking' => 'SCP87654321',
            'photo_path' => 'packages/test2.jpg',
            'status' => 'collected',
            'collected_by' => 'Siti Rahma',
            'collected_at' => now(),
            'created_by' => $user->id,
        ]);

        $this->assertTrue($receivedPackage->isReceived());
        $this->assertFalse($receivedPackage->isCollected());

        $this->assertTrue($collectedPackage->isCollected());
        $this->assertFalse($collectedPackage->isReceived());

        $this->assertCount(1, Package::received()->get());
        $this->assertCount(1, Package::collected()->get());
        $this->assertTrue(Package::received()->first()->is($receivedPackage));
        $this->assertTrue(Package::collected()->first()->is($collectedPackage));
    }
}
