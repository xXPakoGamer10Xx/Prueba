<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Servicios\Mantenimiento;
use App\Models\Servicios\Inventario;
use App\Models\Servicios\EncargadoMantenimiento;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon; // Importar Carbon

class GestionMantenimiento extends Component
{
    use WithPagination;

    // Propiedades públicas
    public $search = '';
    public $showModal = false;
    public $showEncargadoModal = false;
    public $showHistorialModal = false;

    // Propiedades para el formulario de Mantenimiento
    public $id_inventario_fk;
    public $id_encargado_man_fk;
    public $fecha;
    public $tipo = 'preventivo';
    public $refacciones_material;
    public $observaciones;

    // Propiedades para el formulario de Encargado
    public $nombre_encargado;
    public $apellidos_encargado;
    public $cargo_encargado;
    public $contacto_encargado;

    // Propiedades para los filtros
    public $filtroTipo = '';
    public $filtroFechaInicio = '';
    public $filtroFechaFin = '';

    // Propiedades para el historial de equipo
    public $equipoSeleccionadoHistorial = null;

    public function mount()
    {
        $equipoId = request('equipo_id');
        $abrirModal = request('abrir_modal');

        if ($equipoId && $abrirModal) {
            $this->crearMantenimiento($equipoId);
        }
    }

    protected function rules()
    {
        return [
            'id_inventario_fk' => 'required|integer|exists:inventarios,id_inventario',
            'id_encargado_man_fk' => 'required|integer|exists:encargados_mantenimiento,id_encargado_man',
            'fecha' => 'required|date',
            'tipo' => 'required|in:preventivo,correctivo',
            'refacciones_material' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ];
    }

