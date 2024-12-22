<?php
require('ln.php'); 

$server = "localhost";
$username = "cursophp";
$pwd = "password"; 
$db_name = "lindavista";

// Conectarse a la base de datos
$conexion = new mysqli($server, $username, $pwd, $db_name);
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");

$message = '';
$results = []; 

// Gestionar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consulta = $_POST['query'] ?? ''; 

    if (empty($consulta)) {
        $message = "Debe introducir una consulta";
    } else {
        $sql = ''; 

        // Procesar la consulta de entrada utilizando la lógica de logic.php
        if (procesa_consulta($consulta, $conexion, $sql)) {
            // Ejecutar consulta
            $query_result = $conexion->query($sql);
            if ($query_result && $query_result->num_rows > 0) {
                // Almacenar los resultados
                $results = $query_result->fetch_all(MYSQLI_ASSOC);
            } else {
                $message = "No hay viviendas disponibles";
            }
        } else {
            $message = "La consulta no es correcta";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de vivienda</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilo adicional para personalizar el diseño */
        body {
            background-color: #f8f9fa; /* Fondo claro */
        }
        .hero {
            background: linear-gradient(to right, #007bff, #6610f2); /* Degradado */
            color: white;
            padding: 60px 0;
        }
        .property-card img {
            height: 200px;
            object-fit: cover;
        }
        .property-card .card-body {
            font-size: 0.9rem;
        }
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Banner Principal -->
    <section class="hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Inmobiliaria Lindavista</h1>
            <p class="lead">Encuentre la propiedad de sus sueños en unos pocos clics.</p>
        </div>
    </section>

    <!-- Contenido principal -->
    <div class="container py-5">
        <!-- Formulario de búsqueda -->
        <div class="card shadow-sm mb-5">
            <div class="card-body">
                <form method="POST" action="" class="row g-3">
                    <div class="col-md-9">
                        <input 
                            type="text" 
                            name="query" 
                            placeholder="Introduzca su consulta de búsqueda..." 
                            class="form-control form-control-lg" 
                            value="<?= htmlspecialchars($_POST['query'] ?? '') ?>"
                        >
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Buscar</button>
                    </div>
                </form>
                <?php if (!empty($message)): ?>
                    <div class="mt-3 alert alert-danger text-center" role="alert">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección de resultados -->
        <?php if (!empty($results)): ?>
            <h2 class="text-center mb-4">Resultados de la búsqueda</h2>
            <div class="row g-4">
                <?php foreach ($results as $property): ?>
                    <div class="col-md-4">
                        <div class="card property-card h-100 shadow-sm">
                            <!-- Imagen de propiedad -->
                            <?php if (!empty($property['foto'])): ?>
                                <img 
                                    src="./fotos/<?= htmlspecialchars($property['foto']) ?>" 
                                    alt="Property Image" 
                                    class="card-img-top"
                                >
                            <?php else: ?>
                                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span>Ninguna imagen disponible</span>
                                </div>
                            <?php endif; ?>
                            <!-- Detalles de la propiedad -->
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?= htmlspecialchars($property['tipo']) ?></h5>
                                <p class="card-text">
                                    <strong>Ubicación:</strong> <?= htmlspecialchars($property['zona']) ?><br>
                                    <strong>Dormitorios:</strong> <?= $property['ndormitorios'] ?><br>
                                    <strong>Tamaño:</strong> <?= $property['metros_cuadrados'] ?> m²<br>
                                    <strong>Precio:</strong> $<?= number_format($property['precio'], 2) ?><br>
                                    <?php if (!empty($property['extras'])): ?>
                                        <strong>Extras:</strong> <?= htmlspecialchars($property['extras']) ?><br>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conexion->close();
?>