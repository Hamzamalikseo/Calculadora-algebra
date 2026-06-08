<?php
$resultado = '';
$pasos = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    
    if ($tipo === 'lineal') {
        // ax + b = c
        $a = floatval($_POST['a'] ?? 0);
        $b = floatval($_POST['b'] ?? 0);
        $c = floatval($_POST['c'] ?? 0);
        
        if ($a == 0) {
            $error = 'El coeficiente "a" no puede ser cero en una ecuación lineal.';
        } else {
            $pasos[] = "Ecuación: {$a}x + {$b} = {$c}";
            $pasos[] = "Paso 1: Pasar {$b} al otro lado → {$a}x = {$c} - {$b}";
            $resta = $c - $b;
            $pasos[] = "Paso 2: {$a}x = {$resta}";
            $x = $resta / $a;
            $pasos[] = "Paso 3: x = {$resta} ÷ {$a}";
            $resultado = "x = " . round($x, 4);
        }
    } elseif ($tipo === 'cuadratica') {
        // ax² + bx + c = 0
        $a = floatval($_POST['a'] ?? 0);
        $b = floatval($_POST['b'] ?? 0);
        $c = floatval($_POST['c'] ?? 0);
        
        if ($a == 0) {
            $error = 'El coeficiente "a" no puede ser cero en una ecuación cuadrática.';
        } else {
            $discriminante = ($b * $b) - (4 * $a * $c);
            $pasos[] = "Ecuación: {$a}x² + {$b}x + {$c} = 0";
            $pasos[] = "Paso 1: Calcular el discriminante → D = b² - 4ac";
            $pasos[] = "Paso 2: D = ({$b})² - 4×{$a}×{$c} = {$discriminante}";
            
            if ($discriminante < 0) {
                $error = 'El discriminante es negativo — esta ecuación no tiene solución real.';
            } elseif ($discriminante == 0) {
                $x = -$b / (2 * $a);
                $pasos[] = "Paso 3: D = 0, hay una sola solución → x = -b ÷ 2a";
                $resultado = "x = " . round($x, 4);
            } else {
                $x1 = (-$b + sqrt($discriminante)) / (2 * $a);
                $x2 = (-$b - sqrt($discriminante)) / (2 * $a);
                $pasos[] = "Paso 3: x = (-b ± √D) ÷ 2a";
                $pasos[] = "Paso 4: x₁ = (" . (-$b) . " + √{$discriminante}) ÷ " . (2*$a);
                $pasos[] = "Paso 5: x₂ = (" . (-$b) . " - √{$discriminante}) ÷ " . (2*$a);
                $resultado = "x₁ = " . round($x1, 4) . " | x₂ = " . round($x2, 4);
            }
        }
    } elseif ($tipo === 'porcentaje') {
        $valor = floatval($_POST['valor'] ?? 0);
        $porcentaje = floatval($_POST['porcentaje'] ?? 0);
        $pasos[] = "Calcular el {$porcentaje}% de {$valor}";
        $pasos[] = "Paso 1: {$valor} × {$porcentaje} ÷ 100";
        $res = $valor * $porcentaje / 100;
        $pasos[] = "Paso 2: = {$res}";
        $resultado = "Resultado = " . round($res, 4);
    } elseif ($tipo === 'fraccion') {
        $n1 = intval($_POST['n1'] ?? 0);
        $d1 = intval($_POST['d1'] ?? 1);
        $n2 = intval($_POST['n2'] ?? 0);
        $d2 = intval($_POST['d2'] ?? 1);
        $op = $_POST['op'] ?? 'suma';
        
        if ($d1 == 0 || $d2 == 0) {
            $error = 'El denominador no puede ser cero.';
        } else {
            if ($op === 'suma') {
                $pasos[] = "Suma: {$n1}/{$d1} + {$n2}/{$d2}";
                $pasos[] = "Paso 1: Común denominador = {$d1} × {$d2} = " . ($d1*$d2);
                $nr = ($n1 * $d2) + ($n2 * $d1);
                $dr = $d1 * $d2;
                $pasos[] = "Paso 2: ({$n1}×{$d2} + {$n2}×{$d1}) / {$dr} = {$nr}/{$dr}";
            } elseif ($op === 'resta') {
                $pasos[] = "Resta: {$n1}/{$d1} - {$n2}/{$d2}";
                $pasos[] = "Paso 1: Común denominador = {$d1} × {$d2} = " . ($d1*$d2);
                $nr = ($n1 * $d2) - ($n2 * $d1);
                $dr = $d1 * $d2;
                $pasos[] = "Paso 2: ({$n1}×{$d2} - {$n2}×{$d1}) / {$dr} = {$nr}/{$dr}";
            } elseif ($op === 'multiplicacion') {
                $pasos[] = "Multiplicación: {$n1}/{$d1} × {$n2}/{$d2}";
                $nr = $n1 * $n2;
                $dr = $d1 * $d2;
                $pasos[] = "Paso 1: Numeradores × numeradores = {$n1}×{$n2} = {$nr}";
                $pasos[] = "Paso 2: Denominadores × denominadores = {$d1}×{$d2} = {$dr}";
            } else {
                $pasos[] = "División: {$n1}/{$d1} ÷ {$n2}/{$d2}";
                $nr = $n1 * $d2;
                $dr = $d1 * $n2;
                $pasos[] = "Paso 1: Invertir segunda fracción → {$n1}/{$d1} × {$d2}/{$n2}";
                $pasos[] = "Paso 2: = {$nr}/{$dr}";
            }
            // simplificar
            function mcd($a, $b) { return $b ? mcd($b, $a % $b) : abs($a); }
            $m = mcd(abs($nr), abs($dr));
            $ns = $nr / $m;
            $ds = $dr / $m;
            $pasos[] = "Paso 3: Simplificar → MCD({$nr},{$dr}) = {$m}";
            $resultado = "{$ns}/{$ds}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calculadora de Álgebra Paso a Paso en Español</title>
  <meta name="description" content="Resuelve ecuaciones lineales, cuadráticas, fracciones y porcentajes paso a paso en español. Herramienta gratuita para estudiantes hispanohablantes.">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f7fb; color: #2d2d2d; min-height: 100vh; }
    header { background: linear-gradient(135deg, #1a5276, #2471a3); color: white; padding: 28px 20px 22px; text-align: center; }
    header h1 { font-size: 26px; margin-bottom: 6px; }
    header p { font-size: 14px; opacity: 0.88; }
    .container { max-width: 680px; margin: 30px auto; padding: 0 16px 40px; }
    .tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .tab { padding: 9px 16px; border-radius: 20px; border: 2px solid #2471a3; background: white; color: #2471a3; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; text-decoration: none; }
    .tab.active, .tab:hover { background: #2471a3; color: white; }
    .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 20px; }
    .card h2 { font-size: 18px; color: #1a5276; margin-bottom: 16px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 4px; margin-top: 12px; }
    input[type=number], select { width: 100%; padding: 10px 14px; border: 1.5px solid #d0dde8; border-radius: 8px; font-size: 15px; outline: none; transition: border .2s; }
    input[type=number]:focus, select:focus { border-color: #2471a3; }
    .row { display: flex; gap: 12px; }
    .row > div { flex: 1; }
    button[type=submit] { width: 100%; margin-top: 20px; padding: 13px; background: #2471a3; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; border-radius: 8px; transition: background .2s; }
    button[type=submit]:hover { background: #1a5276; }
    .result-box { background: #eaf4fb; border: 1.5px solid #aed6f1; border-radius: 10px; padding: 18px 20px; margin-top: 20px; }
    .result-box h3 { color: #1a5276; font-size: 16px; margin-bottom: 10px; }
    .result-final { font-size: 22px; font-weight: 700; color: #1a5276; margin-bottom: 14px; }
    .paso { background: white; border-left: 3px solid #2471a3; padding: 8px 14px; margin-bottom: 7px; border-radius: 0 6px 6px 0; font-size: 14px; }
    .error-box { background: #fdf2f2; border: 1.5px solid #f1948a; border-radius: 10px; padding: 14px 18px; margin-top: 16px; color: #c0392b; font-size: 14px; }
    .formula-hint { background: #f8f9fa; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #666; margin-bottom: 16px; line-height: 1.6; }
    .formula-hint strong { color: #1a5276; }
    .divider { height: 1px; background: #eee; margin: 8px 0 16px; }
    footer { text-align: center; padding: 24px 16px; font-size: 13px; color: #888; }
    footer a { color: #2471a3; font-weight: 600; text-decoration: none; }
    footer a:hover { text-decoration: underline; }
    .powered { display: inline-block; background: #eaf4fb; border-radius: 20px; padding: 6px 16px; margin-top: 8px; font-size: 13px; }
  </style>
</head>
<body>

<header>
  <h1>🧮 Calculadora de Álgebra</h1>
  <p>Soluciones paso a paso en español — para estudiantes y padres</p>
</header>

<div class="container">

  <?php
  $tipo_activo = $_POST['tipo'] ?? 'lineal';
  ?>

  <form method="POST">
  <div class="tabs">
    <button type="submit" name="tipo" value="lineal" class="tab <?= (!isset($_POST['tipo']) || $tipo_activo==='lineal') ? 'active' : '' ?>">Ecuación Lineal</button>
    <button type="submit" name="tipo" value="cuadratica" class="tab <?= $tipo_activo==='cuadratica' ? 'active' : '' ?>">Ecuación Cuadrática</button>
    <button type="submit" name="tipo" value="fraccion" class="tab <?= $tipo_activo==='fraccion' ? 'active' : '' ?>">Fracciones</button>
    <button type="submit" name="tipo" value="porcentaje" class="tab <?= $tipo_activo==='porcentaje' ? 'active' : '' ?>">Porcentajes</button>
  </div>

  <div class="card">

    <?php if (!isset($_POST['tipo']) || $tipo_activo === 'lineal'): ?>
      <h2>Ecuación Lineal — ax + b = c</h2>
      <div class="formula-hint">
        Introduce los valores de <strong>a</strong>, <strong>b</strong> y <strong>c</strong> para resolver <strong>ax + b = c</strong>
      </div>
      <div class="row">
        <div><label>a (coeficiente de x)</label><input type="number" name="a" value="<?= $_POST['a'] ?? 2 ?>" step="any" required></div>
        <div><label>b (término independiente)</label><input type="number" name="b" value="<?= $_POST['b'] ?? 3 ?>" step="any" required></div>
        <div><label>c (resultado)</label><input type="number" name="c" value="<?= $_POST['c'] ?? 11 ?>" step="any" required></div>
      </div>

    <?php elseif ($tipo_activo === 'cuadratica'): ?>
      <h2>Ecuación Cuadrática — ax² + bx + c = 0</h2>
      <div class="formula-hint">
        Introduce los valores para resolver usando la <strong>fórmula cuadrática</strong>: x = (-b ± √(b²-4ac)) / 2a
      </div>
      <div class="row">
        <div><label>a</label><input type="number" name="a" value="<?= $_POST['a'] ?? 1 ?>" step="any" required></div>
        <div><label>b</label><input type="number" name="b" value="<?= $_POST['b'] ?? -5 ?>" step="any" required></div>
        <div><label>c</label><input type="number" name="c" value="<?= $_POST['c'] ?? 6 ?>" step="any" required></div>
      </div>

    <?php elseif ($tipo_activo === 'porcentaje'): ?>
      <h2>Calculadora de Porcentajes</h2>
      <div class="formula-hint">
        Calcula qué porcentaje representa un número sobre otro valor base.
      </div>
      <div class="row">
        <div><label>Valor base</label><input type="number" name="valor" value="<?= $_POST['valor'] ?? 200 ?>" step="any" required></div>
        <div><label>Porcentaje (%)</label><input type="number" name="porcentaje" value="<?= $_POST['porcentaje'] ?? 15 ?>" step="any" required></div>
      </div>

    <?php elseif ($tipo_activo === 'fraccion'): ?>
      <h2>Calculadora de Fracciones</h2>
      <div class="formula-hint">
        Opera con dos fracciones y obtén el resultado simplificado paso a paso.
      </div>
      <label>Operación</label>
      <select name="op">
        <option value="suma" <?= ($_POST['op']??'suma')==='suma'?'selected':'' ?>>Suma (+)</option>
        <option value="resta" <?= ($_POST['op']??'')==='resta'?'selected':'' ?>>Resta (-)</option>
        <option value="multiplicacion" <?= ($_POST['op']??'')==='multiplicacion'?'selected':'' ?>>Multiplicación (×)</option>
        <option value="division" <?= ($_POST['op']??'')==='division'?'selected':'' ?>>División (÷)</option>
      </select>
      <div class="row" style="margin-top:12px">
        <div>
          <label>Numerador 1</label><input type="number" name="n1" value="<?= $_POST['n1'] ?? 3 ?>" required>
          <label>Denominador 1</label><input type="number" name="d1" value="<?= $_POST['d1'] ?? 4 ?>" required>
        </div>
        <div>
          <label>Numerador 2</label><input type="number" name="n2" value="<?= $_POST['n2'] ?? 2 ?>" required>
          <label>Denominador 2</label><input type="number" name="d2" value="<?= $_POST['d2'] ?? 5 ?>" required>
        </div>
      </div>
    <?php endif; ?>

    <button type="submit">Resolver Paso a Paso →</button>

    <?php if ($error): ?>
      <div class="error-box">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php elseif ($resultado && !empty($pasos)): ?>
      <div class="result-box">
        <h3>📋 Solución Paso a Paso</h3>
        <?php foreach ($pasos as $paso): ?>
          <div class="paso"><?= htmlspecialchars($paso) ?></div>
        <?php endforeach; ?>
        <div class="divider"></div>
        <div class="result-final">✅ <?= htmlspecialchars($resultado) ?></div>
      </div>
    <?php endif; ?>

  </div>
  </form>

</div>

<footer>
  <div>¿Necesitas soluciones más detalladas?</div>
  <div class="powered">
    Visita <a href="https://lacalculadora-dealicia.com/" title="Calculadora de Alicia — Soluciones matemáticas paso a paso">Calculadora de Alicia</a> — explicaciones completas en español
  </div>
</footer>

</body>
</html>
