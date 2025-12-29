<?php
// Inițializare variabile
$textInput = '';
$litereApar = array_fill(0, 26, false);
$literePrezente = 0;
$litereLipsa = 0;
$listaTextLitereLipsa = [];
$previewText = '';
$rezultateStyle = "display: none;";
$alfabetLatin = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

// Procesarea datelor la submit formular
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $textInput = isset($_POST['text_input']) ? trim($_POST['text_input']) : '';
    
    // Dacă s-a apăsat "Text Exemplu"
    if (isset($_POST['exemplu'])) {
        $exempleText = [
            "În această zi minunată începem să muncim :)",
            "Quick brown foxes jump over lazy dogs. This sentence contains all letters from A to Z, making it a perfect pangram for testing fonts and keyboards.",
            "Salut! Cum te simți azi? Vremea este frumoasă și soarele strălucește puternic. Am văzut câțiva copii jucându-se în parc cu mingea lor colorată.",
            "The five boxing wizards jump quickly. This is another English pangram that uses every letter of the alphabet at least once.",
            "Programarea este arta de a comunica cu calculatorul pentru a rezolva probleme complexe prin algoritmi eficienți și structuri de date optimizate."
        ];
        
        $textInput = $exempleText[array_rand($exempleText)];
    }
    
    // Dacă avem text pentru analiză
    if (!empty($textInput)) {
        $rezultateStyle = "display: block;";
        
        // Creăm preview text (primele 300 de caractere)
        if (strlen($textInput) > 300) {
            $previewText = "<strong>Text analizat (primele 300 de caractere):</strong><br>" . htmlspecialchars(substr($textInput, 0, 300)) . "...";
        } else {
            $previewText = "<strong>Text analizat:</strong><br>" . htmlspecialchars($textInput);
        }
        
        // Analizăm literele din text
        $textUpper = strtoupper($textInput);
        
        for ($i = 0; $i < 26; $i++) {
            $litera = $alfabetLatin[$i];
            if (strpos($textUpper, $litera) !== false) {
                $litereApar[$i] = true;
            }
        }
        
        // Calculăm statistici
        $literePrezente = count(array_filter($litereApar));
        $litereLipsa = 26 - $literePrezente;
        
        // Construim lista literelor lipsă
        for ($i = 0; $i < 26; $i++) {
            if (!$litereApar[$i]) {
                $listaTextLitereLipsa[] = $alfabetLatin[$i];
            }
        }
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
    <title>Literele Alfabetului Latin Lipsă din Text</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #8e2de2 0%, #4a00e0 100%);
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
            max-width: 800px;
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
            border-left: 5px solid #8e2de2;
        }

        .problem h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .problem p {
            color: #444;
            line-height: 1.6;
        }

        .input-section {
            margin: 30px 0;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 16px;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 16px;
            line-height: 1.5;
            resize: vertical;
            transition: border-color 0.3s;
        }

        textarea:focus {
            border-color: #8e2de2;
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
            background: linear-gradient(to right, #8e2de2, #4a00e0);
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
            box-shadow: 0 7px 15px rgba(142, 45, 226, 0.4);
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

        .statistics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .statistics {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            border: 2px solid #e9ecef;
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #8e2de2;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            font-weight: 600;
        }

        .letters-container {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            border: 2px solid #e9ecef;
        }

        .letters-container h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .alphabet-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            margin-bottom: 15px;
            text-align: center;
        }

        .letters-grid {
            display: grid;
            grid-template-columns: repeat(13, 1fr);
            gap: 10px;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .letters-grid {
                grid-template-columns: repeat(7, 1fr);
            }
        }

        .letter-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            margin: 0 auto;
            transition: all 0.3s;
            opacity: 1;
            transform: scale(1);
        }

        .present {
            background-color: #d1f7c4;
            color: #27ae60;
            border: 2px solid #2ecc71;
            box-shadow: 0 4px 6px rgba(46, 204, 113, 0.2);
        }

        .absent {
            background-color: #ffd6d6;
            color: #e74c3c;
            border: 2px solid #e74c3c;
            box-shadow: 0 4px 6px rgba(231, 76, 60, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #444;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .present-legend {
            background-color: #d1f7c4;
            border: 2px solid #2ecc71;
        }

        .absent-legend {
            background-color: #ffd6d6;
            border: 2px solid #e74c3c;
        }

        .text-preview {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            max-height: 200px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 16px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 30px;
            color: #888;
            font-size: 14px;
            text-align: center;
        }

        .highlight {
            background-color: rgba(142, 45, 226, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: bold;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .empty-state h3 {
            color: #888;
            margin-bottom: 10px;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Literele Alfabetului Latin Lipsă din Text</h1>
        
        <p class="description">
            Introduceți un text și descoperiți care litere ale alfabetului latin <strong>nu apar</strong> în acel text.
        </p>
        
        <div class="problem">
            <h3>Enunțul problemei:</h3>
            <p>
                Se dă un text. Să se afișeze literele alfabetului latin care nu apar în acest text.
                Alfabetul latin conține 26 de litere: de la A la Z.
            </p>
        </div>
        
        <form method="POST" action="">
            <div class="input-section">
                <label for="text-input">Introduceți textul:</label>
                <textarea id="text-input" name="text_input" placeholder="Scrieți sau lipiți textul aici..." required><?php echo htmlspecialchars($textInput); ?></textarea>
                <p class="hint">Textul va fi analizat indiferent de majuscule/minuscule (A = a).</p>
            </div>
            
            <div class="buttons">
                <button type="submit">Analizează Textul</button>
                <button type="submit" name="exemplu" value="1" class="secondary-btn">Text Exemplu</button>
                <button type="button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF']; ?>'" class="secondary-btn">Resetare</button>
            </div>
        </form>
        
        <div id="rezultate" class="results" style="<?php echo $rezultateStyle; ?>">
            <h2>Rezultate Analiză</h2>
            
            <div class="statistics">
                <div class="stat-card">
                    <div class="stat-value">26</div>
                    <div class="stat-label">Total litere alfabet</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $literePrezente; ?></div>
                    <div class="stat-label">Litere prezente în text</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $litereLipsa; ?></div>
                    <div class="stat-label">Litere lipsă din text</div>
                </div>
            </div>
            
            <?php if (!empty($textInput)): ?>
                <div class="text-preview fade-in">
                    <?php echo $previewText; ?>
                </div>
                
                <div class="letters-container fade-in">
                    <h3>Alfabetul Latin</h3>
                    
                    <div class="alphabet-section">
                        <div class="section-title">Literele care <span class="highlight">APAR</span> în text (verde):</div>
                        <div class="letters-grid" id="litere-prezentate">
                            <?php
                            for ($i = 0; $i < 26; $i++) {
                                $litera = $alfabetLatin[$i];
                                $apare = $litereApar[$i];
                                
                                if ($apare) {
                                    echo '<div class="letter-box present" title="Litera ' . $litera . ' apare în text">' . $litera . '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="alphabet-section">
                        <div class="section-title">Literele care <span class="highlight">NU APAR</span> în text (roșu):</div>
                        <div class="letters-grid" id="litere-lipsa-grid">
                            <?php
                            for ($i = 0; $i < 26; $i++) {
                                $litera = $alfabetLatin[$i];
                                $apare = $litereApar[$i];
                                
                                if (!$apare) {
                                    echo '<div class="letter-box absent" title="Litera ' . $litera . ' nu apare în text">' . $litera . '</div>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="legend">
                        <div class="legend-item">
                            <div class="legend-color present-legend"></div>
                            <span>Literă prezentă în text</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color absent-legend"></div>
                            <span>Literă absentă din text</span>
                        </div>
                    </div>
                </div>
                
                <div class="text-preview fade-in">
                    <strong>Lista literelor lipsă:</strong><br>
                    <?php
                    if (!empty($listaTextLitereLipsa)) {
                        echo implode(', ', $listaTextLitereLipsa) . ' (' . count($listaTextLitereLipsa) . ' litere)';
                    } else {
                        echo '<span style="color: #27ae60; font-weight: bold;">Toate cele 26 de litere ale alfabetului latin apar în text!</span>';
                    }
                    ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Încă nu a fost analizat niciun text</h3>
                    <p>Introduceți un text în caseta de mai sus și apăsați "Analizează Textul" pentru a vedea rezultatele.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            Analiză text folosind metode de manipulare a șirurilor de caractere în PHP
        </div>
    </div>

    <script>
        // Animare pentru litere la încărcarea paginii
        document.addEventListener('DOMContentLoaded', function() {
            const letterBoxes = document.querySelectorAll('.letter-box');
            letterBoxes.forEach((box, index) => {
                box.style.opacity = '0';
                box.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    box.style.transition = 'opacity 0.5s, transform 0.5s';
                    box.style.opacity = '1';
                    box.style.transform = 'scale(1)';
                }, index * 30);
            });
            
            // Funcție pentru analiză în timp real (opțională)
            const textInput = document.getElementById('text-input');
            let timeoutId;
            
            if (textInput) {
                textInput.addEventListener('input', function() {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        if (this.value.trim().length > 0) {
                            // Pentru a activa această funcționalitate, ar trebui implementat AJAX
                            // sau folosit JavaScript pentru analiză client-side
                            console.log('Text modificat, ar putea face analiză în timp real...');
                        } else {
                            // Dacă textul e gol, putem ascunde rezultatele
                            document.getElementById('rezultate').style.display = 'none';
                        }
                    }, 800);
                });
            }
            
            // Funcție pentru generare text exemplu în JavaScript (opțională)
            function genereazaTextExempluJS() {
                const exempleText = [
                    "În această zi minunată începem să muncim :)",
                    "Quick brown foxes jump over lazy dogs. This sentence contains all letters from A to Z, making it a perfect pangram for testing fonts and keyboards.",
                    "Salut! Cum te simți azi? Vremea este frumoasă și soarele strălucește puternic. Am văzut câțiva copii jucându-se în parc cu mingea lor colorată.",
                    "The five boxing wizards jump quickly. This is another English pangram that uses every letter of the alphabet at least once.",
                    "Programarea este arta de a comunica cu calculatorul pentru a rezolva probleme complexe prin algoritmi eficienți și structuri de date optimizate."
                ];

                const exempluAleatoriu = exempleText[Math.floor(Math.random() * exempleText.length)];
                document.getElementById('text-input').value = exempluAleatoriu;
                
                // Dacă vrei să trimită automat formularul
                // document.querySelector('form').submit();
            }
            
            // Adăugăm funcția la butonul de Text Exemplu dacă există
            const btnExempluJS = document.querySelector('button[onclick*="genereazaTextExemplu"]');
            if (btnExempluJS) {
                btnExempluJS.onclick = genereazaTextExempluJS;
            }
        });
        
        // Funcție pentru animație de fade-in la încărcarea elementelor
        function animateElements() {
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((element, index) => {
                element.style.opacity = '0';
                setTimeout(() => {
                    element.style.transition = 'opacity 0.5s';
                    element.style.opacity = '1';
                }, index * 100);
            });
        }
        
        // Apelăm animația după încărcarea DOM-ului
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', animateElements);
        } else {
            animateElements();
        }
    </script>
</body>
</html>