    public function render()
    {
        $query = Mantenimiento::with(['inventario.equipo', 'encargadoMantenimiento'])
            ->when($this->search, function ($query) {
                $query->where('tipo', 'like', '%' . $this->search . '%')
                    ->orWhereHas('inventario.equipo', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('encargadoMantenimiento', function ($q) {
                        $q->where('nombre', 'like', '%' . $this->search . '%')
                            ->orWhere('apellidos', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroFechaInicio, fn($q) => $q->where('fecha', '>=', $this->filtroFechaInicio))
            ->when($this->filtroFechaFin, fn($q) => $q->where('fecha', '<=', $this->filtroFechaFin));

        $reportes = $query->orderBy('fecha', 'desc')->paginate(10);

        $equipos_inventario = Inventario::with('equipo')
            ->whereNotIn('status', ['baja', 'proceso de baja'])
            ->get();

        $encargados = EncargadoMantenimiento::orderBy('nombre')->get();
        $stats = $this->calcularEstadisticas();

        return view('livewire.servicios.gestion-mantenimiento', [
            'reportes' => $reportes,
            'equipos_inventario' => $equipos_inventario,
            'encargados' => $encargados,
            'stats' => $stats,
        ]);
    }

    /**
     * MODIFICADO: Ahora calcula un top 5 de equipos.
     */
    private function calcularEstadisticas()
    {
        // MODIFICADO: Obtiene el top 5 de equipos con más mantenimientos.
        $equiposTopMantenimiento = Inventario::with('equipo')
            ->withCount('mantenimientos')
            ->having('mantenimientos_count', '>', 0) // Solo los que tienen al menos 1
            ->orderBy('mantenimientos_count', 'desc')
            ->limit(5) // Límite de 5
            ->get();

        $ratio = Mantenimiento::select('tipo', DB::raw('count(*) as total'))
            ->where('fecha', '>=', now()->subYear())
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $totalMesActual = Mantenimiento::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        return [
            'totalMesActual' => $totalMesActual,
            'equiposTopMantenimiento' => $equiposTopMantenimiento, // Se cambia el nombre de la variable
            'ratioPreventivo' => $ratio->get('preventivo', 0),
            'ratioCorrectivo' => $ratio->get('correctivo', 0),
        ];
    }
    
    /**
     * MODIFICADO: Se añade formato profesional y se corrige la fecha.
     */
    public function exportarCSV()
    {
        $reportesQuery = Mantenimiento::with(['inventario.equipo', 'encargadoMantenimiento'])
            ->when($this->filtroTipo, fn ($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroFechaInicio, fn ($q) => $q->where('fecha', '>=', $this->filtroFechaInicio))
            ->when($this->filtroFechaFin, fn ($q) => $q->where('fecha', '<=', $this->filtroFechaFin))
            ->orderBy('fecha', 'desc');

        $reportes = $reportesQuery->get();
        $fileName = 'Reportes_Mantenimiento_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=utf-8", // Se añade charset
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($reportes) {
            $file = fopen('php://output', 'w');
            
            // --- NUEVO: Encabezado personalizado para el hospital ---
            fputcsv($file, ['HOSPITAL MUNICIPAL DE CHICONCUAC']);
            fputcsv($file, ['Reporte de Historial de Mantenimientos']);
            fputcsv($file, ['Generado el: ' . Carbon::now('America/Mexico_City')->format('d/m/Y H:i')]);
            fputcsv($file, []); // Línea en blanco para separar

            // Encabezados de la tabla
            fputcsv($file, ['ID Reporte', 'Fecha Mantenimiento', 'Equipo', 'Num. Serie', 'Tipo', 'Encargado', 'Observaciones', 'Refacciones y Material']);

            foreach ($reportes as $reporte) {
                fputcsv($file, [
                    $reporte->id_mantenimiento,
                    Carbon::parse($reporte->fecha)->format('d/m/Y'), // CORREGIDO: Se formatea la fecha
                    $reporte->inventario->equipo->nombre ?? 'N/A',
                    $reporte->inventario->num_serie ?? 'N/A',
                    ucfirst($reporte->tipo),
                    ($reporte->encargadoMantenimiento->nombre ?? '') . ' ' . ($reporte->encargadoMantenimiento->apellidos ?? ''),
                    $reporte->observaciones ?? '',
                    $reporte->refacciones_material ?? '',
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }


    // --- El resto de funciones se mantienen sin cambios ---

    public function verHistorial($inventarioId)
    {
        $inventario = Inventario::with(['equipo', 'mantenimientos' => function ($query) {
            $query->orderBy('fecha', 'desc');
        }, 'mantenimientos.encargadoMantenimiento'])->find($inventarioId);

        if ($inventario) {
            $this->equipoSeleccionadoHistorial = [
                'nombre' => $inventario->equipo->nombre,
                'num_serie' => $inventario->num_serie,
                'mantenimientos' => $inventario->mantenimientos,
            ];
            $this->showHistorialModal = true;
        }
    }

    public function cerrarHistorialModal()
    {
        $this->showHistorialModal = false;
        $this->equipoSeleccionadoHistorial = null;
    }

    public function resetearFiltros()
    {
        $this->reset('filtroTipo', 'filtroFechaInicio', 'filtroFechaFin', 'search');
        $this->resetPage();
    }
    
    public function crearMantenimiento($equipoId = null)
    {
        $this->resetMantenimientoInput();
        if ($equipoId) {
            $this->id_inventario_fk = $equipoId;
        }
        $this->showModal = true;
    }

    public function saveMantenimiento()
    {
        $this->validate();

        Mantenimiento::create([
            'id_inventario_fk' => $this->id_inventario_fk,
            'id_encargado_man_fk' => $this->id_encargado_man_fk,
            'fecha' => $this->fecha,
            'tipo' => $this->tipo,
            'refacciones_material' => $this->refacciones_material,
            'observaciones' => $this->observaciones,
        ]);

        $this->showModal = false;
        session()->flash('success', 'Reporte de mantenimiento guardado exitosamente.');
    }

    public function crearEncargado()
    {
        $this->resetEncargadoInput();
        $this->showEncargadoModal = true;
    }

    public function saveEncargado()
    {
        $this->validate([
            'nombre_encargado' => 'required|string|max:100',
            'apellidos_encargado' => 'required|string|max:100',
            'cargo_encargado' => 'required|string|max:100',
            'contacto_encargado' => 'nullable|string|max:100',
        ]);

        EncargadoMantenimiento::create([
            'nombre' => $this->nombre_encargado,
            'apellidos' => $this->apellidos_encargado,
            'cargo' => $this->cargo_encargado,
            'contacto' => $this->contacto_encargado,
        ]);

        $this->showEncargadoModal = false;
        session()->flash('success', 'Encargado registrado exitosamente.');
    }

    public function generarPDF($reporteData)
    {
        $this->dispatch('generarPDF', $reporteData);
    }

    private function resetMantenimientoInput()
    {
        $this->resetErrorBag();
        $this->id_inventario_fk = null;
        $this->id_encargado_man_fk = null;
        $this->fecha = now()->format('Y-m-d');
        $this->tipo = 'preventivo';
        $this->refacciones_material = '';
        $this->observaciones = '';
    }

    private function resetEncargadoInput()
    {
        $this->resetErrorBag();
        $this->nombre_encargado = '';
        $this->apellidos_encargado = '';
        $this->cargo_encargado = '';
        $this->contacto_encargado = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}