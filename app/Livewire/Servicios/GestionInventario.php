<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use Livewire\WithPagination; // Importar el trait de paginación
use App\Models\Servicios\Inventario; // Importar el modelo Inventario
use App\Models\Servicios\Equipo;     // Importar el modelo Equipo
use App\Models\Servicios\Area;       // Importar el modelo Area
use App\Models\Servicios\Garantia;   // Importar el modelo Garantia
use Illuminate\Support\Facades\DB;   // Para transacciones de base de datos

class GestionInventario extends Component
{
    use WithPagination; // Habilitar la funcionalidad de paginación de Livewire

    // Define el tema de paginación a usar. 'bootstrap' es común para estilos predefinidos.
    protected $paginationTheme = 'bootstrap';
    public $search = ''; // Propiedad para el término de búsqueda en la tabla de inventario

    // --- PROPIEDADES PARA EL MODAL Y ESTADO ---
    public $isModalOpen = false; // Controla la visibilidad del modal para crear/editar
    public $isEditMode = false;  // Indica si el modal está en modo edición o creación
    public $inventarioId;        // Para almacenar el ID del inventario a editar/eliminar
    public $equipoId;            // Para almacenar el ID del equipo asociado al inventario (para edición)

    // --- PROPIEDADES DE LOS CAMPOS DEL FORMULARIO (Inventario y Equipo) ---
    // Campos del modelo Equipo
    public $nombre;                     // Nombre del equipo
    public $marca;                      // Marca del equipo
    public $modelo;                     // Modelo del equipo
    public $frecuencia_mantenimiento = 6; // Frecuencia de mantenimiento (valor por defecto)

    // Campos del modelo Inventario
    public $num_serie;                  // Número de serie
    public $num_serie_sicopa;           // Número de serie SICOPA
    public $num_serie_sia;              // Número de serie SIA
    public $pertenencia = 'propia';     // Tipo de pertenencia (valor por defecto)
    public $status = 'funcionando';     // Estado del inventario (valor por defecto)
    public $id_area_fk;                 // Clave foránea al área
    public $id_garantia_fk;             // Clave foránea a la garantía

    // --- PROPIEDADES PARA LA NUEVA GARANTÍA (cuando se selecciona 'new_warranty') ---
    public $showNewWarrantyFields = false; // Controla la visibilidad de los campos para nueva garantía
    public $nueva_garantia_status = 'activa'; // Estado por defecto de la nueva garantía
    public $nueva_garantia_fecha;         // Fecha de la nueva garantía
    public $nueva_garantia_empresa;       // Empresa de la nueva garantía
    public $nueva_garantia_contacto;      // Contacto de la nueva garantía

    /**
     * Define las reglas de validación para los campos del formulario.
     * Se ajustan dinámicamente si se está creando una nueva garantía.
     *
     * @return array
     */
    protected function rules()
    {
        $rules = [
            'nombre' => 'required|string|max:100',
            'marca' => 'nullable|string|max:100', // Marca es nullable según tu DB
            'modelo' => 'nullable|string|max:100', // Modelo es nullable según tu DB
            'frecuencia_mantenimiento' => 'required|integer',
            'num_serie' => 'nullable|string|max:50', // Num_serie es nullable según tu DB
            'num_serie_sicopa' => 'nullable|string|max:50|unique:inventarios,num_serie_sicopa,' . $this->inventarioId . ',id_inventario', // Unique con ignorar el actual
            'num_serie_sia' => 'nullable|string|max:50|unique:inventarios,num_serie_sia,' . $this->inventarioId . ',id_inventario', // Unique con ignorar el actual
            'pertenencia' => 'required|in:propia,comodato',
            'status' => 'required|in:funcionando,sin funcionar,parcialmente funcional,proceso de baja,baja',
            'id_area_fk' => 'required|exists:areas,id_area',
            'id_garantia_fk' => 'nullable|exists:garantias,id_garantia', // Puede ser null o un ID existente
        ];

        // Reglas condicionales para la nueva garantía
        if ($this->showNewWarrantyFields) {
            $rules['nueva_garantia_status'] = 'required|in:activa,terminada';
            // La fecha, empresa y contacto solo son requeridos si la nueva garantía está 'activa'
            $rules['nueva_garantia_fecha'] = 'nullable|date';
            $rules['nueva_garantia_empresa'] = 'nullable|string|max:100';
            $rules['nueva_garantia_contacto'] = 'nullable|string|max:100';

            // Si la nueva garantía es 'activa', estos campos son requeridos
            if ($this->nueva_garantia_status === 'activa') {
                $rules['nueva_garantia_fecha'] = 'required|date';
                $rules['nueva_garantia_empresa'] = 'required|string|max:100';
                $rules['nueva_garantia_contacto'] = 'required|string|max:100';
            }
        }

        return $rules;
    }

