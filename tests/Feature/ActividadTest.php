<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Actividad;
use App\Models\Area;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActividadTest extends TestCase
{
    use DatabaseTransactions;

    protected $area;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        
        // Ensure there is at least one Area in DB to reference
        $this->area = Area::first() ?? Area::create(['nombre' => 'Sistemas']);
    }

    public function test_can_create_activity_without_priority(): void
    {
        $user = User::factory()->create(['rol' => 'jefe']);
        $this->actingAs($user);

        // Send a request to store activity with tiene_prioridad = false
        $response = $this->post(route('actividades.store'), [
            'titulo' => 'Actividad de Prueba Sin Prioridad',
            'descripcion' => 'Prueba unitaria de prioridad opcional.',
            'empleado_id' => $user->id,
            'tiene_prioridad' => 'no', // unchecked
            'prioridad' => 'media', // should be ignored and stored as null
            'tiene_plazo' => 'si',
            'modalidad' => 'un_dia',
            'area_id' => $this->area->id,
            'fecha_inicio' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('actividades', [
            'titulo' => 'Actividad de Prueba Sin Prioridad',
            'prioridad' => null,
        ]);
    }

    public function test_can_create_activity_with_priority(): void
    {
        $user = User::factory()->create(['rol' => 'jefe']);
        $this->actingAs($user);

        $response = $this->post(route('actividades.store'), [
            'titulo' => 'Actividad de Prueba Con Prioridad',
            'descripcion' => 'Prueba unitaria de prioridad urgente.',
            'empleado_id' => $user->id,
            'tiene_prioridad' => 'si', // checked
            'prioridad' => 'urgente',
            'tiene_plazo' => 'si',
            'modalidad' => 'un_dia',
            'area_id' => $this->area->id,
            'fecha_inicio' => now()->toDateString(),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('actividades', [
            'titulo' => 'Actividad de Prueba Con Prioridad',
            'prioridad' => 'urgente',
        ]);
    }

    public function test_can_reschedule_activity_in_follow_up(): void
    {
        $user = User::factory()->create(['rol' => 'jefe']);
        $this->actingAs($user);

        $actividad = Actividad::create([
            'titulo' => 'Actividad a Dar Seguimiento',
            'descripcion' => 'Prueba de reprogramacion.',
            'empleado_id' => $user->id,
            'jefe_id' => $user->id,
            'estado' => 'pendiente',
            'area_id' => $this->area->id,
            'tiempo_estimado' => 'Por definir',
            'fecha_inicio' => now()->toDateString(),
        ]);

        $nuevaFecha = now()->addDays(2)->toDateString();

        $response = $this->post(route('actividades.darSeguimiento', $actividad->id), [
            'fecha_seguimiento' => $nuevaFecha,
            'comentario' => 'Nota de seguimiento de prueba.',
            'tipo_actividad' => 'asignada',
        ]);

        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('actividades', [
            'id' => $actividad->id,
            'en_seguimiento' => 1,
            'fecha_seguimiento' => $nuevaFecha,
        ]);

        $this->assertDatabaseHas('avances_actividad', [
            'actividad_id' => $actividad->id,
            'comentario' => "📅 [SEGUIMIENTO] Se programó continuar la actividad el día " . \Carbon\Carbon::parse($nuevaFecha)->format('d/m/Y') . ". Motivo: Nota de seguimiento de prueba.",
        ]);
    }
}
