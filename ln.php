<?php

function quitar_tildes($cadena) {
    if (!function_exists('transliterator_transliterate')) {
        die("La función transliterator_transliterate no está disponible. Verifica si la extensión Intl está habilitada.");
    }
    return transliterator_transliterate('Any-Latin; Latin-ASCII', $cadena);
}

function palabras_validas($consulta, $conexion) {
    // Dividir la consulta en palabras
    $palabras = explode(' ', $consulta);

    // Palabras que no necesitan validación
    $palabras_excluidas = ['o', 'en', 'el', 'una', 'un', 'de', 'con', 'mas'];

    // Convertir todas las palabras a minúsculas para validación
    $palabras = array_map('strtolower', $palabras);

    // Filtrar solo palabras relevantes (excluye palabras en la lista y números)
    $palabras_a_validar = array_filter($palabras, function ($palabra) use ($palabras_excluidas) {
        return !in_array($palabra, $palabras_excluidas) && !is_numeric($palabra);
    });

    // Si no hay palabras a validar, todo es válido
    if (empty($palabras_a_validar)) {
        return true;
    }

    // Crear placeholders para la consulta
    $placeholders = implode(',', array_fill(0, count($palabras_a_validar), '?'));

    // Consulta para validar palabras
    $query = "SELECT palabra FROM ln_diccionario WHERE palabra IN ($placeholders)";
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        die("Error al preparar la consulta de palabras válidas: " . $conexion->error);
    }

    // Vincular las palabras como parámetros
    $stmt->bind_param(str_repeat('s', count($palabras_a_validar)), ...$palabras_a_validar);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si todas las palabras son válidas
    if ($stmt->num_rows === count($palabras_a_validar)) {
        $stmt->close();
        return true; // Todas las palabras son válidas
    }

    $stmt->close();
    return false; // Hay palabras no válidas
}

function procesa_consulta($consulta, $conexion, &$sql) {
    // Limpieza de la consulta
    $consulta = trim($consulta);
    $consulta = strtolower($consulta);
    $consulta = quitar_tildes($consulta);

    // Validar palabras válidas
    if (!palabras_validas($consulta, $conexion)) {
        //echo "La consulta contiene palabras no válidas.<br>";
        $sql = '';
        return false;
    }

    // Lista de patrones definidos directamente en el código, ordenados de mayor especificidad a menor especificidad
    $patrones = [
        '/(?:busco|deseo|quiero) (?:un|una) (\w+) en(?: el)? (\w+) o en(?: el)? (\w+)/' => "busco tipo zona1 o zona2",
        '/(?:busco|deseo|quiero) (?:un|una) (\w+) con (\d+) dormitorios en(?: el)? (\w+)/' => "busco tipo numero dormitorios zona",
        '/(?:busco|deseo|quiero) (?:un|una) (\w+) con mas de (\d+) dormitorios en(?: el)? (\w+)/' => "busco tipo mas numero dormitorios zona",
        '/(?:busco|deseo|quiero) (?:un|una) (\w+) de mas de (\d+) metros cuadrados/' => "busco tipo mas metros metros cuadrados",
        '/(?:busco|deseo|quiero) (?:un|una) (\w+) (?:barato|barata)/' => "busco tipo barato",
        '/(?:busco|deseo|quiero) (?:un|una) (\w+) en(?: el)? (\w+)/' => "busco tipo zona",
        '/(?:busco|deseo|quiero) en(?: el)? (\w+)/' => "busco zona",
        '/(?:busco|deseo|quiero) (?:un|una) (\w+)/' => "busco tipo"
    ];

    // Iterar sobre los patrones y buscar coincidencias
    foreach ($patrones as $patron => $referencia) {
        if (preg_match($patron, $consulta, $matches)) {
            // Depuración: Mostrar el patrón encontrado
            //echo "Patrón encontrado: $patron, referencia: $referencia<br>";
            
            // Buscar la consulta correspondiente en la base de datos usando la referencia
            $query = "SELECT consultasql FROM ln_patrones WHERE patron = ?";
            $stmt = $conexion->prepare($query);
            if (!$stmt) {
                die("Error al preparar la consulta: " . $conexion->error);
            }

            $stmt->bind_param('s', $referencia);
            $stmt->execute();
            $stmt->bind_result($consulta_sql);

            if ($stmt->fetch()) {
                $stmt->close();

                // Reemplazar los placeholders (%1, %2, etc.) en la consulta SQL
                for ($i = 1; $i < count($matches); $i++) {
                    $consulta_sql = str_replace("%$i", $matches[$i], $consulta_sql);
                }

                // Asignar la consulta SQL generada
                $sql = $consulta_sql;
                return true;
            } else {
                // No se encontró consulta SQL para la referencia
                echo "No se encontró consulta SQL para la referencia: $referencia<br>";
            }
        }
    }

    // Si no hay coincidencias
    $sql = '';
    echo "No se encontraron coincidencias para la consulta: $consulta<br>";
    return false;
}
?>
