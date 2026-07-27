<div class="nomina-card">

    <h2>Lista de empleados</h2>

    <table class="nomina-table">

        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Puesto</th>
                <th>Salario</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>

            ${empleados.map(e=>`
            <tr>

                <td>${e.id}</td>

                <td class="nombre">${e.nombre}</td>

                <td>
                    <span class="badge">${e.puesto}</span>
                </td>

                <td>
                    $${e.salario.toLocaleString()}
                </td>

                <td>
                    <button class="btn" onclick="seleccionar(${e.id})">
                        Ver
                    </button>
                </td>

            </tr>`).join('')}

        </tbody>

    </table>

</div>
