<?php
// Inițializare variabile
$m = 4;
$n = 5;
$matriceInput = "3 -2 5 0 8\n-1 7 4 2 -3\n6 0 -5 9 1\n2 4 -8 3 7";
$matriceA = [];
$vectorX = [];
$maximePeColoane = [];
$pozitiiMaxime = [];
$afisareMatrice = "";
$afisareVector = "";
$explicatieVector = "";
$detaliiCalcul = "";
$rezultateStyle = "display: none;";

// Procesarea datelor la submit formular
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $m = isset($_POST['m']) ? intval($_POST['m']) : 4;
    $n = isset($_POST['n']) ? intval($_POST['n']) : 5;
    $matriceInput = isset($_POST['matrice']) ? trim($_POST['matrice']) : "";
    
    // Validare dimensiuni
    if ($m < 1 || $m > 10) $m = 4;
    if ($n < 1 || $n > 10) $n = 5;
    
    // Dacă s-a apăsat "Generează Matrice Aleatorie"
    if (isset($_POST['genereaza'])) {
        $matriceText = '';
        for ($i = 0; $i < $m; $i++) {
            $rand = [];
            for ($j = 0; $j < $n; $j++) {
                $numar = mt_rand(-20, 20);
                $rand[] = $numar;
            }
            $matriceText .= implode(' ', $rand) . ($i < $m - 1 ? "\n" : '');
        }
        $matriceInput = $matriceText;
    }
    
    // Parsarea matricei
    $linii = explode("\n", $matriceInput);
    $linii = array_map('trim', $linii);
    $linii = array_filter($linii, function($line) { return $line !== ''; });
    
    $matriceA = [];
    $eroare = false;
    
    if (count($linii) !== $m) {
        $eroare = true;
        $matriceInput = "3 -2 5 0 8\n-1 7 4 2 -3\n6 0 -5 9 1\n2 4 -8 3 7";
        $m = 4;
        $n = 5;
    } else {
        foreach ($linii as $i => $linie) {
            $elemente = preg_split('/\s+/', $linie);
            
            if (count($elemente) !== $n) {
                $eroare = true;
                break;
            }
            
            $rand = [];
            foreach ($elemente as $elem) {
                $val = intval($elem);
                if (!is_numeric($elem) && !ctype_digit($elem) && !(strpos($elem, '-') === 0 && ctype_digit(substr($elem, 1)))) {
                    $eroare = true;
                    break 2;
                }
                $rand[] = $val;
            }
            $matriceA[] = $rand;
        }
    }
    
    // Dacă nu sunt erori, calculăm vectorul
    if (!$eroare && !empty($matriceA)) {
        $rezultateStyle = "display: block;";
        
        // Inițializare vector X cu valori foarte mici
        $vectorX = array_fill(0, $n, -999999);
        $maximePeColoane = array_fill(0, $n, -999999);
        $pozitiiMaxime = array_fill(0, $n, []);
        $detaliiCalcul = "<strong>Calcul maxim pe coloane:</strong><br><br>";
        
        // Calcul pentru fiecare coloană
        for ($col = 0; $col < $n; $col++) {
            $max = -999999;
            $pozitiiMax = [];
            
            $detaliiCalcul .= "<strong>Coloana " . ($col + 1) . ":</strong> ";
            
            for ($row = 0; $row < $m; $row++) {
                $val = $matriceA[$row][$col];
                
                if ($val > $max) {
                    $max = $val;
                    $pozitiiMax = [sprintf("(%d,%d)", $row + 1, $col + 1)];
                    if ($row > 0) {
                        $detaliiCalcul .= "Nou maxim: $val la (" . ($row + 1) . "," . ($col + 1) . ")";
                    } else {
                        $detaliiCalcul .= "$val la (" . ($row + 1) . "," . ($col + 1) . ")";
                    }
                } elseif ($val == $max && $max != -999999) {
                    $pozitiiMax[] = sprintf("(%d,%d)", $row + 1, $col + 1);
                    $detaliiCalcul .= ", egal la (" . ($row + 1) . "," . ($col + 1) . ")";
                }
            }
            
            $vectorX[$col] = $max;
            $maximePeColoane[$col] = $max;
            $pozitiiMaxime[$col] = $pozitiiMax;
            $detaliiCalcul .= " → <span class=\"highlight\">max = $max</span><br>";
        }
        
        // Generare afișare matrice
        $afisareMatrice = '';
        for ($row = 0; $row < $m; $row++) {
            $afisareMatrice .= '<div class="matrix-row">';
            for ($col = 0; $col < $n; $col++) {
                $clase = 'matrix-cell matrix-a-cell';
                $esteMaxim = false;
                
                // Verificăm dacă această celulă este maximul coloanei
                foreach ($pozitiiMaxime[$col] as $pozitie) {
                    preg_match('/\((\d+),(\d+)\)/', $pozitie, $matches);
                    if (isset($matches[1]) && ($matches[1] - 1) == $row && ($matches[2] - 1) == $col) {
                        $clase .= ' max-in-column';
                        $esteMaxim = true;
                        break;
                    }
                }
                
                $afisareMatrice .= sprintf(
                    '<div class="%s"%s>%d</div>',
                    $clase,
                    $esteMaxim ? ' title="Maximul coloanei ' . ($col + 1) . '"' : '',
                    $matriceA[$row][$col]
                );
            }
            $afisareMatrice .= '</div>';
        }
        
        // Generare afișare vector
        $afisareVector = '';
        for ($i = 0; $i < $n; $i++) {
            $afisareVector .= sprintf(
                '<span class="vector-element" title="Maximul coloanei %d = %d">%d</span> ',
                $i + 1,
                $vectorX[$i],
                $vectorX[$i]
            );
        }
        
        // Adăugăm titlu vector
        $afisareVector .= '<div style="text-align: center; margin-top: 15px; color: #ecf0f1; font-style: italic;">';
        $afisareVector .= "X($n) = [max(col1), max(col2), ..., max(col$n)]";
        $afisareVector .= '</div>';
        
        // Generare explicație vector
        $explicatieVector = '<div style="text-align: center; margin-top: 15px;">';
        for ($i = 0; $i < $n; $i++) {
            $explicatieVector .= "x<sub>" . ($i + 1) . "</sub> = " . $vectorX[$i] . " (max col " . ($i + 1) . ")";
            if ($i < $n - 1) {
                $explicatieVector .= ' &nbsp;&nbsp; ';
            }
        }
        $explicatieVector .= '</div>';
    }
}

