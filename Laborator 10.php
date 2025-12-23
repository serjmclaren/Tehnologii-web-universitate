<?php
// Inițializare variabile
$n = 10;
$vectorStr = "2 -5 0 0 7 -3 -4 1 0 8";
$existaDouaZeroVecine = false;
$existaTreiAcelasiSemn = false;
$pozitiiZero = [];
$pozitiiSemn = [];
$detaliiA = "";
$detaliiB = "";
$vector = [];
$componenteAfisate = "";
$rezultateStyle = "display: none;";
$statistici = "";
$detaliiAnaliza = "";

// Procesarea datelor la submit formular
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = isset($_POST['n']) ? intval($_POST['n']) : 10;
    $vectorStr = isset($_POST['vector']) ? trim($_POST['vector']) : "";
    
    // Validare n
    if ($n <= 1 || $n >= 100) {
        $n = 10; // Valoare implicită în caz de eroare
    }
    
    // Dacă s-a apăsat "Generează Vector Aleatoriu"
    if (isset($_POST['genereaza'])) {
        $vector = [];
        for ($i = 0; $i < $n; $i++) {
            $random = mt_rand(0, 100) / 100;
            if ($random < 0.4) {
                $numar = mt_rand(1, 10);
            } elseif ($random < 0.8) {
                $numar = -mt_rand(1, 10);
            } else {
                $numar = 0;
            }
            $vector[] = $numar;
        }
        $vectorStr = implode(" ", $vector);
    } 
    // Dacă s-a introdus manual vectorul
    elseif (!empty($vectorStr)) {
        $componente = preg_split('/\s+/', $vectorStr);
        $vector = [];
        foreach ($componente as $comp) {
            $vector[] = intval($comp);
        }
        
        // Dacă lungimea vectorului nu corespunde cu n, ajustăm n
        if (count($vector) != $n) {
            $n = count($vector);
        }
    }
    
    // Analiza vectorului (doar dacă avem vector)
    if (!empty($vector)) {
        $rezultateStyle = "display: block;";
        
        // a) Verificare componente vecine egale cu 0
        for ($i = 0; $i < count($vector) - 1; $i++) {
            if ($vector[$i] === 0 && $vector[$i+1] === 0) {
                $existaDouaZeroVecine = true;
                $pozitiiZero[] = $i;
                $detaliiA .= "Perechea de zerouri la pozițiile " . ($i+1) . " și " . ($i+2) . "<br>";
            }
        }
        
        // b) Verificare 3 componente vecine de același semn
        for ($i = 0; $i < count($vector) - 2; $i++) {
            $semn1 = $vector[$i] <=> 0; // -1 pentru negativ, 0 pentru zero, 1 pentru pozitiv
            $semn2 = $vector[$i+1] <=> 0;
            $semn3 = $vector[$i+2] <=> 0;
            
            if ($semn1 === $semn2 && $semn2 === $semn3 && $semn1 !== 0) {
                $existaTreiAcelasiSemn = true;
                $pozitiiSemn[] = $i;
                
                $semnText = $semn1 > 0 ? "pozitive" : "negative";
                $detaliiB .= "Tripletul $semnText: [" . $vector[$i] . ", " . $vector[$i+1] . ", " . $vector[$i+2] . "] la pozițiile " . ($i+1) . ", " . ($i+2) . ", " . ($i+3) . "<br>";
            }
        }
        
        // Generare afișare vector cu culori
        foreach ($vector as $i => $val) {
            $clasa = "";
            if ($val > 0) $clasa = "positive";
            elseif ($val < 0) $clasa = "negative";
            else $clasa = "zero";
            
            $componenteAfisate .= "<span class=\"$clasa\">$val</span>";
            if ($i < count($vector) - 1) {
                $componenteAfisate .= ", ";
            }
        }
        
        // Statistici
        $pozitive = count(array_filter($vector, function($x) { return $x > 0; }));
        $negative = count(array_filter($vector, function($x) { return $x < 0; }));
        $zerouri = count(array_filter($vector, function($x) { return $x === 0; }));
        
        $statistici .= "<strong>Statistici vector:</strong><br>";
        $statistici .= "• Lungime: " . count($vector) . " elemente<br>";
        $statistici .= "• Numere pozitive: $pozitive<br>";
        $statistici .= "• Numere negative: $negative<br>";
        $statistici .= "• Zerouri: $zerouri<br><br>";
        
        $detaliiAnaliza .= "<strong>Rezumat:</strong><br>";
        $detaliiAnaliza .= "• Condiția a) (2 zerouri vecine): " . ($existaDouaZeroVecine ? 'Îndeplinită' : 'Neîndeplinită') . "<br>";
        $detaliiAnaliza .= "• Condiția b) (3 componente vecine același semn): " . ($existaTreiAcelasiSemn ? 'Îndeplinită' : 'Neîndeplinită');
        
        if ($existaDouaZeroVecine && $existaTreiAcelasiSemn) {
            $detaliiAnaliza .= "<br><br><span class=\"success\">✓ Ambele condiții sunt îndeplinite!</span>";
        } elseif ($existaDouaZeroVecine || $existaTreiAcelasiSemn) {
            $detaliiAnaliza .= "<br><br><span class=\"highlight\">✓ O condiție este îndeplinită</span>";
        } else {
            $detaliiAnaliza .= "<br><br><span class=\"failure\">✗ Nici o condiție nu este îndeplinită</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analiză Vector - Componente Vecine</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 600px;
            padding: 40px;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
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

        .requirements {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #3498db;
        }

        .requirements h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .requirements ul {
            padding-left: 20px;
            color: #444;
        }

        .requirements li {
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .input-group {
            margin: 25px 0;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        .input-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        input[type="number"] {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="number"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .small-input {
            width: 80px;
            flex: none;
        }

        button, .btn {
            background: linear-gradient(to right, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 16px 30px;
            font-size: 17px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        button:hover, .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(52, 152, 219, 0.4);
        }

        button:active, .btn:active {
            transform: translateY(0);
        }

        .secondary-btn {
            background: linear-gradient(to right, #95a5a6, #7f8c8d);
            margin-top: 5px;
        }

        .results {
            margin-top: 30px;
        }

        .result-card {
            background-color: #ecf0f1;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #3498db;
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.5s, transform 0.5s;
        }

        .result-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .result-content {
            font-size: 16px;
            color: #2d3436;
            line-height: 1.5;
        }

        .vector-display {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            margin: 15px 0;
            overflow-x: auto;
        }

        .positive {
            color: #2ecc71;
            font-weight: bold;
        }

        .negative {
            color: #e74c3c;
            font-weight: bold;
        }

        .zero {
            color: #f39c12;
            font-weight: bold;
        }

        .highlight {
            background-color: rgba(52, 152, 219, 0.2);
            padding: 2px 5px;
            border-radius: 4px;
            font-weight: bold;
        }

        .success {
            color: #27ae60;
        }

        .failure {
            color: #e74c3c;
        }

        .footer {
            margin-top: 25px;
            color: #888;
            font-size: 14px;
            text-align: center;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }
            
            h1 {
                font-size: 24px;
            }
            
            .input-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .small-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Analiză Vector - Componente Vecine</h1>
        
        <p class="description">
            Introduceti un vector cu n componente întregi (1 < n < 100)<br>
            și verificați dacă vectorul îndeplinește condițiile specificate.
        </p>
        
        <div class="requirements">
            <h3>Cerințe de verificat:</h3>
            <ul>
                <li><strong>a)</strong> Vectorul conține <strong>2 componente vecine egale cu 0</strong></li>
                <li><strong>b)</strong> Vectorul conține <strong>3 componente vecine de același semn</strong></li>
            </ul>
        </div>
        
        <form method="POST" action="">
            <div class="input-group">
                <label for="n">Numărul de componente (n):</label>
                <div class="input-row">
                    <input type="number" id="n" name="n" min="2" max="99" value="<?php echo htmlspecialchars($n); ?>" placeholder="Ex: 10" class="small-input" required>
                    <button type="submit" name="genereaza" value="1" class="secondary-btn">Generează Vector Aleatoriu</button>
                </div>
            </div>
            
            <div class="input-group">
                <label for="vector">Introduceți componentele vectorului (separate prin spațiu):</label>
                <input type="text" id="vector" name="vector" placeholder="Ex: 2 -5 0 0 7 -3 -4 1 0 8" value="<?php echo htmlspecialchars($vectorStr); ?>" required>
            </div>
            
            <button type="submit">Analizează Vectorul</button>
        </form>
        
        <div id="rezultate" class="results" style="<?php echo $rezultateStyle; ?>">
            <div class="result-card">
                <h3>Vectorul introdus:</h3>
                <div id="afisare-vector" class="vector-display">
                    v = [<?php echo $componenteAfisate; ?>]
                </div>
            </div>
            
            <div class="result-card">
                <h3>a) Verificare componente vecine egale cu 0:</h3>
                <div id="rezultat-a" class="result-content">
                    <?php if (!empty($vector)): ?>
                        <?php if ($existaDouaZeroVecine): ?>
                            <span class="success">✓ DA</span> - Vectorul conține <?php echo count($pozitiiZero); ?> perechi de componente vecine egale cu 0.
                            <br><small><?php echo $detaliiA; ?></small>
                        <?php else: ?>
                            <span class="failure">✗ NU</span> - Vectorul NU conține două componente vecine egale cu 0.
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="result-card">
                <h3>b) Verificare 3 componente vecine de același semn:</h3>
                <div id="rezultat-b" class="result-content">
                    <?php if (!empty($vector)): ?>
                        <?php if ($existaTreiAcelasiSemn): ?>
                            <span class="success">✓ DA</span> - Vectorul conține <?php echo count($pozitiiSemn); ?> triplete de componente vecine de același semn.
                            <br><small><?php echo $detaliiB; ?></small>
                        <?php else: ?>
                            <span class="failure">✗ NU</span> - Vectorul NU conține trei componente vecine de același semn.
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="result-card">
                <h3>Detalii analiză:</h3>
                <div id="detalii-analiza" class="result-content">
                    <?php 
                    if (!empty($vector)) {
                        echo $statistici;
                        echo $detaliiAnaliza;
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="footer">
            Analiză realizată folosind structuri repetitive și condiționale în PHP
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
        });
    </script>
</body>
</html>