    /**
     * El método render() es el corazón de un componente Livewire.
     * Se encarga de renderizar la vista asociada al componente y pasarle los datos necesarios.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        // Obtener los registros de inventario con sus relaciones (equipo, área, garantía)
        $inventario = Inventario::with(['equipo', 'area', 'garantia'])
            // Aplicar filtro de búsqueda si $search no está vacío
            ->when($this->search, function ($query) {
                $query->whereHas('equipo', function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('marca', 'like', '%' . $this->search . '%')
                      ->orWhere('modelo', 'like', '%' . $this->search . '%');
                })
                ->orWhere('num_serie', 'like', '%' . $this->search . '%')
                ->orWhere('num_serie_sicopa', 'like', '%' . $this->search . '%')
                ->orWhere('num_serie_sia', 'like', '%' . $this->search . '%')
                ->orWhere('pertenencia', 'like', '%' . $this->search . '%')
                ->orWhere('status', 'like', '%' . $this->search . '%')
                ->orWhereHas('area', function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id_inventario', 'desc') // Ordenar por ID de inventario de forma descendente
            ->paginate(10); // Paginar los resultados

        // Obtener todas las áreas para el select del formulario
        $areas = Area::orderBy('nombre')->get();
        // Obtener todas las garantías existentes para el select del formulario
        $garantias = Garantia::all();

        return view('livewire.servicios.gestion-inventario', [
            'inventario' => $inventario,
            'areas' => $areas,
            'garantias' => $garantias,
        ]);
    }

    /**
     * Actualiza el estado de un equipo en el inventario.
     * Previene la modificación si el equipo ya está en estado 'baja'.
     *
     * @param int $inventarioId El ID del registro de inventario a actualizar.
     * @param string $nuevoStatus El nuevo estado a asignar.
     */
    public function actualizarStatus($inventarioId, $nuevoStatus)
    {
        $inventario = Inventario::find($inventarioId);
        if ($inventario) {
            // No permitir cambiar el estado si ya está en 'baja'
            if ($inventario->status === 'baja' && $nuevoStatus !== 'baja') {
                session()->flash('error', 'Este equipo ya ha sido dado de baja y su estado no puede ser modificado a otro que no sea "baja".');
                // Recargar la página para reflejar el estado actual si el usuario intentó cambiarlo
                $this->js('window.location.reload()');
                return;
            }
            // Si el nuevo estado es 'baja', verificar si hay un proceso de baja 'en proceso' activo
            if ($nuevoStatus === 'baja') {
                $procesoBajaActivo = ProcesoBaja::where('id_inventario_fk', $inventarioId)
                                                ->where('estado', 'en proceso')
                                                ->first();
                if ($procesoBajaActivo) {
                    session()->flash('error', 'Existe un proceso de baja "en proceso" para este equipo. Finalícelo o cancélelo desde la sección de Bajas.');
                    return;
                }
            }

            // Actualizar el estado del inventario
            $inventario->status = $nuevoStatus;
            $inventario->save(); // Usar save() para disparar eventos si los hubiera
            session()->flash('success', 'Estado del equipo actualizado correctamente.');
        } else {
            session()->flash('error', 'Equipo de inventario no encontrado.');
        }
    }


