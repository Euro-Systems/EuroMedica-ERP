<div class="tabs">
<div class="tab" onclick="mostrar('practicantes')">Practicantes</div>
<div class="tab active" onclick="mostrar('ficha_practicante')">Ficha Detalle</div>
</div>
<div class="rh-card"><h2>${p.nombre} ${p.ap} ${p.am}</h2></div>
<div class="ficha-wrap">
<div class="col">
<div class="rh-card">
<h3>Datos personales</h3>
<div class="empleado-grid">
<div><b>Nombre</b><input value="${p.nombre}" onchange="practSel.nombre=this.value"></div>
<div><b>Apellido Paterno</b><input value="${p.ap}" onchange="practSel.ap=this.value"></div>
<div><b>Apellido Materno</b><input value="${p.am}" onchange="practSel.am=this.value"></div>
<div><b>NSS</b><input value="${p.nss}" onchange="practSel.nss=this.value"></div>
<div><b>RFC</b><input value="${p.rfc}" onchange="practSel.rfc=this.value"></div>
<div><b>CURP</b><input value="${p.curp}" onchange="practSel.curp=this.value"></div>
<div><b>Celular</b><input value="${p.celular}" onchange="practSel.celular=this.value"></div>
<div><b>Dirección</b><input value="${p.direccion}" onchange="practSel.direccion=this.value"></div>
<div><b>Estado civil</b><input value="${p.estado_civil}" onchange="practSel.estado_civil=this.value"></div>
<div><b>Fecha nacimiento</b><input type="date" value="${p.nacimiento}" onchange="practSel.nacimiento=this.value"></div>
<div><b>Talla Uniforme</b><select onchange="practSel.talla_uniforme=this.value" style="width:100%;padding:5px;border-radius:6px;border:1px solid #d1d5db;">
    <option value="S" ${p.talla_uniforme==='S'?'selected':''}>Chica (S)</option>
    <option value="M" ${p.talla_uniforme==='M'?'selected':''}>Mediana (M)</option>
    <option value="L" ${p.talla_uniforme==='L'?'selected':''}>Grande (L)</option>
    <option value="XL" ${p.talla_uniforme==='XL'?'selected':''}>Extra Grande (XL)</option>
</select></div>
<div><b>Tipo de Sangre</b><input value="${p.tipo_sangre||''}" onchange="practSel.tipo_sangre=this.value"></div>
<div><b>Alergias</b><input value="${p.alergias||''}" onchange="practSel.alergias=this.value"></div>
<div><b>Nivel Inglés</b><input value="${p.nivel_ingles||''}" onchange="practSel.nivel_ingles=this.value"></div>
</div>
</div>
</div>
<div class="col">
<div class="rh-card">
<h3>Control de horas</h3>
<div class="empleado-grid">
<div><b>Horas requeridas</b><input value="${p.horas_requeridas}" readonly></div>
<div><b>Horas acumuladas</b><input id="horasInput" value="${p.horas_llevadas}"></div>
</div>
<div style="margin-top:10px;text-align:right;"><button class="btn-ver" onclick="guardarHoras()">Guardar horas</button></div>
</div>
<div class="rh-card">
<h3>Periodo</h3>
<div class="empleado-grid">
<div><b>Fecha inicio</b><input type="date" value="${p.fecha_inicio}"></div>
<div><b>Fecha término</b><input type="date" value="${p.fecha_termino}"></div>
</div>
</div>
</div>
</div>
</div>
<div class="rh-card sticky-acciones" style="margin-top:10px;">
<h3>Acciones de Ficha</h3>
<div style="display:flex; gap:10px; flex-wrap:wrap;">
<button class="btn-ver" style="background:#3b82f6; margin:0;" onclick="guardarCambiosFicha()">Guardar Cambios</button>
<button class="btn-ver" style="background:#eab308; margin:0;" onclick="exportarFichaPDF()">Exportar PDF</button>
<button class="btn-ver" style="background:#ef4444; margin:0;" onclick="eliminarRegistro('practicante')">Eliminar Registro</button>
</div>
</div>
