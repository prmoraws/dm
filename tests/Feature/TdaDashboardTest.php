<?php

namespace Tests\Feature;

use App\Livewire\Universal\TdaDashboard;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TdaDashboardTest extends TestCase
{
    #[Test]
    public function componente_do_dashboard_tda_existe(): void
    {
        $this->assertTrue(class_exists(TdaDashboard::class));
    }
}
