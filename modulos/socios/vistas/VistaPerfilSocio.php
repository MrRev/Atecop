<?php
// Verificar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['idadmin'])) {
    header('Location: index.php?modulo=seguridad&accion=login');
    exit;
}

require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="contenedor-principal">
    <div class="encabezado-seccion">
            <h1>Perfil del Socio</h1>
        <div class="grupo-botones">
            <a href="index.php?modulo=socios&accion=formulario&id=<?php echo $socio->getIdsocio(); ?>" 
               class="boton-primario">Editar</a>
            <a href="index.php?modulo=socios&accion=listar" class="boton-secundario">← Volver</a>
        </div>
    </div>

    <!-- Información General del Socio -->
    <div class="tarjeta-perfil">
        <div class="encabezado-tarjeta">
            <h2>Información General</h2>
            <span class="estado-badge estado-<?php echo strtolower($socio->getEstado()); ?>">
                <?php echo htmlspecialchars($socio->getEstado()); ?>
            </span>
        </div>
        
        <div class="contenido-tarjeta">
            <div class="fila-info">
                <div class="campo-info">
                    <label>DNI/RUC:</label>
                    <span><?php echo htmlspecialchars($socio->getDni()); ?></span>
                </div>
                <div class="campo-info">
                    <label>Nombre Completo:</label>
                    <span><?php echo htmlspecialchars($socio->getNombrecompleto()); ?></span>
                </div>
            </div>

            <div class="fila-info">
                <div class="campo-info">
                    <label>Fecha de Nacimiento:</label>
                    <span><?php echo $socio->getFechanacimiento() ? date('d/m/Y', strtotime($socio->getFechanacimiento())) : 'No registrada'; ?></span>
                </div>
                <div class="campo-info">
                    <label>Profesión:</label>
                    <span><?php echo htmlspecialchars($socio->getNombreprofesion() ?? 'No especificada'); ?></span>
                </div>
            </div>

            <div class="fila-info">
                <div class="campo-info">
                    <label>Email:</label>
                    <span><?php echo htmlspecialchars($socio->getEmail()); ?></span>
                </div>
                <div class="campo-info">
                    <label>Teléfono:</label>
                    <span><?php echo htmlspecialchars($socio->getTelefono() ?? 'No registrado'); ?></span>
                </div>
            </div>

            <div class="campo-info campo-completo">
                <label>Dirección:</label>
                <span><?php echo htmlspecialchars($socio->getDireccion() ?? 'No registrada'); ?></span>
            </div>

            <div class="campo-info campo-completo">
                <label>Cuenta Bancaria:</label>
                <span><?php echo htmlspecialchars($socio->getNumcuentabancaria() ?? 'No registrada'); ?></span>
            </div>
        </div>
    </div>

    <!-- Información de Membresía -->
    <div class="tarjeta-perfil">
        <div class="encabezado-tarjeta">
            <h2>Membresía</h2>
        </div>
        
        <div class="contenido-tarjeta">
            <div class="fila-info">
                <div class="campo-info">
                    <label>Tipo de Socio:</label>
                    <span><?php echo htmlspecialchars($socio->getNombretipo()); ?></span>
                </div>
                <div class="campo-info">
                    <label>Plan Actual:</label>
                    <span><?php echo htmlspecialchars($socio->getNombreplan()); ?></span>
                </div>
            </div>

            <div class="fila-info">
                <div class="campo-info">
                    <label>Costo del Plan:</label>
                    <span>S/ <?php echo number_format($plan['costo'] ?? 0, 2); ?></span>
                </div>
                <div class="campo-info">
                    <label>Duración:</label>
                    <span><?php echo htmlspecialchars($plan['duracionmeses'] ?? '0'); ?> mes(es)</span>
                </div>
            </div>

            <div class="fila-info">
                <div class="campo-info">
                    <label>Fecha de Vencimiento:</label>
                    <span class="fecha-vencimiento <?php echo (strtotime($socio->getFechavencimiento()) < time()) ? 'vencido' : ''; ?>">
                        <?php echo date('d/m/Y', strtotime($socio->getFechavencimiento())); ?>
                    </span>
                </div>
                <div class="campo-info">
                    <label>Fecha de Registro:</label>
                    <span><?php echo date('d/m/Y H:i', strtotime($socio->getFechacreacion())); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Pagos -->
    <div class="tarjeta-perfil">
        <div class="encabezado-tarjeta">
            <h2>Historial de Pagos</h2>
                <a href="index.php?modulo=pagos&accion=registrar&idsocio=<?php echo $socio->getIdsocio(); ?>" 
               class="boton-pequeno">+ Registrar Pago</a>
        </div>
        
        <div class="contenido-tarjeta">
            <?php if (isset($pagos) && count($pagos) > 0): ?>
                <table class="tabla-datos tabla-compacta">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Estado</th>
                            <th>Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagos as $pago): ?>
                            <tr>
                                    <td><?php echo isset($pago['fechapago']) ? date('d/m/Y', strtotime($pago['fechapago'])) : ''; ?></td>
                                    <td><?php echo htmlspecialchars($pago['concepto'] ?? ''); ?></td>
                                    <td>S/ <?php echo number_format($pago['monto'] ?? 0, 2); ?></td>
                                    <td><?php echo htmlspecialchars($pago['nombremetodo'] ?? ''); ?></td>
                                    <td>
                                        <?php $estadoPago = $pago['estado'] ?? ''; ?>
                                        <span class="estado-badge estado-<?php echo strtolower($estadoPago); ?>">
                                            <?php echo htmlspecialchars($estadoPago); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($pago['urlcomprobante'])): ?>
                                            <a href="<?php echo htmlspecialchars($pago['urlcomprobante']); ?>" 
                                               target="_blank" class="enlace-comprobante">Ver</a>
                                        <?php else: ?>
                                            <span class="texto-gris">Sin comprobante</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="texto-centrado texto-gris">No hay pagos registrados</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cursos Inscritos -->
    <div class="tarjeta-perfil">
        <div class="encabezado-tarjeta">
            <h2>Cursos Inscritos</h2>
        </div>
        
        <div class="contenido-tarjeta">
            <?php if (isset($cursos) && count($cursos) > 0): ?>
                <table class="tabla-datos tabla-compacta">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado Pago</th>
                            <th>Estado Curso</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cursos as $curso): ?>
                            <tr>
                                    <td><?php echo htmlspecialchars($curso['nombrecurso'] ?? $curso['nombre'] ?? ''); ?></td>
                                    <td><?php echo !empty($curso['fechainicio']) ? date('d/m/Y', strtotime($curso['fechainicio'])) : ''; ?></td>
                                    <td><?php echo !empty($curso['fechafin']) ? date('d/m/Y', strtotime($curso['fechafin'])) : ''; ?></td>
                                    <td>
                                        <?php $estadoPagoCurso = $curso['estadopagocurso'] ?? ''; ?>
                                        <span class="estado-badge estado-<?php echo strtolower($estadoPagoCurso); ?>">
                                            <?php echo htmlspecialchars($estadoPagoCurso); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($curso['estado'] ?? ''); ?></td>
                                </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="texto-centrado texto-gris">No está inscrito en ningún curso</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>
