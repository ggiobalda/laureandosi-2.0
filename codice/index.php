<?php
require_once "API.php";
// controllo che la sessione sia attiva per leggere i dati
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// funzione helper per recuperare i valori di sessione in sicurezza
function get_session($key, $default = "") {
    return $_SESSION[$key] ?? $default;
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Generatore Prospetti di Laurea</title>
        <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css">
    </head>

    <body <?php echo get_session('sfondo') ?>>

        <form method="post" action="index.php">
            
            <table <?php echo get_session('test') ?>>
                <tr id="riga1">
                    <td colspan="3">
                        <b>Generatore Prospetti di Laurea <?php echo get_session('test') ?></b>
                    </td>
                </tr>
                <tr id="riga2">
                    <td>
                        <label class="info"><b>CdL:</b></label><br>
                        <select name="cdl" style="font-size: 16px;" required>
                            <option value="">Seleziona il CdL</option>
                            <?php 
                            $corsi = [
                                "T. Ing. Biomedica", "T. Ing. Elettronica", "T. Ing. Informatica", 
                                "T. Ing. delle Telecomunicazioni", "M. Ing. Biomedica, Bionics Engineering", 
                                "M. Ing. Elettronica", "M. Computer Engineering, Artificial Intelligence and Data Enginering", 
                                "M. Ing. Robotica e della Automazione", "M. Ing. delle Telecomunicazioni"
                            ];
                            foreach ($corsi as $corso): ?>
                                <option value="<?php echo $corso ?>"><?php echo $corso ?></option>
                            <?php endforeach ?>
                        </select>
                        <br><br><br>
                        <label class="info"><b>Data di laurea: </b></label><br>
                        <input type="date" name="data" required>
                    </td>

                    <td>
                        <label class="info"><b>Matricole:</b></label><br>
                        <textarea title="matricole" name="matricole" style="font-size: 16px;" placeholder="Inserisci numeri di matricola separati da uno spazio..." required></textarea>
                    </td>

                    <td>
                        <button type="submit" name="crea">Crea Prospetti</button>
                        <br><br>
                        
                        <?php if (get_session('link')): ?>
                            <a href="<?php echo get_session('link') ?>" target="blank">
                                <p>Visualizza Prospetti</p>
                            </a>
                        <?php endif; ?>
                        
                        <br>
                        <button type="submit" name="invia" <?php echo get_session('disabilitato') ?>>
                            Invia Prospetti
                        </button>

                        <p class="messaggio-stato" style="color: red;"><?php echo get_session('stato') ?></p>
                    </td>
                </tr>
            </table>

            <table style="margin-top: 20px; border: none; background-color: transparent;">
                <tr>
                    <td>
                        <?php echo get_session('testTitolo') ?>
                        <div class="messaggio-errore">
                            <?php echo get_session('statoErrori') ?>
                        </div>
                    </td>
                </tr>
            </table>
        </form>

    </body>
</html>