// Funcție pentru resetare
function resetare() {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vector cu Maximul pe Coloane - Matrice</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 900px;
            padding: 40px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 28px;
            text-align: center;
        }

        .description {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.5;
            font-size: 16px;
            text-align: center;
        }

        .problem {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #3498db;
        }

        .problem h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .problem p {
            color: #444;
            line-height: 1.6;
        }

        .formula {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            background-color: #ecf0f1;
            border-radius: 8px;
        }

        .input-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
        }

        @media (max-width: 768px) {
            .input-section {
                grid-template-columns: 1fr;
            }
        }

        .input-group {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
        }

        .input-group h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 20px;
            text-align: center;
        }

        .dimension-inputs {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            justify-content: center;
        }

        .dim-input {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dim-input label {
            font-weight: 600;
            color: #444;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="number"] {
            width: 100px;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
            transition: border-color 0.3s;
        }

        input[type="number"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .matrix-input textarea {
            width: 100%;
            height: 180px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            resize: vertical;
            transition: border-color 0.3s;
        }

        .matrix-input textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        .hint {
            font-size: 14px;
            color: #666;
            margin-top: 8px;
            font-style: italic;
        }

        .buttons {
            display: flex;
            gap: 15px;
            margin: 25px 0;
        }

        button {
            flex: 1;
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 16px;
            font-size: 17px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(52, 152, 219, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .secondary-btn {
            background: linear-gradient(to right, #95a5a6, #7f8c8d);
        }

        .results {
            margin-top: 40px;
        }

        .results h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
            font-size: 24px;
        }

        .matrices-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .matrices-container {
                grid-template-columns: 1fr;
            }
        }

        .matrix-display {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            border: 2px solid #e9ecef;
        }

        .matrix-display h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            text-align: center;
            font-size: 18px;
        }

        .matrix {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            line-height: 1.8;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            overflow-x: auto;
        }

        .matrix-row {
            display: flex;
            justify-content: center;
            margin-bottom: 5px;
        }

        .matrix-cell {
            width: 60px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            border-radius: 6px;
            font-weight: bold;
        }

        .matrix-a-cell {
            background-color: #e8f4fc;
            border: 1px solid #3498db;
            color: #2c3e50;
        }

        .max-in-column {
            background-color: #ffeb3b !important;
            border: 2px solid #ff9800 !important;
            color: #e65100 !important;
            box-shadow: 0 0 8px rgba(255, 152, 0, 0.5);
        }

        .vector-display {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            text-align: center;
            margin: 15px 0;
            overflow-x: auto;
        }

        .vector-element {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background-color: #3498db;
            border-radius: 8px;
            font-weight: bold;
            min-width: 60px;
            border: 2px solid #2980b9;
        }

        .explanation {
            background-color: #fff8e1;
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
            border-left: 5px solid #ff9800;
        }

        .explanation h3 {
            color: #e65100;
            margin-bottom: 10px;
        }

        .explanation ul {
            padding-left: 20px;
            color: #444;
        }

        .explanation li {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 30px;
            color: #888;
            font-size: 14px;
            text-align: center;
        }

        .highlight {
            background-color: rgba(255, 152, 0, 0.2);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }

        .success {
            color: #27ae60;
            font-weight: bold;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vector cu Maximul pe Coloane</h1>
        
        <p class="description">
            Construiți vectorul \( X(n) \) unde \( x_i \) este <strong>cel mai mare element din coloana i</strong> a matricei \( A(m, n) \)
        </p>
        
        <div class="problem">
            <h3>Enunțul problemei:</h3>
            <p>
                Se dă matricea \( A(m, n) \) cu elemente numere întregi. Să se construiască vectorul \( X(n) \),  
                unde elementul \( x_i \) este egal cu <strong>cel mai mare element din coloana i</strong> a matricei \( A \).
            </p>
            <div class="formula">
                x<sub>i</sub> = max(A[1..m, i])
            </div>
        </div>
        
        <form method="POST" action="">
            <div class="input-section">
                <div class="input-group">
                    <h3>Dimensiuni Matrice</h3>
                    <div class="dimension-inputs">
                        <div class="dim-input">
                            <label for="m">Număr de linii (m):</label>
                            <input type="number" id="m" name="m" min="1" max="10" value="<?php echo htmlspecialchars($m); ?>" required>
                        </div>
                        <div class="dim-input">
                            <label for="n">Număr de coloane (n):</label>
                            <input type="number" id="n" name="n" min="1" max="10" value="<?php echo htmlspecialchars($n); ?>" required>
                        </div>
                    </div>
                    <button type="submit" name="genereaza" value="1" class="secondary-btn">Generează Matrice Aleatorie</button>
                </div>
                
                <div class="input-group">
                    <h3>Matricea A(m, n)</h3>
                    <div class="matrix-input">
                        <textarea id="matrice" name="matrice" placeholder="Introduceți matricea linie cu linie, elementele separate prin spațiu. Exemplu:
3 -2 5 0 8
-1 7 4 2 -3
6 0 -5 9 1
2 4 -8 3 7" rows="8" required><?php echo htmlspecialchars($matriceInput); ?></textarea>
                        <p class="hint">Introduceți exact m linii, fiecare cu n numere întregi.</p>
                    </div>
                </div>
            </div>
            
            <div class="buttons">
                <button type="submit">Calculează Vectorul X(n)</button>
                <button type="button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'" class="secondary-btn">Resetare</button>
            </div>
        </form>
        
        <div id="rezultate" class="results" style="<?php echo $rezultateStyle; ?>">
            <h2>Rezultate Calcul</h2>
            
            <div class="matrices-container">
                <div class="matrix-display">
                    <h3>Matricea A(m, n)</h3>
                    <div id="afisare-matrice" class="matrix">
                        <?php echo $afisareMatrice; ?>
                    </div>
                </div>
                
                <div class="matrix-display">
                    <h3>Vectorul X(n) - Maxime pe Coloane</h3>
                    <div id="afisare-vector" class="vector-display">
                        <?php echo $afisareVector; ?>
                    </div>
                    <div id="explicatie-vector" class="matrix">
                        <?php echo $explicatieVector; ?>
                    </div>
                </div>
            </div>
            
            <div class="explanation">
                <h3>Cum s-a calculat vectorul X(n):</h3>
                <ul>
                    <li>Pentru fiecare coloană <strong>i</strong> (de la 1 la n), se caută cel mai mare element din acea coloană</li>
                    <li>Elementul maxim din coloana <strong>i</strong> devine <strong>x<sub>i</sub></strong> în vectorul X</li>
                    <li>Maximul se determină prin parcurgerea tuturor elementelor coloanei și comparare</li>
                    <li>Rezultatul: X = [max(col1), max(col2), ..., max(coln)]</li>
                </ul>
                <div id="detalii-calcul" style="margin-top: 15px;">
                    <?php echo $detaliiCalcul; ?>
                </div>
            </div>
        </div>
        
        <div class="footer">
            Algoritm bazat pe parcurgerea matricelor bidimensionale folosind structuri repetitive
        </div>
    </div>

    <script>
        // Animație pentru card-uri (doar pentru afișare mai atractivă)
        document.addEventListener('DOMContentLoaded', function() {
            const resultCards = document.querySelectorAll('.result-card');
            resultCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s, transform 0.5s';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + index * 100);
            });
            
            // Animație pentru matrice și vector
            const matrixDisplay = document.getElementById('afisare-matrice');
            const vectorDisplay = document.getElementById('afisare-vector');
            
            if (matrixDisplay) {
                matrixDisplay.style.opacity = '0';
                matrixDisplay.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    matrixDisplay.style.transition = 'opacity 0.5s, transform 0.5s';
                    matrixDisplay.style.opacity = '1';
                    matrixDisplay.style.transform = 'scale(1)';
                }, 300);
            }
            
            if (vectorDisplay) {
                vectorDisplay.style.opacity = '0';
                vectorDisplay.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    vectorDisplay.style.transition = 'opacity 0.5s, transform 0.5s';
                    vectorDisplay.style.opacity = '1';
                    vectorDisplay.style.transform = 'translateY(0)';
                }, 500);
            }
        });
        
        // Funcție pentru generare matrice aleatorie în JavaScript (pentru feedback rapid)
        function genereazaMatriceJS() {
            const m = parseInt(document.getElementById("m").value);
            const n = parseInt(document.getElementById("n").value);
            
            if (isNaN(m) || isNaN(n) || m < 1 || n < 1 || m > 10 || n > 10) {
                alert("Dimensiunile trebuie să fie numere întregi între 1 și 10.");
                return false;
            }
            
            let matriceText = '';
            
            for (let i = 0; i < m; i++) {
                let rand = [];
                for (let j = 0; j < n; j++) {
                    const numar = Math.floor(Math.random() * 41) - 20;
                    rand.push(numar);
                }
                matriceText += rand.join(' ') + (i < m - 1 ? '\n' : '');
            }
            
            document.getElementById("matrice").value = matriceText;
            return true;
        }
    </script>
</body>
</html>