    /**
     * Abre el modal para crear un nuevo registro de inventario.
     * Resetea los campos y establece el modo de creación.
     */
    public function create()
    {
        $this->resetInputFields(); // Limpia todos los campos del formulario
        $this->isEditMode = false; // Establece el modo a creación
        $this->isModalOpen = true; // Abre el modal
        // Establecer fecha de garantía por defecto al crear
        $this->nueva_garantia_fecha = now()->format('Y-m-d');
    }

    /**
     * Abre el modal para editar un registro de inventario existente.
     * Carga los datos del inventario y su equipo asociado.
     *
     * @param int $id El ID del registro de inventario a editar.
     */
    public function edit($id)
    {
        $inventario = Inventario::with('equipo')->findOrFail($id); // Carga inventario y su equipo
        $this->inventarioId = $inventario->id_inventario;
        $this->equipoId = $inventario->id_equipo_fk;

        // Cargar datos del Equipo
        $this->nombre = $inventario->equipo->nombre;
        $this->marca = $inventario->equipo->marca;
        $this->modelo = $inventario->equipo->modelo;
        $this->frecuencia_mantenimiento = $inventario->equipo->frecuencia_mantenimiento;

        // Cargar datos del Inventario
        $this->num_serie = $inventario->num_serie;
        $this->num_serie_sicopa = $inventario->num_serie_sicopa;
        $this->num_serie_sia = $inventario->num_serie_sia;
        $this->pertenencia = $inventario->pertenencia;
        $this->status = $inventario->status;
        $this->id_area_fk = $inventario->id_area_fk;
        $this->id_garantia_fk = $inventario->id_garantia_fk;

        // Al editar, no se muestra el formulario de nueva garantía por defecto
        $this->showNewWarrantyFields = false;
        // Resetear campos de nueva garantía para evitar que se muestren valores residuales
        $this->reset(['nueva_garantia_status', 'nueva_garantia_fecha', 'nueva_garantia_empresa', 'nueva_garantia_contacto']);


        $this->isEditMode = true; // Establece el modo a edición
        $this->isModalOpen = true; // Abre el modal
    }

    /**
     * Guarda (crea o actualiza) un registro de inventario y su equipo asociado.
     * Maneja la creación de nuevas garantías si se selecciona esa opción.
     * Utiliza una transacción de base de datos para asegurar la atomicidad.
     */
    public function store()
    {
        $this->validate(); // Validar los datos del formulario

        // Iniciar una transacción de base de datos para asegurar atomicidad
        DB::transaction(function () {
            $garantiaId = $this->id_garantia_fk;

            // Lógica para crear una nueva garantía si se seleccionó 'new_warranty'
            if ($this->id_garantia_fk === 'new_warranty') {
                $garantia = Garantia::create([
                    'status' => $this->nueva_garantia_status,
                    'fecha_garantia' => $this->nueva_garantia_fecha, // Siempre guardar la fecha
                    'empresa' => $this->nueva_garantia_empresa,
                    'contacto' => $this->nueva_garantia_contacto,
                ]);
                $garantiaId = $garantia->id_garantia; // Usar el ID de la nueva garantía
            } else {
                // Si no es una nueva garantía, asegurarse de que sea null si no se seleccionó ninguna
                $garantiaId = empty($garantiaId) ? null : $garantiaId;
            }

            // Crear o actualizar el registro del Equipo
            $equipo = Equipo::updateOrCreate(
                ['id_equipo' => $this->equipoId], // Si equipoId es null, crea; si no, busca y actualiza
                [
                    'nombre' => $this->nombre,
                    'marca' => $this->marca,
                    'modelo' => $this->modelo,
                    'frecuencia_mantenimiento' => $this->frecuencia_mantenimiento,
                ]
            );

            // Crear o actualizar el registro del Inventario
            Inventario::updateOrCreate(
                ['id_inventario' => $this->inventarioId], // Si inventarioId es null, crea; si no, busca y actualiza
                [
                    'id_equipo_fk' => $equipo->id_equipo, // Asigna el ID del equipo (nuevo o existente)
                    'num_serie' => $this->num_serie,
                    'num_serie_sicopa' => $this->num_serie_sicopa,
                    'num_serie_sia' => $this->num_serie_sia,
                    'pertenencia' => $this->pertenencia,
                    'status' => $this->status,
                    'id_area_fk' => $this->id_area_fk,
                    'id_garantia_fk' => $garantiaId, // Asigna el ID de la garantía (nueva o existente)
                ]
            );
        });

        // Muestra un mensaje de éxito.
        session()->flash('success', $this->isEditMode ? 'Equipo actualizado en el inventario correctamente.' : 'Equipo agregado al inventario correctamente.');
        $this->closeModal(); // Cierra el modal después de guardar
    }

