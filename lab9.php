<?php
$rezultat = "";
$pasi = "";
$n = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["n"])) {
        $n = intval($_POST["n"]);

        if ($n < 0) {
            $rezultat = "Introduceți un număr natural ≥ 0";
        } else {
            $k = 2 * $n;
            $factorial = 1;
            $pasi = "(2×$n)! = $k! = ";

            if ($k === 0) {
                $factorial = 1;
                $pasi .= "1";
            } else {
                for ($i = 1; $i <= $k; $i++) {
                    $factorial *= $i;
                    if ($i === 1) {
                        $pasi .= "1";
                    } else {
                        $pasi .= " × $i";
                    }
                }
                $pasi .= " = " . number_format($factorial, 0, ',', ' ');
            }

            $rezultat = number_format($factorial, 0, ',', ' ');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Calcul (2n)!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',Tahoma}
        body{
            background:linear-gradient(135deg,#2c3e50,#3498db);
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px
        }
        .container{
            background:#fff;
            border-radius:20px;
            box-shadow:0 15px 30px rgba(0,0,0,.3);
            max-width:500px;
            width:100%;
            padding:40px;
            text-align:center
        }
        h1{color:#2c3e50;margin-bottom:10px}
        .description{color:#666;margin-bottom:25px}
        .formula{
            background:#ecf0f1;
            border-left:5px solid #3498db;
            padding:15px;
            margin:20px 0;
            font-size:20px;
            font-weight:bold
        }
        label{text-align:left;display:block;margin-bottom:8px}
        input{
            width:100%;
            padding:14px;
            font-size:18px;
            border-radius:10px;
            border:2px solid #ddd
        }
        button{
            width:100%;
            margin-top:20px;
            padding:16px;
            font-size:18px;
            border:none;
            border-radius:10px;
            background:linear-gradient(to right,#3498db,#2c3e50);
            color:#fff;
            cursor:pointer
        }
        .result-container{
            margin-top:30px;
            background:#ecf0f1;
            padding:20px;
            border-radius:10px
        }
        .result-value{
            font-size:28px;
            font-weight:bold;
            word-break:break-all
        }
        .calculation-steps{
            margin-top:15px;
            background:#dfe6e9;
            padding:15px;
            border-radius:8px;
            text-align:left
        }
        .footer{margin-top:25px;color:#888}
    </style>
</head>

<body>
<div class="container">
    <h1>Calcul Factorial (2n)!</h1>

    <p class="description">
        Introduceți valoarea lui <strong>n</strong> pentru a calcula <strong>(2n)!</strong>
    </p>

    <div class="formula">(2n)! = (2 × n)!</div>

    <form method="post">
        <label>Introduceți valoarea lui n:</label>
        <input type="number" name="n" min="0" max="50" value="<?= htmlspecialchars($n) ?>" required>
        <button type="submit">Calculează (2n)!</button>
    </form>

    <?php if ($rezultat !== ""): ?>
        <div class="result-container">
            <div class="result-title">Rezultat:</div>
            <div class="result-value"><?= $rezultat ?></div>
            <div class="calculation-steps"><?= $pasi ?></div>
        </div>
    <?php endif; ?>

    <div class="footer">
        Calcul realizat folosind structuri repetitive în PHP
    </div>
</div>
</body>
</html>
