@extends('layouts.app')

@section('title', 'Organigrama de la Empresa')

@section('content')

<!-- Cargar librería html2canvas para la descarga -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<style>
.org-header {
    background: linear-gradient(135deg, #ffffff 0%, #f3f6fb 100%);
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    padding: 40px 0 20px; /* Incrementado para evitar corte superior */
    position: relative;
    margin: -40px -12px 30px;
}
.org-header::after {
    content: "";
    position: absolute;
    bottom: 0; left: 0;
    height: 4px; width: 100%;
    background: linear-gradient(90deg, #0d6efd, #6f42c1);
}
.org-header .logo {
    position: absolute;
    top: 30px; left: 25px; /* Ajustado para alinearse con el padding nuevo */
    width: 105px;
    opacity: 0.95;
}
.org-header .header-text {
    margin-left: 140px;
    padding-top: 5px;
}
.org-header .titulo {
    font-size: 2.2rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}
.org-header .subtitulo {
    font-size: 0.85rem;
    color: #6b7280;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

/* --- Canvas y Contenedor del Organigrama Fijo y Centrado --- */
.org-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 45px 30px;
    width: 100%;
    overflow: auto;
    background-color: #fafbfc;
}

/* --- Árbol Top-Down con conectores CSS --- */
.org-tree, .org-tree ul {
    display: flex;
    justify-content: center;
    padding-top: 25px;
    position: relative;
    list-style-type: none;
    margin: 0;
}

.org-tree li {
    text-align: center;
    position: relative;
    padding: 25px 12px 0 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* --- Conectores del Nivel Superior (Línea Verde Lima) --- */
.org-tree li::before, .org-tree li::after {
    content: '';
    position: absolute;
    top: 0;
    right: 50%;
    border-top: 2.5px solid #84cc16; /* Verde Lima */
    width: 50%;
    height: 25px;
    z-index: 1;
}
.org-tree li::after {
    right: auto;
    left: 50%;
    border-left: 2.5px solid #84cc16;
}

/* Eliminar conectores superiores para elementos únicos */
.org-tree li:only-child::after, .org-tree li:only-child::before {
    display: none;
}
.org-tree li:only-child {
    padding-top: 0;
}
.org-tree li:first-child::before, .org-tree li:last-child::after {
    border: 0 none;
}
.org-tree li:last-child::before {
    border-right: 2.5px solid #84cc16;
    border-radius: 0 8px 0 0;
}
.org-tree li:first-child::after {
    border-radius: 8px 0 0 0;
}

/* Conector vertical descendente desde el padre */
.org-tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 2.5px solid #84cc16;
    width: 0;
    height: 25px;
    z-index: 1;
}

/* --- Conectores de Niveles Inferiores (Línea Azul) --- */
.org-tree ul ul::before {
    border-color: #0d6efd; /* Azul primary */
}
.org-tree ul ul li::before, .org-tree ul ul li::after {
    border-color: #0d6efd !important;
}
.org-tree ul ul li:last-child::before {
    border-right: 2.5px solid #0d6efd !important;
}

/* --- Tarjetas de Nodos --- */
.org-node {
    background-color: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 14px;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.04);
    min-width: 250px;
    max-width: 280px;
    cursor: default;
    position: relative;
    z-index: 5;
    text-align: left;
}
.org-node:hover {
    border-color: #0d6efd;
    box-shadow: 0 8px 16px rgba(13, 110, 253, 0.08);
}

.org-node-info {
    flex: 1;
}
.org-node-name {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.9rem;
    line-height: 1.2;
}
.org-node-role {
    font-size: 0.74rem;
    color: #64748b;
    display: block;
    margin-top: 2px;
    font-weight: 500;
}

.org-badge {
    font-size: 0.6rem;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 700;
    display: inline-block;
    letter-spacing: 0.5px;
    margin-top: 3px;
    text-transform: uppercase;
}
.badge-admin { background-color: #e2e8f0; color: #334155; border: 1px solid #cbd5e1; }
.badge-directivo { background-color: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.badge-jefe { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
.badge-empleado { background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.badge-practicante { background-color: #fef9c3; color: #a16207; border: 1px solid #fef08a; }

/* Resaltado de Búsqueda */
.node-highlight {
    border-color: #0d6efd !important;
    background-color: #f0f7ff !important;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2) !important;
    transform: scale(1.03);
}

/* Colapsable */
.btn-toggle-org {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    color: #64748b;
    transition: color 0.2s ease;
}
.btn-toggle-org:hover {
    color: #0d6efd;
}
.toggle-icon {
    transition: transform 0.25s ease;
    display: inline-block;
}

.org-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}
.org-avatar-inner {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
    color: #ffffff;
}
</style>

<div class="org-header">
    <img src="{{ asset('images/logo.png') }}" alt="Logo clinica" class="logo">
    <div class="header-text">
        <h1 class="titulo">Organigrama de la Empresa</h1>
        <div class="subtitulo">Distribución y Jerarquía Organizacional</div>
    </div>
    <div style="position: absolute; top: 32px; right: 25px; display: flex; gap: 10px;">
        <button onclick="downloadOrganigrama()" class="btn btn-sm btn-primary" style="padding: 6px 14px; font-size: 0.85rem; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center; border: none; background-color: #0d6efd; color: white;">
            <i class="bi bi-download me-1"></i> Descargar Organigrama
        </button>
        <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary" style="padding: 6px 14px; font-size: 0.85rem; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; background-color: white;">
            <i class="bi bi-arrow-left me-1"></i> Volver al Inicio
        </a>
    </div>
</div>

<div class="container-fluid py-2">
    <!-- Buscador -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="org-search" class="form-control border-start-0 ps-0" placeholder="Buscar por nombre, rol o área..." onkeyup="filterOrgTree()">
                    </div>
                </div>
                <div class="col-md-6 text-md-end text-muted small">
                    <span class="me-3"><i class="bi bi-circle-fill text-danger me-1"></i> Dirección</span>
                    <span class="me-3"><i class="bi bi-circle-fill text-primary me-1"></i> Jefes / Coordinadores</span>
                    <span class="me-3"><i class="bi bi-circle-fill text-success me-1"></i> Colaboradores</span>
                    <span><i class="bi bi-circle-fill text-warning me-1"></i> Practicantes / SS</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Canvas del Organigrama -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #fafbfc; min-height: 580px; position: relative; overflow: hidden;">
        <div class="card-body p-0">
            @php
                $directivos = $usersForOrganigrama->filter(fn($u) => $u->rol === 'directivo')->sortBy('name');
                $jefes = $usersForOrganigrama->filter(fn($u) => $u->rol === 'jefe')->sortBy('name');

                $gradients = [
                    'directivo' => 'linear-gradient(135deg, #ef4444, #f87171)',  // Rojo
                    'jefe' => 'linear-gradient(135deg, #3b82f6, #60a5fa)',       // Azul
                    'empleado' => 'linear-gradient(135deg, #10b981, #34d399)',   // Verde
                    'practicante' => 'linear-gradient(135deg, #f59e0b, #fbbf24)', // Amarillo
                ];

                $roleLabels = [
                    'directivo' => 'DIRECCIÓN',
                    'jefe' => 'JEFE / COORDINADOR',
                    'empleado' => 'COLABORADOR',
                    'practicante' => 'PRACTICANTE / SS',
                ];
            @endphp

            @if($usersForOrganigrama->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="bi bi-people-fill display-1 text-light"></i>
                    <p class="mt-3">No hay usuarios activos registrados.</p>
                </div>
            @else
                <div class="org-container" id="capture-container">
                    <ul class="org-tree-root org-tree">
                        <!-- Nodo Raíz: Clínica Euromédica -->
                        <li>
                            <div class="org-node" style="border-left: 4px solid #64748b;" id="node_company">
                                <div class="org-avatar">
                                    <div class="org-avatar-inner" style="background: linear-gradient(135deg, #64748b, #475569)">
                                        🏥
                                    </div>
                                </div>
                                <div class="org-node-info">
                                    <div class="org-node-name d-flex align-items-center justify-content-between">
                                        <span>Clínica Euromédica</span>
                                        <button class="btn-toggle-org" onclick="toggleOrgNode(this, 'sub_list_company')">
                                            <i class="bi bi-caret-right-fill toggle-icon" style="transform: rotate(90deg);"></i>
                                        </button>
                                    </div>
                                    <span class="org-badge badge-admin">Corporativo</span>
                                    <span class="org-node-role">Matriz</span>
                                </div>
                            </div>

                            <!-- Subnivel 1: Directivos -->
                            <ul id="sub_list_company">
                                @if($directivos->isNotEmpty())
                                    @foreach($directivos as $dir)
                                        @php
                                            $isFirstDir = $loop->first;
                                            $jefesBajoDirectivo = $jefes->filter(function($u) use ($dir, $isFirstDir) {
                                                return $u->jefe_id === $dir->id || ($isFirstDir && is_null($u->jefe_id));
                                            })->sortBy('name');

                                            $empleadosSinJefe = $isFirstDir
                                                ? $usersForOrganigrama->filter(fn($u) => in_array($u->rol, ['empleado', 'practicante']) && is_null($u->jefe_id))->sortBy('name')
                                                : collect();
                                        @endphp
                                        <li class="org-item" data-search="{{ strtolower($dir->name) }} {{ strtolower($dir->rol) }} {{ strtolower($dir->departamento) }}">
                                            <div class="org-node" style="border-left: 4px solid #dc3545;" id="node_{{ $dir->id }}">
                                                <div class="org-avatar">
                                                    <div class="org-avatar-inner" style="background: {{ $gradients[$dir->rol] }}">
                                                        {{ strtoupper(substr($dir->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="org-node-info">
                                                    <div class="org-node-name d-flex align-items-center justify-content-between">
                                                        <span>{{ $dir->name }}</span>
                                                        @if($jefesBajoDirectivo->isNotEmpty() || $empleadosSinJefe->isNotEmpty())
                                                            <button class="btn-toggle-org" onclick="toggleOrgNode(this, 'sub_list_{{ $dir->id }}')">
                                                                <i class="bi bi-caret-right-fill toggle-icon" style="transform: rotate(90deg);"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <span class="org-badge badge-directivo">{{ $roleLabels[$dir->rol] }}</span>
                                                    <span class="org-node-role">{{ $dir->departamento }}</span>
                                                </div>
                                            </div>

                                            <!-- Subnivel 2: Jefes -->
                                            @if($jefesBajoDirectivo->isNotEmpty() || $empleadosSinJefe->isNotEmpty())
                                                <ul id="sub_list_{{ $dir->id }}">
                                                    
                                                    @foreach($jefesBajoDirectivo as $jefe)
                                                        @php
                                                            $subordinadosDeJefe = $usersForOrganigrama->filter(fn($u) => $u->jefe_id === $jefe->id)->sortBy('name');
                                                        @endphp
                                                        <li class="org-item" data-search="{{ strtolower($jefe->name) }} {{ strtolower($jefe->rol) }} {{ strtolower($jefe->departamento) }}">
                                                            <div class="org-node" style="border-left: 4px solid #0d6efd;" id="node_{{ $jefe->id }}">
                                                                <div class="org-avatar">
                                                                    <div class="org-avatar-inner" style="background: {{ $gradients[$jefe->rol] }}">
                                                                        {{ strtoupper(substr($jefe->name, 0, 1)) }}
                                                                    </div>
                                                                </div>
                                                                <div class="org-node-info">
                                                                    <div class="org-node-name d-flex align-items-center justify-content-between">
                                                                        <span>{{ $jefe->name }}</span>
                                                                        @if($subordinadosDeJefe->isNotEmpty())
                                                                            <button class="btn-toggle-org" onclick="toggleOrgNode(this, 'sub_list_{{ $jefe->id }}')">
                                                                                <i class="bi bi-caret-right-fill toggle-icon" style="transform: rotate(90deg);"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                    <span class="org-badge badge-jefe">{{ $roleLabels[$jefe->rol] }}</span>
                                                                    <span class="org-node-role">{{ $jefe->departamento }}</span>
                                                                </div>
                                                            </div>

                                                            <!-- Subnivel 3: Colaboradores / Practicantes -->
                                                            @if($subordinadosDeJefe->isNotEmpty())
                                                                <ul id="sub_list_{{ $jefe->id }}">
                                                                    @foreach($subordinadosDeJefe as $emp)
                                                                        <li class="org-item" data-search="{{ strtolower($emp->name) }} {{ strtolower($emp->rol) }} {{ $emp->area ? strtolower($emp->area->nombre) : strtolower($emp->departamento) }}">
                                                                            <div class="org-node" style="border-left: 4px solid {{ $emp->rol === 'practicante' ? '#ffc107' : '#198754' }};" id="node_{{ $emp->id }}">
                                                                                <div class="org-avatar">
                                                                                    <div class="org-avatar-inner" style="background: {{ $gradients[$emp->rol] }}">
                                                                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="org-node-info">
                                                                                    <div class="org-node-name">{{ $emp->name }}</div>
                                                                                    <span class="org-badge badge-{{ $emp->rol }}">{{ $roleLabels[$emp->rol] }}</span>
                                                                                    <span class="org-node-role">{{ $emp->area ? $emp->area->nombre : ($emp->departamento ?? '') }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </li>
                                                    @endforeach

                                                    <!-- Empleados sin jefe directo (HASTA ABAJO de los Directivos) -->
                                                    @foreach($empleadosSinJefe as $emp)
                                                        <li class="org-item" data-search="{{ strtolower($emp->name) }} {{ strtolower($emp->rol) }} {{ $emp->area ? strtolower($emp->area->nombre) : strtolower($emp->departamento) }}">
                                                            <div class="org-node" style="border-left: 4px solid {{ $emp->rol === 'practicante' ? '#ffc107' : '#198754' }};" id="node_{{ $emp->id }}">
                                                                <div class="org-avatar">
                                                                    <div class="org-avatar-inner" style="background: {{ $gradients[$emp->rol] }}">
                                                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                                                    </div>
                                                                </div>
                                                                <div class="org-node-info">
                                                                    <div class="org-node-name">{{ $emp->name }}</div>
                                                                    <span class="org-badge badge-{{ $emp->rol }}">{{ $roleLabels[$emp->rol] }}</span>
                                                                    <span class="org-node-role">{{ $emp->area ? $emp->area->nombre : ($emp->departamento ?? '') }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach

                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach
                                @else
                                    <!-- Si no hay Directivos registrados -->
                                    @foreach($jefes->filter(fn($u) => is_null($u->jefe_id))->sortBy('name') as $jefe)
                                        @php
                                            $subordinadosDeJefe = $usersForOrganigrama->filter(fn($u) => $u->jefe_id === $jefe->id)->sortBy('name');
                                        @endphp
                                        <li class="org-item" data-search="{{ strtolower($jefe->name) }} {{ strtolower($jefe->rol) }} {{ strtolower($jefe->departamento) }}">
                                            <div class="org-node" style="border-left: 4px solid #0d6efd;" id="node_{{ $jefe->id }}">
                                                <div class="org-avatar">
                                                    <div class="org-avatar-inner" style="background: {{ $gradients[$jefe->rol] }}">
                                                        {{ strtoupper(substr($jefe->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="org-node-info">
                                                    <div class="org-node-name d-flex align-items-center justify-content-between">
                                                        <span>{{ $jefe->name }}</span>
                                                        @if($subordinadosDeJefe->isNotEmpty())
                                                            <button class="btn-toggle-org" onclick="toggleOrgNode(this, 'sub_list_{{ $jefe->id }}')">
                                                                <i class="bi bi-caret-right-fill toggle-icon" style="transform: rotate(90deg);"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <span class="org-badge badge-jefe">{{ $roleLabels[$jefe->rol] }}</span>
                                                    <span class="org-node-role">{{ $jefe->departamento }}</span>
                                                </div>
                                            </div>

                                            @if($subordinadosDeJefe->isNotEmpty())
                                                <ul id="sub_list_{{ $jefe->id }}">
                                                    @foreach($subordinadosDeJefe as $emp)
                                                        <li class="org-item" data-search="{{ strtolower($emp->name) }} {{ strtolower($emp->rol) }} {{ $emp->area ? strtolower($emp->area->nombre) : strtolower($emp->departamento) }}">
                                                            <div class="org-node" style="border-left: 4px solid {{ $emp->rol === 'practicante' ? '#ffc107' : '#198754' }};" id="node_{{ $emp->id }}">
                                                                <div class="org-avatar">
                                                                    <div class="org-avatar-inner" style="background: {{ $gradients[$emp->rol] }}">
                                                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                                                    </div>
                                                                </div>
                                                                <div class="org-node-info">
                                                                    <div class="org-node-name">{{ $emp->name }}</div>
                                                                    <span class="org-badge badge-{{ $emp->rol }}">{{ $roleLabels[$emp->rol] }}</span>
                                                                    <span class="org-node-role">{{ $emp->area ? $emp->area->nombre : ($emp->departamento ?? '') }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </li>
                                    @endforeach

                                    @foreach($usersForOrganigrama->filter(fn($u) => in_array($u->rol, ['empleado', 'practicante']) && is_null($u->jefe_id))->sortBy('name') as $emp)
                                        <li class="org-item" data-search="{{ strtolower($emp->name) }} {{ strtolower($emp->rol) }} {{ $emp->area ? strtolower($emp->area->nombre) : strtolower($emp->departamento) }}">
                                            <div class="org-node" style="border-left: 4px solid {{ $emp->rol === 'practicante' ? '#ffc107' : '#198754' }};" id="node_{{ $emp->id }}">
                                                <div class="org-avatar">
                                                    <div class="org-avatar-inner" style="background: {{ $gradients[$emp->rol] }}">
                                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div class="org-node-info">
                                                    <div class="org-node-name">{{ $emp->name }}</div>
                                                    <span class="org-badge badge-{{ $emp->rol }}">{{ $roleLabels[$emp->rol] }}</span>
                                                    <span class="org-node-role">{{ $emp->area ? $emp->area->nombre : ($emp->departamento ?? '') }}</span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleOrgNode(button, targetId) {
    const target = document.getElementById(targetId);
    if (!target) return;
    const isCollapsed = target.style.display === 'none';
    target.style.display = isCollapsed ? 'flex' : 'none';
    if (target.style.display === 'flex') {
        target.style.flexDirection = 'row';
    }
    
    // Rotar el icono
    const icon = button.querySelector('.toggle-icon');
    if (icon) {
        icon.style.transform = isCollapsed ? 'rotate(90deg)' : 'rotate(0deg)';
    }
}

function filterOrgTree() {
    const query = document.getElementById('org-search').value.toLowerCase().trim();
    const items = document.querySelectorAll('.org-item');
    const nodes = document.querySelectorAll('.org-node');

    // Limpiar resaltados previos
    nodes.forEach(node => node.classList.remove('node-highlight'));

    if (query === "") {
        items.forEach(item => {
            item.style.display = 'block';
        });
        return;
    }

    // Filtrar y resaltar coincidencias
    items.forEach(item => {
        const searchText = item.getAttribute('data-search') || "";
        const node = item.querySelector('.org-node');
        
        if (searchText.includes(query)) {
            item.style.display = 'block';
            if (node) {
                node.classList.add('node-highlight');
            }
            
            // Expandir ancestros
            let parent = item.parentElement;
            while (parent) {
                if (parent.tagName === 'UL') {
                    parent.style.display = 'flex';
                    parent.style.flexDirection = 'row';
                    
                    // Rotar caret correspondiente
                    const parentLi = parent.parentElement;
                    if (parentLi) {
                        const toggleBtn = parentLi.querySelector('.btn-toggle-org');
                        if (toggleBtn) {
                            const icon = toggleBtn.querySelector('.toggle-icon');
                            if (icon) icon.style.transform = 'rotate(90deg)';
                        }
                    }
                }
                if (parent.tagName === 'LI' && parent.classList.contains('org-item')) {
                    parent.style.display = 'block';
                }
                parent = parent.parentElement;
            }
        } else {
            item.style.display = 'none';
        }
    });

    // Mantener visibles coincidencias profundas
    items.forEach(item => {
        const searchText = item.getAttribute('data-search') || "";
        if (searchText.includes(query)) {
            item.style.display = 'block';
        }
    });
}

function downloadOrganigrama() {
    const captureEl = document.getElementById('capture-container');
    if (!captureEl) return;
    
    // Forzar temporalmente que todos los nodos estén expandidos al descargar para que la foto se vea completa
    const originalDisplays = [];
    const collapsibles = captureEl.querySelectorAll('ul');
    collapsibles.forEach(ul => {
        originalDisplays.push({ el: ul, display: ul.style.display });
        ul.style.display = 'flex';
        ul.style.flexDirection = 'row';
    });

    const toggleIcons = captureEl.querySelectorAll('.toggle-icon');
    toggleIcons.forEach(icon => {
        icon.style.transform = 'rotate(90deg)';
    });

    html2canvas(captureEl, {
        backgroundColor: '#fafbfc',
        scale: 2, // Mayor nitidez
        useCORS: true,
        logging: false
    }).then(canvas => {
        // Restaurar estado de colapsado original
        originalDisplays.forEach(item => {
            item.el.style.display = item.display;
        });
        
        // Crear enlace de descarga y disparar clic
        const link = document.createElement('a');
        link.download = 'organigrama-clinica-euromedica.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }).catch(err => {
        console.error("Error al generar imagen:", err);
        alert("Ocurrió un error al generar la descarga. Intente nuevamente.");
    });
}
</script>

@endsection