    /**
     * Redirige a la página de gestión de mantenimientos para registrar un nuevo mantenimiento
     * para el equipo especificado.
     *
     * @param int $inventarioId El ID del registro de inventario.
     */
    public function registrarMantenimiento($inventarioId)
    {
        return redirect()->to(route('servicios.mantenimiento') . '?equipo_id=' . $inventarioId . '&abrir_modal=true');
    }

    /**
     * Redirige a la página de gestión de bajas para registrar un nuevo proceso de baja
     * para el equipo especificado.
     *
     * @param int $inventarioId El ID del registro de inventario.
     */
    public function registrarBaja($inventarioId)
    {
        return redirect()->to(route('servicios.bajas') . '?equipo_id=' . $inventarioId . '&abrir_modal=true');
    }

    /**
     * Método que se ejecuta automáticamente cuando la propiedad $id_garantia_fk cambia.
     * Controla la visibilidad de los campos para crear una nueva garantía.
     *
     * @param string $value El valor seleccionado para id_garantia_fk.
     */
    public function updatedIdGarantiaFk($value)
    {
        $this->showNewWarrantyFields = ($value === 'new_warranty');
        // Resetear los campos de nueva garantía cada vez que se cambia la opción
        $this->reset(['nueva_garantia_status', 'nueva_garantia_fecha', 'nueva_garantia_empresa', 'nueva_garantia_contacto']);
        // Establecer fecha por defecto si se activa la nueva garantía
        if ($this->showNewWarrantyFields) {
            $this->nueva_garantia_fecha = now()->format('Y-m-d');
        }
    }

    /**
     * Cierra el modal y resetea todos los campos del formulario.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields(); // Llama al método privado para resetear
    }

    /**
     * Resetea todas las propiedades del formulario a sus valores iniciales.
     * Utiliza `reset()` sin argumentos para resetear todas las propiedades públicas del componente.
     */
    private function resetInputFields()
    {
        // Esto resetea todas las propiedades públicas del componente a sus valores iniciales/por defecto.
        $this->reset([
            'inventarioId', 'equipoId',
            'nombre', 'marca', 'modelo', 'frecuencia_mantenimiento',
            'num_serie', 'num_serie_sicopa', 'num_serie_sia',
            'pertenencia', 'status', 'id_area_fk', 'id_garantia_fk',
            'showNewWarrantyFields', 'nueva_garantia_status', 'nueva_garantia_fecha', 'nueva_garantia_empresa', 'nueva_garantia_contacto'
        ]);
        // Asegurarse de que los valores por defecto se reestablezcan correctamente.
        $this->frecuencia_mantenimiento = 6;
        $this->pertenencia = 'propia';
        $this->status = 'funcionando';
        $this->nueva_garantia_status = 'activa';
        $this->nueva_garantia_fecha = now()->format('Y-m-d'); // Fecha por defecto para nueva garantía
    }

    /**
     * Método que se ejecuta automáticamente cuando la propiedad $search cambia.
     * Resetea la paginación a la primera página.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }
}
